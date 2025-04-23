<?php

namespace App\Modules\Grades\Controllers;

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

class GradesReportsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $data = User::query()->with(['roles']);
            if ($request->ajax()) {
                return YajraDataTables::eloquent($data)
                    ->addIndexColumn()
                    ->addColumn('roles', function ($item) {
                        return Str::limit(implode(', ', $item->roles->pluck('name')->toArray()), 20);
                    })
                    ->editColumn('created_at', function ($item) {
                        return TimeHelper::time_elapsed_string($item->created_at);
                    })
                    ->editColumn('updated_at', function ($item) {
                        return TimeHelper::time_elapsed_string($item->updated_at);
                    })
                    ->addColumn('action', function ($row) {
                        $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editProduct">Edit</a>';
                        $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteProduct">Delete</a>';
                        return $btn;
                    })
                    ->rawColumns(['actions'])
                    ->make(true);
            }
            return view("Grades::indexx", compact('data'));
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }

    }

    public function getResults(Request $request)
    {
        if ($request->ajax()) {
            $queryBuilderJSON = $request->json()->all();
            //$contaRequest =  count($request['rules']) - 1;
            $convertJSON = json_encode($queryBuilderJSON, true);
           // return $request;

            $sub = DB::table('users')
            ->Join('grades', 'users.id', '=', 'grades.student_id')

            ->Join('user_courses', 'users.id', '=', 'user_courses.users_id')
            ->Join('courses', 'user_courses.courses_id', '=', 'courses.id')

            ->leftjoin('matriculations as mat', 'users.id', '=', 'mat.user_id')
            ->leftjoin('matriculation_classes', 'matriculation_classes.matriculation_id', '=', 'mat.id')
            ->leftjoin('classes as c2', 'matriculation_classes.class_id', '=', 'c2.id') 

            ->leftJoin('user_candidate', 'users.id', '=', 'user_candidate.user_id')
            ->leftjoin('user_classes', 'user_classes.user_id', '=', 'user_candidate.user_id')
            ->leftjoin('classes as c1', 'user_classes.class_id', '=', 'c1.id')

            ->Join('disciplines', 'grades.discipline_id', '=', 'disciplines.id')
            ->Join('disciplines_translations', function ($join) {
                   $join->on('disciplines.id', '=', 'disciplines_translations.discipline_id')
                   ->where('disciplines_translations.version', 1);
                    //->distinct();
               })

            ->leftJoin('users as u1', 'u1.id', '=', 'grades.created_by')
            ->leftJoin('users as u2', 'u2.id', '=', 'grades.updated_by')

            ->Join('user_parameters', function ($join) {
                   $join->on('users.id', '=', 'user_parameters.users_id')
                   //->where('user_parameters.parameters_id','=', '19');
                   ->where('user_parameters.parameters_id', 19);
               })

            ->Join('user_parameters as u_p', function ($join) {
                   $join->on('users.id', '=', 'u_p.users_id')
                   //->where('user_parameters.parameters_id','=', '19');
                   ->where('u_p.parameters_id', 1);
                   })

            ->select(
             
                //'users.name as name',
                'courses.id as courses_id',
                'grades.value as value',
                'grades.student_id as student_id',
                'u1.name as created_by', 
                'u2.name as updated_by', 
                'grades.created_at as created_at',
                'grades.updated_at as updated_at',
                'users.email as email',
                'c2.display_name as turma_matriculado',
                //'c2.code as code',
                'c1.display_name as turma_candidato',
                'user_classes.class_id as class_id',

                'disciplines.id as discipline_id',
                'disciplines_translations.display_name as disciplina',
                //'disciplines_translations.discipline_id as discipline_id',
                'mat.course_year as ano',
                'user_parameters.value as param_mecanografico',
                'u_p.value as name',
                'c2.display_name as display_name',
                'disciplines.code as code' 

            )
            ->distinct();
            //->get();
            //return $sub; 

        $table = DB::table(DB::raw("({$sub->toSql()}) as sub"));
        $qbp = new QueryBuilderParser(
                        array('courses.id', 'c2.display_name', 'user_classes.class_id', 
                              'disciplines.id', 
                              //'value', 
                              'user_parameters.value', 
                              'users.email',
                              'disciplines.code'
                              )
                    );

    $convertJSON = str_replace("discipline_id", "disciplines.id", $convertJSON);
    $convertJSON = str_replace("courses_id", "courses.id", $convertJSON);
    $convertJSON = str_replace("class_id", "user_classes.class_id", $convertJSON);
    $convertJSON = str_replace("display_name", "c2.display_name", $convertJSON);
    $convertJSON = str_replace("param_mecanografico", "user_parameters.value", $convertJSON);
    $convertJSON = str_replace("email", "users.email", $convertJSON);
    //$convertJSON = str_replace("value", "grades.value", $convertJSON);
    $convertJSON = str_replace("code", "disciplines.code", $convertJSON);

    $query = $qbp->parse($convertJSON, $sub);
    $rows = $query->get();
    //return $rows;

    //return Response::JSON($rows);
    $view = view("Grades::gradesgetReports", compact('rows'))->render();
    return response()->json(['html' => $view]);
                
    //return Response::JSON($rows);
    //$view = view("Payments::paymentsgetReports", compact('rows'))->render();
    //return response()->json(['html' => $view]);
        }
    }
}