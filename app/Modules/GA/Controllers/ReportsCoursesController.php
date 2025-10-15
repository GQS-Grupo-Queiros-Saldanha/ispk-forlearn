<?php

namespace App\Modules\GA\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Users\Models\User;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use LynX39\LaraPdfMerger\Facades\PdfMerger;
use Throwable;
use timgws\QueryBuilderParser;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Facades\DataTables as YajraDataTables;
use App\Modules\GA\Models\Course;

class ReportsCoursesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            $model = Course::join('users as u1', 'u1.id', '=', 'courses.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'courses.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'courses.deleted_by')
                ->leftJoin('duration_type_translations as dtt', function ($join) {
                    $join->on('dtt.duration_types_id', '=', 'courses.duration_types_id');
                    $join->on('dtt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dtt.active', '=', DB::raw(true));
                })
                ->leftJoin('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'courses.id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })
                ->select([
                    'courses.*',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    'ct.display_name',
                    'ct.abbreviation',
                    DB::raw('CONCAT(courses.duration_value, " ", dtt.display_name) as duration')
                ]);
                if($request->ajax())
                {
                  return YajraDataTables::eloquent($model)
                  ->addIndexColumn()
                  ->make(true);
                }
            return view("GA::courses.report_index", compact('model'));

        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function getCoursesResults(Request $request)
    {
       if ($request->ajax()) {
            $queryBuilderJSON = $request->json()->all();
            $contaRequest =  count($request['rules']) - 1;
 
            $convertJSON = json_encode($queryBuilderJSON, true);

            $sub = DB::table('courses')


                  ->join('courses as c1', function($join){
                      $join->on('c1.id', '=', 'courses.id')
                      ->where('c1.deleted_at', null);
                  })

                ->join('users as u1', 'u1.id', '=', 'courses.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'courses.updated_by')

                ->join('duration_types', 'courses.duration_types_id','=','duration_types.id')
                ->join('course_cycles', 'courses.course_cycles_id', '=', 'course_cycles.id')
                ->join('degrees', 'courses.degrees_id','=', 'degrees.id')
                ->join('departments', 'courses.departments_id', '=', 'departments.id')
                  ->leftJoin('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'courses.id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })
                  ->leftJoin('duration_type_translations as dtt', function ($join) {
                    $join->on('dtt.duration_types_id', '=', 'courses.duration_types_id');
                    $join->on('dtt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dtt.active', '=', DB::raw(true));
                })
                   
                  ->leftJoin('degree_translations as dt', function ($join) {
                    $join->on('dt.degrees_id', '=', 'courses.degrees_id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                })

                   ->leftJoin('department_translations as dpt', function ($join) {
                    $join->on('dpt.departments_id', '=', 'courses.departments_id');
                    $join->on('dpt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dpt.active', '=', DB::raw(true));
                })
                  
                    ->leftJoin('course_cycle_translations as cct', function ($join) {
                    $join->on('cct.course_cycles_id', '=', 'courses.course_cycles_id');
                    $join->on('cct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('cct.active', '=', DB::raw(true));
                })



                ->select(
                  'duration_types.id as duration_type',
                  'c1.id as courses_id',
                  'ct.display_name as course_name',
                  'courses.duration_value as duration_value',
                  'courses.course_cycles_id as course_cycles',
                  'courses.degrees_id as degrees',
                  'courses.departments_id as departments',
                  'dtt.display_name as duration_type_name',
                  'dt.display_name as degree_name',
                  'dpt.display_name as departments_name',
                  'cct.display_name as course_cycles_name',
                  'u1.name as created_by',
                  'u2.name as updated_by'
                )->distinct();


            $table = DB::table(DB::raw("({$sub->toSql()}) as sub"));

            $qbp = new QueryBuilderParser(
                array(
                  'duration_type','duration_value','course_cycles', 'degrees', 'departments'
                )
            );

            $query = $qbp->parse($convertJSON, $table);

            $rows = $query->get();

            //return $rows;
            $view = view("GA::courses.getCoursesReports", compact('rows'))->render();
            return response()->json(['html' => $view]);
        }
    }
}




