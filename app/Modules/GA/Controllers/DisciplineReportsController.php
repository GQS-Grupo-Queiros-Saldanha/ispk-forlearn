<?php

namespace App\Modules\GA\Controllers;
use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Users\Models\User;
use App\Modules\GA\Models\Discipline;
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

class DisciplineReportsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {


            $model = Discipline::join('users as u1', 'u1.id', '=', 'disciplines.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'disciplines.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'disciplines.deleted_by')
                ->leftJoin('discipline_has_areas as dha', 'disciplines.id', '=', 'dha.discipline_id')
                ->leftJoin('discipline_areas_translations as dat', function ($join) {
                    $join->on('dat.discipline_areas_id', '=', 'dha.discipline_area_id');
                    $join->on('dat.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dat.active', '=', DB::raw(true));
                })
                ->leftJoin('discipline_profiles as dp', 'dp.id', '=', 'disciplines.discipline_profiles_id')
                ->leftJoin('discipline_profile_translations as dpt', function ($join) {
                    $join->on('dpt.discipline_profiles_id', '=', 'dp.id');
                    $join->on('dpt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dpt.active', '=', DB::raw(true));
                })
                ->leftJoin('disciplines_translations as dt', function ($join) {
                    $join->on('dt.discipline_id', '=', 'disciplines.id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                })
                ->leftJoin('courses', 'courses.id', '=', 'disciplines.courses_id')
                ->leftJoin('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'courses.id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })
                ->select([
                    'disciplines.id as discipline_id',
                    'disciplines.code as discipline_code',
                    'disciplines.created_at',
                    'disciplines.updated_at',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    'dt.display_name as discipline_name',
                    DB::raw('GROUP_CONCAT(dat.display_name) as areas'),
                    'dpt.display_name as profile',
                    'dat.display_name as discipline_area',
                    'ct.display_name as course_name',
                    'dt.abbreviation as discipline_abbreviation'
                ])
                ->groupBy([
                    'disciplines.id',
                ])->get();

            return view('GA::index', compact('model'));

            
           /* $disciplines = DB::table('disciplines')
            ->join('disciplines_translations', 'disciplines.id', '=', 'disciplines_translations.discipline_id')
            ->join('discipline_has_areas', 'disciplines.id', '=', 'discipline_has_areas.discipline_id')
            ->join('courses', 'disciplines.courses_id', '=', 'courses.id')
            ->join('courses_translations', 'courses_translations.courses_id', '=', 'courses.id')
            //->join('discipline_profile_translations', 'discipline_profiles.','=')

            ->select(
                'disciplines.*',
                'disciplines.code as code', 
                'disciplines_translations.display_name as display_name', 
                'disciplines_translations.description as description', 
                'disciplines_translations.abbreviation as abbreviation',
                'disciplines.courses_id as courses_id', 
                'courses_translations.display_name as course_name', 
                'disciplines.discipline_profiles_id as discipline_profiles_id', 
                //'discipline_profile_translations.display_name as display_name',
                'discipline_has_areas.discipline_area_id as discipline_area_id'
                //,'discipline_areas_translations.display_name as display_name')

            )->where('disciplines_translations.active', 1)->get();

            return view('GA::index', compact('disciplines'));*/
    }



    public function getResults(Request $request)
    {
        if ($request->ajax()) {
            $queryBuilderJSON = $request->json()->all();

            $convertJSON = json_encode($queryBuilderJSON, true);
           // return $request;

            $sub = DB::table('disciplines')
            ->join('disciplines_translations', function($join){
                $join->on('disciplines.id', '=', 'disciplines_translations.discipline_id')
                ->where('disciplines.deleted_at', null)
                ->where('disciplines_translations.active', 1);
            })


            //, 'disciplines.id', '=', 'disciplines_translations.discipline_id'
            ->join('discipline_has_areas', 'disciplines.id', '=', 'discipline_has_areas.discipline_id')
            ->join('courses', 'disciplines.courses_id', '=', 'courses.id')
            ->join('users', 'disciplines.created_by', '=', 'users.id')

            ->select(
                'disciplines.code as code', 
                'disciplines_translations.display_name as display_name', 
                'disciplines_translations.description as description', 
                'disciplines_translations.abbreviation as abbreviation',
                'disciplines.courses_id as courses_id', 
                //'courses_translations.display_name as display_name', 
                'disciplines.discipline_profiles_id as discipline_profiles_id', 
                //'discipline_profile_translations.display_name as display_name',
                'discipline_has_areas.discipline_area_id as discipline_area_id',
                //,'discipline_areas_translations.display_name as display_name'
                'disciplines.id as discipline_id',
                'users.name as created_by',
                'users.name as updated_by',
                'disciplines.created_at as created_at',
                'disciplines.updated_at as updated_at'
            )

            ->distinct();

            $table = DB::table(DB::raw("({$sub->toSql()}) as sub"));
            $qbp = new QueryBuilderParser(
       
        array( 'display_name', 'disciplines.code', 'abbreviation', 'courses_id', 'discipline_profiles_id', 'discipline_area_id', 'created_at')
    );

    $convertJSON = str_replace("code", "disciplines.code", $convertJSON);

    $query = $qbp->parse($convertJSON, $sub);
    $rows = $query->get();
    //return $rows;
    //return Response::JSON($rows);
    $view = view("GA::disciplinegetReports", compact('rows'))->render();
    return response()->json(['html' => $view]);
        }
    }
}