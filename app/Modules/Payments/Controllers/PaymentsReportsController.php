<?php

namespace App\Modules\Payments\Controllers;

use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Users\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;
use timgws\QueryBuilderParser;
use Yajra\DataTables\Facades\DataTables as YajraDataTables;

class PaymentsReportsController extends Controller
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
            return view("Payments::index", compact('data'));
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }

    }

    public function getResults(Request $request)
    {
        if ($request->ajax()) {
            $queryBuilderJSON = $request->json()->all();
            $contaRequest =  count($request['rules']) - 1;
            $convertJSON = json_encode($queryBuilderJSON, true);
           // return $request;


            for ($i=0; $i <= $contaRequest ; $i++) {

                if ($request['rules'][$i]['field'] == 'status' && $request['rules'][$i]['value'] == 'total') {
                   $sub = DB::table('users')
            ->leftJoin('article_requests', 'users.id', '=', 'article_requests.user_id')
            //->leftJoin('transactions', 'article_requests.id', '=', 'transactions.article_request_id')
            ->leftJoin('article_translations', 'article_requests.article_id', '=', 'article_translations.article_id')
            ->leftJoin('user_candidate', 'users.id', '=', 'user_candidate.user_id')
            ->leftJoin('articles as artc', 'article_requests.article_id', '=', 'artc.id')

            ->leftjoin('matriculations as mat', 'mat.user_id', '=', 'users.id')

            ->leftJoin('transaction_article_requests', function ($join) {
                    $join->on('article_requests.id', '=', 'transaction_article_requests.article_request_id')
                    ->where('article_requests.status','=', 'total');
                    //->distinct();
                })

            ->Join('transactions as tr', function ($join) {
                    $join->on('transaction_article_requests.transaction_id', '=', 'tr.id')
                    ->where('tr.type','=', 'payment');
                    //->distinct();
                })

            ->leftJoin('users as u1', 'u1.id', '=', 'tr.created_by')
            ->leftJoin('users as u2', 'u2.id', '=', 'tr.updated_by')

            ->leftJoin('user_courses', 'users.id', '=', 'user_courses.users_id')
            ->leftJoin('courses', 'user_courses.courses_id', '=', 'courses.id')

            ->leftjoin('user_classes', 'user_classes.user_id', '=', 'user_candidate.user_id')
            ->leftjoin('classes as c1', 'user_classes.class_id', '=', 'c1.id')

            ->leftjoin('matriculation_classes', 'matriculation_classes.matriculation_id', '=', 'mat.id')
            ->leftjoin('classes as c2', 'matriculation_classes.class_id', '=', 'c2.id')

            ->leftjoin('transaction_info', 'tr.id', '=', 'transaction_info.transaction_id')
            ->leftjoin('banks', 'transaction_info.bank_id', '=', 'banks.id')



        /*    ->leftjoin('user_parameters', 'users.id', '=', 'user_parameters.users_id')
            ->leftJoin('parameters', function ($join) {
                   $join->on('user_parameters.parameters_id', '=', 'parameters.id')
                   ->where('user_parameters.parameters_id', 19);
                   //->distinct('matriculation_classes.matriculation_id');
                   })
                */
            //->join('transaction_receipts', 'transactions.id', '=', 'transaction_receipts.transaction_id')
            ->select(

               // 'user_parameters.parameters_id as value_mecanografico',
             
                //'transaction_receipts.code as code',
                'tr.id as id_transaction',
                'article_requests.id as article_requests_id',
                'users.id as user_id', 'user_candidate.code as code_candidate',
                'mat.code as code_matricula', 'users.name as name',
                'users.email as email',
                'article_translations.display_name as display_name',
                'article_requests.status as status',
                'tr.type as type',
                'tr.value as value',
                'u1.name as created_by', 'tr.created_at as created_at',
                'u2.name as updated_by', 'tr.updated_at as updated_at',
                'courses.id as courses_id',
                'article_requests.month as month'
                //'artc.id as article_id',

                ,'c1.display_name as turma_candidato'
                ,'user_classes.class_id as class_id'
                ,'c2.display_name as turma_matriculado'
                ,'c2.code as code'

                ,'tr.notes as nota'
                ,'banks.display_name as banco'
                ,'transaction_info.reference as reference'

            )
            //->where('transactions.type','=', 'payment');
            ->distinct();
            //->get();
            //return $sub;

            $table = DB::table(DB::raw("({$sub->toSql()}) as sub"));
           $qbp = new QueryBuilderParser(
                        array('article_translations.display_name', 'status', 'month', 'courses.id', 'user_classes.class_id', 'c2.code')
                    );

    $convertJSON = str_replace("display_name", "article_translations.display_name", $convertJSON);
    $convertJSON = str_replace("courses_id", "courses.id", $convertJSON);
    $convertJSON = str_replace("class_id", "user_classes.class_id", $convertJSON);
    $convertJSON = str_replace("code", "c2.code", $convertJSON);

    $query = $qbp->parse($convertJSON, $sub);
    $rows = $query->get();
    //return $rows;

    //return Response::JSON($rows);
    $view = view("Payments::paymentsgetReports", compact('rows'))->render();
    return response()->json(['html' => $view]);

                }

            elseif ($request['rules'][$i]['field'] == 'status' && $request['rules'][$i]['value'] == 'pending') {
                   $sub = DB::table('users')
            ->leftJoin('article_requests', 'users.id', '=', 'article_requests.user_id')
            //->leftJoin('transactions', 'article_requests.id', '=', 'transactions.article_request_id')
            ->leftJoin('article_translations', 'article_requests.article_id', '=', 'article_translations.article_id')
            ->leftJoin('user_candidate', 'users.id', '=', 'user_candidate.user_id')
            ->leftJoin('articles as artc', 'article_requests.article_id', '=', 'artc.id')

            ->leftjoin('matriculations as mat', 'mat.user_id', '=', 'users.id')

            ->leftJoin('transaction_article_requests', function ($join) {
                    $join->on('article_requests.id', '=', 'transaction_article_requests.article_request_id')
                    ->where('article_requests.status','=', 'pending');
                    //->distinct();
                })

            ->Join('transactions as tr', function ($join) {
                    $join->on('transaction_article_requests.transaction_id', '=', 'tr.id')
                    ->where('tr.type','=', 'debit');
                    //->distinct();
                })

            //->leftJoin('transactions as tr', function ($join) {
            //        $join->on('article_requests.id', '=', 'tr.article_request_id')
            //        ->where('tr.type','=', 'debit');
                    //->distinct();
            //    })

            ->leftJoin('users as u1', 'u1.id', '=', 'tr.created_by')
            ->leftJoin('users as u2', 'u2.id', '=', 'tr.updated_by')

            ->leftJoin('user_courses', 'users.id', '=', 'user_courses.users_id')
            ->leftJoin('courses', 'user_courses.courses_id', '=', 'courses.id')


            ->leftjoin('user_classes', 'user_classes.user_id', '=', 'user_candidate.user_id')
            ->leftjoin('classes as c1', 'user_classes.class_id', '=', 'c1.id')

            ->leftjoin('matriculation_classes', 'matriculation_classes.matriculation_id', '=', 'mat.id')
            ->leftjoin('classes as c2', 'matriculation_classes.class_id', '=', 'c2.id')

            ->leftjoin('transaction_info', 'tr.id', '=', 'transaction_info.transaction_id')
            ->leftjoin('banks', 'transaction_info.bank_id', '=', 'banks.id')
        

            //->join('transaction_receipts', 'transactions.id', '=', 'transaction_receipts.transaction_id')
            ->select(

                //'transaction_receipts.code as code',
                'tr.id as id_transaction',
                'article_requests.id as article_requests_id',
                'users.id as user_id', 'user_candidate.code as code_candidate',
                'mat.code as code_matricula', 'users.name as name',
                'users.email as email',
                'article_translations.display_name as display_name',
                'article_requests.status as status',
                'tr.type as type',
                'tr.value as value',
                'u1.name as created_by', 'tr.created_at as created_at',
                'u2.name as updated_by', 'tr.updated_at as updated_at',
                'courses.id as courses_id',
                'article_requests.month as month'
                //'artc.id as article_id',

                ,'c1.display_name as turma_candidato'
                ,'user_classes.class_id as class_id'
                ,'c2.display_name as turma_matriculado'
                ,'c2.code as code'

                ,'tr.notes as nota'
                ,'banks.display_name as banco'
                ,'transaction_info.reference as reference'

            )
            //->where('transactions.type','=', 'payment');
            ->distinct();
            //->get();
            //return $sub;

       $table = DB::table(DB::raw("({$sub->toSql()}) as sub"));
           $qbp = new QueryBuilderParser(
                        array('article_translations.display_name', 'status', 'month', 'courses.id', 'user_classes.class_id', 'c2.code')
                    );

    $convertJSON = str_replace("display_name", "article_translations.display_name", $convertJSON);
    $convertJSON = str_replace("courses_id", "courses.id", $convertJSON);
    $convertJSON = str_replace("class_id", "user_classes.class_id", $convertJSON);
    $convertJSON = str_replace("code", "c2.code", $convertJSON);

    $query = $qbp->parse($convertJSON, $sub);
    $rows = $query->get();
    //return $rows;

    //return Response::JSON($rows);
    $view = view("Payments::paymentsgetReports", compact('rows'))->render();
    return response()->json(['html' => $view]);

                }

            elseif ($request['rules'][$i]['field'] == 'status' && $request['rules'][$i]['value'] == 'partial') {
                    $sub = DB::table('users')
            ->leftJoin('article_requests', 'users.id', '=', 'article_requests.user_id')
            //->leftJoin('transactions', 'article_requests.id', '=', 'transactions.article_request_id')
            ->leftJoin('article_translations', 'article_requests.article_id', '=', 'article_translations.article_id')
            ->leftJoin('user_candidate', 'users.id', '=', 'user_candidate.user_id')
            ->leftJoin('articles as artc', 'article_requests.article_id', '=', 'artc.id')

            ->leftjoin('matriculations as mat', 'mat.user_id', '=', 'users.id')

            ->leftJoin('transaction_article_requests', function ($join) {
                    $join->on('article_requests.id', '=', 'transaction_article_requests.article_request_id')
                    ->where('article_requests.status','=', 'partial');
                    //->distinct();
                })

            ->Join('transactions as tr', function ($join) {
                    $join->on('transaction_article_requests.transaction_id', '=', 'tr.id')
                    ->where('tr.type','=', 'payment');
                    //->distinct();
                })

           // ->leftJoin('transactions as tr', function ($join) {
           //         $join->on('article_requests.id', '=', 'tr.article_request_id')
           //         ->where('tr.type','=', 'payment');
                    //->where('tr.type','<>', 'payment');
                    //->distinct();
           //     })

            ->leftJoin('users as u1', 'u1.id', '=', 'tr.created_by')
            ->leftJoin('users as u2', 'u2.id', '=', 'tr.updated_by')

            ->leftJoin('user_courses', 'users.id', '=', 'user_courses.users_id')
            ->leftJoin('courses', 'user_courses.courses_id', '=', 'courses.id')


            ->leftjoin('user_classes', 'user_classes.user_id', '=', 'user_candidate.user_id')
            ->leftjoin('classes as c1', 'user_classes.class_id', '=', 'c1.id')

            ->leftjoin('matriculation_classes', 'matriculation_classes.matriculation_id', '=', 'mat.id')
            ->leftjoin('classes as c2', 'matriculation_classes.class_id', '=', 'c2.id')

            ->leftjoin('transaction_info', 'tr.id', '=', 'transaction_info.transaction_id')
            ->leftjoin('banks', 'transaction_info.bank_id', '=', 'banks.id')
        

            //->join('transaction_receipts', 'transactions.id', '=', 'transaction_receipts.transaction_id')
            ->select(

                //'transaction_receipts.code as code',
                'tr.id as id_transaction',
                'article_requests.id as article_requests_id',
                'users.id as user_id', 'user_candidate.code as code_candidate',
                'mat.code as code_matricula', 'users.name as name',
                'users.email as email',
                'article_translations.display_name as display_name',
                'article_requests.status as status',
                'tr.type as type',
                'tr.value as value',
                'u1.name as created_by', 'tr.created_at as created_at',
                'u2.name as updated_by', 'tr.updated_at as updated_at',
                'courses.id as courses_id',
                'article_requests.month as month'
                //'artc.id as article_id',

                ,'c1.display_name as turma_candidato'
                ,'user_classes.class_id as class_id'
                ,'c2.display_name as turma_matriculado'
                ,'c2.code as code'

                ,'tr.notes as nota'
                ,'banks.display_name as banco'
                ,'transaction_info.reference as reference'

            )
            ->where('status','=', 'partial')
            ->distinct();
            //->get();
            //return $sub;

             $table = DB::table(DB::raw("({$sub->toSql()}) as sub"));
           $qbp = new QueryBuilderParser(
                        array('article_translations.display_name', 'status', 'month', 'courses.id', 'user_classes.class_id', 'c2.code')
                    );

    $convertJSON = str_replace("display_name", "article_translations.display_name", $convertJSON);
    $convertJSON = str_replace("courses_id", "courses.id", $convertJSON);
    $convertJSON = str_replace("class_id", "user_classes.class_id", $convertJSON);
    $convertJSON = str_replace("code", "c2.code", $convertJSON);

    $query = $qbp->parse($convertJSON, $sub);
    $rows = $query->get();
    //return $rows;

    //return Response::JSON($rows);
    $view = view("Payments::paymentsgetReports", compact('rows'))->render();
    return response()->json(['html' => $view]);

                }else{

                }
            }



    //return Response::JSON($rows);
    //$view = view("Payments::paymentsgetReports", compact('rows'))->render();
    //return response()->json(['html' => $view]);
        }
    }
}
