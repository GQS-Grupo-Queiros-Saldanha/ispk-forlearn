<?php

namespace App\Modules\Reports\Controllers;

use App\Exports\IncomeExport;
use App\Exports\Detalhada;
use App\Exports\PendingExport;
use App\Helpers\LanguageHelper;
use App\Modules\GA\Models\LectiveYear;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Modules\GA\Models\Course;
use App\Modules\Payments\Models\Article;
use App\Modules\Payments\Models\ArticleRequest;
use App\Modules\Payments\Models\Transaction;
use App\Modules\Users\Models\User;
//use Barryvdh\DomPDF\PDF;
use App\Modules\Users\Models\Matriculation;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Log;
use Illuminate\Support\Facades\DB;
use App\Model\Institution;
use Toastr;
use Auth;
use Illuminate\Http\Request as HttpRequest;
use App\Modules\Payments\Util\ArticlesUtil;
use App\Modules\Reports\Util\DocsReportsUtil;

class DocsReportsController extends Controller
{


    public function generateEnrollmentIncame()
    {

        //   $students = User::whereHas('roles', function ($q) {
        //     $q->whereIn('id', [6,15]);
        //  })
        //     //->whereHas('matriculation')

        //     ->with(['parameters' => function ($q) {
        //         $q->whereIn('code', ['nome', 'n_mecanografico']);
        //     }])
        //     ->leftJoin('user_parameters as u_p', function ($join) {
        //             $join->on('users.id', '=', 'u_p.users_id')
        //                  ->where('u_p.parameters_id', 1);
        //         })
        //     ->leftJoin('user_parameters as u_p0', function ($join) {
        //         $join->on('users.id', '=', 'u_p0.users_id')
        //              ->where('u_p0.parameters_id', 19);
        //     })
        //      ->join('article_requests','article_requests.user_id','=','users.id')
        //      ->where('article_requests.status', "total")
        //      ->orWhere('article_requests.status', "partial")
        //      ->select([\DB::raw('DISTINCT(article_requests.user_id),
        //               u_p.value as display_name, users.email as email, u_p0.value as meca, users.id as id ')])
        //     ->get();


        // Article::with('currentTranslation')->get();
        $lectiveYears = LectiveYear::with(['currentTranslation'])
            ->get();

        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
            ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
            ->first();


        $articles = DB::table('articles as articl')->join('article_translations as art_trans', 'art_trans.article_id', '=', 'articl.id')
            ->select([
                'articl.id as id',
                'articl.code as code',
                'art_trans.display_name as display_name'
            ])
            ->whereNull('articl.deleted_at')
            ->where('art_trans.active', '=', 1)
            ->orderByRaw('articl.deleted_at DESC')
            ->distinct()
            ->whereBetween('articl.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
            ->get();
        $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
        $courses = Course::with('currentTranslation')->get();
        $data = ['articles' => $articles, 'courses' => $courses, 'lectiveYears' => $lectiveYears, 'lectiveYearSelected' => $lectiveYearSelected];


        return view("Reports::generate-enrollment-income")->with($data);
    }










    public function enrollmentIncomeWithParameters(Request $request)
    {

        

        //Avaliar datas vazias
        //    return $request;
        $valormatriculaDupl = [];
        $antransoLectivoArticl = [];
        $matricula = 0;
        $transacao = 0;
        $qtdRegisto = 0;
        $objetoValorTransacao = [];
        $objetoSaldo_cartera = [];
        $valorRepetido = [];

        switch ($request->submitButton) {
            case 'pdf':
                $date1 =  $request->get('data1') == null ? date('Y-m-d') : $request->get('data1');
                $date2 =  $request->get('data2');

                $article = $request->get('article');
                $course = $request->get('curso');
                $student = $request->get('student');

                $institution = Institution::latest()->first();
                $titulo_documento = "Folha de caixa[ detalhada ]";
                $documentoGerado_documento = "Data da Transação: ";
                $documentoCode_documento = 8;

                
                if ($request->get('data2') == null) {

                    $date2 = null;
                    $repeticao = 0;



                    // consulta dos emolumentos e dos alunos.
                    $getTransaction = DB::table('transactions as trans')
                        ->join('transaction_article_requests as trans_articl_reques', 'trans_articl_reques.transaction_id', '=', 'trans.id')
                        ->join('article_requests as article_reques', 'article_reques.id', '=', 'trans_articl_reques.article_request_id')
                        ->leftJoin('article_translations as at', function ($join) {
                            $join->on('at.article_id', '=', 'article_reques.article_id');
                            $join->on('at.language_id', '=', \DB::raw(LanguageHelper::getCurrentLanguage()));
                            $join->on('at.active', '=', \DB::raw(true));
                        })
                        ->join('articles as article', 'article.id', '=', 'at.article_id')
                        ->join('users as us', 'us.id', '=', 'article_reques.user_id')
                        ->leftJoin('user_parameters as full_name', function ($join) {
                            $join->on('us.id', '=', 'full_name.users_id')
                                ->where('full_name.parameters_id', 1);
                        })
                        ->leftJoin('user_parameters as up_meca', function ($join) {
                            $join->on('us.id', '=', 'up_meca.users_id')
                                ->where('up_meca.parameters_id', 19);
                        })
                        ->join('user_courses as uc', 'uc.users_id', '=', 'us.id')
                        ->leftJoin('courses_translations as ct', function ($join) {
                            $join->on('ct.courses_id', '=', 'uc.courses_id');
                            $join->on('ct.language_id', '=', \DB::raw(LanguageHelper::getCurrentLanguage()));
                            $join->on('ct.active', '=', \DB::raw(true));
                        })
                        ->leftJoin('users as u1', 'u1.id', '=', 'trans.created_by')
                        ->leftJoin('user_parameters as user_va', function ($join) {
                            $join->on('u1.id', '=', 'user_va.users_id')
                                ->where('user_va.parameters_id', 1);
                        })
                        ->leftJoin('transaction_receipts as recibo', function ($join) {
                            $join->on('recibo.transaction_id', '=', 'trans.id');
                        })
                        ->leftJoin('historic_user_balance as historic_saldo', function ($join) {
                            $join->on('historic_saldo.id_transaction', '=', 'trans.id');
                        })
                        ->select([
                            'trans.id as transaction_id',
                            'trans.created_at as created_atranst',
                            'article.created_at as created_article',
                            'trans.type as transaction_type',
                            'article_reques.id as id_article_requests',
                            'at.display_name as article_name',
                            'full_name.value as full_name',
                            'recibo.code as recibo',
                            'recibo.path as path',
                            'ct.display_name as course_name',
                            'up_meca.value as matriculation_number',
                            'u1.name as created_by_user',
                            'historic_saldo.valor_credit as valorSaldo_credit',
                            'trans_articl_reques.value as price',
                            'us.id as user'
                        ])
                        ->distinct('trans.transaction_id')
                        ->whereDate('trans.created_at',  $date1)
                        ->whereIn('trans.type', ['payment', 'adjust'])
                        ->where('recibo.path', '!=', null)
                        ->where('trans.data_from', '!=', 'estorno')
                        ->where('historic_saldo.data_from', '=', null)
                        ->when($request->get('student') != null, function ($q) use ($student) {
                            return $q->whereIn('us.id', $student);
                        })
                        ->when($request->get('curso') != null, function ($q) use ($course) {
                            return $q->whereIn('ct.courses_id', $course);
                        })
                        ->when($request->get('article') != null, function ($q) use ($article) {
                            return $q->whereIn('at.article_id', $article);
                        })
                        ->orderBy('id_article_requests', 'asc')
                        ->get()
                        ->unique('id_article_requests')
                        ->groupBy(['article_name', 'course_name']);



                    $getTransaction = collect($getTransaction)->map(function ($item) {
                        foreach ($item as $key => $value) {
                            foreach ($value as $k => $valor) {
                                $array = explode('-', $valor->path);
                                $expldeSttring = explode('.', $array[1] . '-' . $array[2]);
                                $valor->recibo = $expldeSttring[0];
                            }
                        }
                        return $item;
                    });


                    // consultar os bancos e referencias de cada respentiva transacaõ
                    $getInfornBanco = DB::table('transactions as trans')
                        ->join('transaction_article_requests as trans_articl_reques', 'trans_articl_reques.transaction_id', '=', 'trans.id')
                        ->join('article_requests as article_reques', 'article_reques.id', '=', 'trans_articl_reques.article_request_id')
                        ->leftJoin('article_translations as at', function ($join) {
                            $join->on('at.article_id', '=', 'article_reques.article_id');
                            $join->on('at.language_id', '=', \DB::raw(LanguageHelper::getCurrentLanguage()));
                            $join->on('at.active', '=', \DB::raw(true));
                        })
                        ->join('articles as article', 'article.id', '=', 'at.article_id')
                        ->join('users as us', 'us.id', '=', 'article_reques.user_id')
                        ->join('transaction_info as info_trans', 'info_trans.transaction_id', '=', 'trans.id')
                        ->join('banks as bank', 'info_trans.bank_id', '=', 'bank.id')
                        ->leftJoin('historic_user_balance as historic_saldo', function ($join) {
                            $join->on('historic_saldo.id_transaction', '=', 'trans.id');
                        })
                        ->select([
                            'trans.id as transaction_id',
                            'trans.value as value_totalTtrans',
                            'article_reques.id as id_article_requests',
                            'bank.display_name as bank_name',
                            'bank.id as id_bank',
                            'info_trans.value as valorreferencia',
                            'historic_saldo.valor_credit as valorSaldo_credit',
                            'info_trans.reference as reference'
                        ])
                        ->distinct('trans.transaction_id')
                        ->whereDate('trans.created_at',  $date1)
                        ->where('trans.data_from', '!=', 'estorno')
                        ->when($request->get('student') != null, function ($q) use ($student) {
                            return $q->whereIn('us.id', $student);
                        })
                        ->when($request->get('article') != null, function ($q) use ($article) {
                            return $q->whereIn('at.article_id', $article);
                        })
                        ->orderBy('id_article_requests', 'asc')
                        ->get();



                    // estrutura que pega o valor total de cada tranzação e o saldo em carteira utilizado na respectiva transacão
                    foreach ($getInfornBanco as  $key => $item) {
                        if ($repeticao == 0) {
                            $repeticao = $item->transaction_id;
                            $objetoValorTransacao[] = (object) ['transaction_id' => $item->transaction_id, 'valorTotal_trans' => $item->value_totalTtrans];
                            $objetoSaldo_cartera[] = (object) ['transaction_id' => $item->transaction_id, 'valor_saldo' => $item->valorSaldo_credit];
                            $valorRepetido[] = $item->transaction_id;
                        }
                        if (in_array($item->transaction_id, $valorRepetido)) {
                        } else {
                            $valorRepetido[] = $item->transaction_id;
                            $repeticao = $item->transaction_id;
                            $objetoValorTransacao[] = (object) ['transaction_id' => $item->transaction_id, 'valorTotal_trans' => $item->value_totalTtrans];
                            $objetoSaldo_cartera[] = (object) ['transaction_id' => $item->transaction_id, 'valor_saldo' => $item->valorSaldo_credit];
                        }
                    }

                    /*
                        Para resolver a situação de dados duplicado foi utilizado, a logica fazer consulta aparte, uma consulta 
                        que serve para dados da transação bancaria, a outra são os dados como nome do aluno matricula, emolumentos...
                    */


                    $pdf_name = "FC[ detalhada ] " . $date1;
                    $pdf = PDF::loadView("Reports::enrollment-income", compact('objetoValorTransacao', 'objetoSaldo_cartera', 'getInfornBanco', 'getTransaction', 'date1', 'date2', 'institution', 'titulo_documento', 'documentoGerado_documento', 'documentoCode_documento'));
                    $pdf->setOption('margin-top', '1mm');
                    $pdf->setOption('margin-left', '1mm');
                    $pdf->setOption('margin-bottom', '2cm');
                    $pdf->setOption('margin-right', '1mm');
                    $pdf->setOption('enable-javascript', true);
                    $pdf->setOption('debug-javascript', true);
                    $pdf->setOption('javascript-delay', 1000);
                    $pdf->setOption('enable-smart-shrinking', true);
                    $pdf->setOption('no-stop-slow-scripts', true);
                    $pdf->setPaper('a4', 'landscape');

                    // $footer_html = view()->make('Reports::partials.enrollment-income-footer', compact('institution'))->render();
                    $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
                    $pdf->setOption('footer-html', $footer_html);
                    // return $pdf->stream('ISPM_T_0001');
                    return $pdf->download($pdf_name . '.pdf');
                } else {

                    $date_from = Carbon::parse($date1)->startOfDay();
                    $date_to = Carbon::parse($date2)->endOfDay();
                    $repeticao = 0;


                    // consulta dos emolumentos e dos alunos.
                    $getTransaction = DB::table('transactions as trans')
                        ->join('transaction_article_requests as trans_articl_reques', 'trans_articl_reques.transaction_id', '=', 'trans.id')
                        ->join('article_requests as article_reques', 'article_reques.id', '=', 'trans_articl_reques.article_request_id')
                        ->leftJoin('article_translations as at', function ($join) {
                            $join->on('at.article_id', '=', 'article_reques.article_id');
                            $join->on('at.language_id', '=', \DB::raw(LanguageHelper::getCurrentLanguage()));
                            $join->on('at.active', '=', \DB::raw(true));
                        })
                        ->leftJoin('articles as article', 'article.id', '=', 'at.article_id')
                        ->join('users as us', 'us.id', '=', 'article_reques.user_id')
                        ->leftJoin('user_parameters as full_name', function ($join) {
                            $join->on('us.id', '=', 'full_name.users_id')
                                ->where('full_name.parameters_id', 1);
                        })
                        ->leftJoin('user_parameters as up_meca', function ($join) {
                            $join->on('us.id', '=', 'up_meca.users_id')
                                ->where('up_meca.parameters_id', 19);
                        })
                        ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'us.id')
                        ->leftJoin('courses_translations as ct', function ($join) {
                            $join->on('ct.courses_id', '=', 'uc.courses_id');
                            $join->on('ct.language_id', '=', \DB::raw(LanguageHelper::getCurrentLanguage()));
                            $join->on('ct.active', '=', \DB::raw(true));
                        })
                        ->leftJoin('users as u1', 'u1.id', '=', 'trans.created_by')
                        ->leftJoin('user_parameters as user_va', function ($join) {
                            $join->on('u1.id', '=', 'user_va.users_id')
                                ->where('user_va.parameters_id', 1);
                        })
                        ->leftJoin('transaction_receipts as recibo', function ($join) {
                            $join->on('recibo.transaction_id', '=', 'trans.id');
                        })
                        ->leftJoin('historic_user_balance as historic_saldo', function ($join) {
                            $join->on('historic_saldo.id_transaction', '=', 'trans.id');
                        })
                        ->select([
                            'trans.id as transaction_id',
                            'trans.created_at as created_atranst',
                            'article.created_at as created_article',
                            'trans.type as transaction_type',
                            'article_reques.id as id_article_requests',
                            'at.display_name as article_name',
                            'full_name.value as full_name',
                            'recibo.code as recibo',
                            'recibo.path as path',
                            'ct.display_name as course_name',
                            'up_meca.value as matriculation_number',
                            'u1.name as created_by_user',
                            'historic_saldo.valor_credit as valorSaldo_credit',
                            'trans_articl_reques.value as price',
                            'us.id as user'
                        ])
                        ->distinct('trans.transaction_id')
                        ->whereBetween('trans.created_at', [$date_from, $date_to])
                        ->whereIn('trans.type', ['payment', 'adjust'])
                        ->where('recibo.path', '!=', null)
                        ->where('trans.data_from', '!=', 'estorno')
                        ->where('historic_saldo.data_from', '=', null)
                        ->when($request->get('student') != null, function ($q) use ($student) {
                            return $q->whereIn('us.id', $student);
                        })
                        ->when($request->get('curso') != null, function ($q) use ($course) {
                            return $q->whereIn('ct.courses_id', $course);
                        })
                        ->when($request->get('article') != null, function ($q) use ($article) {
                            return $q->whereIn('at.article_id', $article);
                        })
                        ->orderBy('id_article_requests', 'asc')
                        ->get()
                        ->unique('id_article_requests')
                        ->groupBy(['article_name', 'course_name']);


                    $getTransaction = collect($getTransaction)->map(function ($item) {
                        foreach ($item as $key => $value) {
                            foreach ($value as $k => $valor) {
                                $array = explode('-', $valor->path);
                                $expldeSttring = explode('.', $array[1] . '-' . $array[2]);
                                $valor->recibo = $expldeSttring[0];
                            }
                        }
                        return $item;
                    });




                    // consultar os bancos e referencias de cada respentiva transacaõ
                    $getInfornBanco = DB::table('transactions as trans')
                        ->join('transaction_article_requests as trans_articl_reques', 'trans_articl_reques.transaction_id', '=', 'trans.id')
                        ->join('article_requests as article_reques', 'article_reques.id', '=', 'trans_articl_reques.article_request_id')
                        ->leftJoin('article_translations as at', function ($join) {
                            $join->on('at.article_id', '=', 'article_reques.article_id');
                            $join->on('at.language_id', '=', \DB::raw(LanguageHelper::getCurrentLanguage()));
                            $join->on('at.active', '=', \DB::raw(true));
                        })
                        ->join('articles as article', 'article.id', '=', 'at.article_id')
                        ->join('users as us', 'us.id', '=', 'article_reques.user_id')
                        ->join('transaction_info as info_trans', 'info_trans.transaction_id', '=', 'trans.id')
                        ->join('banks as bank', 'info_trans.bank_id', '=', 'bank.id')
                        ->leftJoin('historic_user_balance as historic_saldo', function ($join) {
                            $join->on('historic_saldo.id_transaction', '=', 'trans.id');
                        })
                        ->select([
                            'trans.id as transaction_id',
                            'trans.value as value_totalTtrans',
                            'article_reques.id as id_article_requests',
                            'bank.display_name as bank_name',
                            'bank.id as id_bank',
                            'info_trans.value as valorreferencia',
                            'historic_saldo.valor_credit as valorSaldo_credit',
                            'info_trans.reference as reference'
                        ])
                        ->distinct('trans.transaction_id')
                        ->whereBetween('trans.created_at', [$date_from, $date_to])
                        ->whereIn('trans.type', ['payment', 'adjust'])
                        ->where('trans.data_from', '!=', 'estorno')
                        ->when($request->get('student') != null, function ($q) use ($student) {
                            return $q->whereIn('us.id', $student);
                        })
                        ->when($request->get('article') != null, function ($q) use ($article) {
                            return $q->whereIn('at.article_id', $article);
                        })
                        ->orderBy('id_article_requests', 'asc')
                        ->get();



                    // estrutura que pega o valor total de cada tranzação e o saldo em carteira utilizado na respectiva transacão
                    foreach ($getInfornBanco as  $key => $item) {
                        if ($repeticao == 0) {
                            $repeticao = $item->transaction_id;
                            $objetoValorTransacao[] = (object) ['transaction_id' => $item->transaction_id, 'valorTotal_trans' => $item->value_totalTtrans];
                            $objetoSaldo_cartera[] = (object) ['transaction_id' => $item->transaction_id, 'valor_saldo' => $item->valorSaldo_credit];
                            $valorRepetido[] = $item->transaction_id;
                        }
                        if (in_array($item->transaction_id, $valorRepetido)) {
                        } else {
                            $valorRepetido[] = $item->transaction_id;
                            $repeticao = $item->transaction_id;
                            $objetoValorTransacao[] = (object) ['transaction_id' => $item->transaction_id, 'valorTotal_trans' => $item->value_totalTtrans];
                            $objetoSaldo_cartera[] = (object) ['transaction_id' => $item->transaction_id, 'valor_saldo' => $item->valorSaldo_credit];
                        }
                    }

                    /*
                        Para resolver a situação de dados duplicado foi utilizado, a logica fazer consulta aparte, uma consulta 
                        que serve para dados da transação bancaria, a outra são os dados como nome do aluno matricula, emolumentos...
                    */

                    $pdf_name = "FC[ detalhada ] " . $date1 . "___" . $date2;
                    $institution = Institution::latest()->first();
                    // return view('Reports::partials.enrollment-income-footer'); Claudio Teste.
                    //return view("Reports::enrollment-income", compact('emoluments', 'date1', 'date2'));
                    $pdf = PDF::loadView("Reports::enrollment-income", compact('objetoValorTransacao', 'objetoSaldo_cartera', 'getInfornBanco', 'getTransaction', 'date1', 'date2', 'institution', 'titulo_documento', 'documentoGerado_documento', 'documentoCode_documento'));
                    $pdf->setOption('margin-top', '1mm');
                    $pdf->setOption('margin-left', '1mm');
                    $pdf->setOption('margin-bottom', '2cm');
                    $pdf->setOption('margin-right', '1mm');
                    $pdf->setOption('enable-javascript', true);
                    $pdf->setOption('debug-javascript', true);
                    $pdf->setOption('javascript-delay', 1000);
                    $pdf->setOption('enable-smart-shrinking', true);
                    $pdf->setOption('no-stop-slow-scripts', true);
                    $pdf->setPaper('a4', 'landscape');
                    // $footer_html = view()->make('Reports::partials.enrollment-income-footer', compact('institution'))->render();
                    $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution', 'qtdRegisto'))->render();
                    $pdf->setOption('footer-html', $footer_html);
                    // return $pdf->stream('ISPM_T_0001');
                    return $pdf->download($pdf_name . '.pdf');
                }

                break;
            case 'excel':
                $date1 =  $request->get('data1') == null ? date('Y-m-d') : $request->get('data1');
                $date2 =  $request->get('data2');

                $article = $request->get('article');
                $course = $request->get('curso');
                $student = $request->get('student');

                $institution = Institution::latest()->first();
                $titulo_documento = "Folha de caixa[ detalhada ]";
                $documentoGerado_documento = "Data da Transação: ";
                $documentoCode_documento = 8;

              

                if ($request->get('data2') == null) {

                    $date2 = null;
                    $repeticao = 0;



                    // consulta dos emolumentos e dos alunos.
                    $getTransaction = DB::table('transactions as trans')
                        ->join('transaction_article_requests as trans_articl_reques', 'trans_articl_reques.transaction_id', '=', 'trans.id')
                        ->join('article_requests as article_reques', 'article_reques.id', '=', 'trans_articl_reques.article_request_id')
                        ->leftJoin('article_translations as at', function ($join) {
                            $join->on('at.article_id', '=', 'article_reques.article_id');
                            $join->on('at.language_id', '=', \DB::raw(LanguageHelper::getCurrentLanguage()));
                            $join->on('at.active', '=', \DB::raw(true));
                        })
                        ->join('articles as article', 'article.id', '=', 'at.article_id')
                        ->join('users as us', 'us.id', '=', 'article_reques.user_id')
                        ->leftJoin('user_parameters as full_name', function ($join) {
                            $join->on('us.id', '=', 'full_name.users_id')
                                ->where('full_name.parameters_id', 1);
                        })
                        ->leftJoin('user_parameters as up_meca', function ($join) {
                            $join->on('us.id', '=', 'up_meca.users_id')
                                ->where('up_meca.parameters_id', 19);
                        })
                        ->lefJoin('user_courses as uc', 'uc.users_id', '=', 'us.id')
                        ->leftJoin('courses_translations as ct', function ($join) {
                            $join->on('ct.courses_id', '=', 'uc.courses_id');
                            $join->on('ct.language_id', '=', \DB::raw(LanguageHelper::getCurrentLanguage()));
                            $join->on('ct.active', '=', \DB::raw(true));
                        })
                        ->leftJoin('users as u1', 'u1.id', '=', 'trans.created_by')
                        ->leftJoin('user_parameters as user_va', function ($join) {
                            $join->on('u1.id', '=', 'user_va.users_id')
                                ->where('user_va.parameters_id', 1);
                        })
                        ->leftJoin('transaction_receipts as recibo', function ($join) {
                            $join->on('recibo.transaction_id', '=', 'trans.id');
                        })
                        ->leftJoin('historic_user_balance as historic_saldo', function ($join) {
                            $join->on('historic_saldo.id_transaction', '=', 'trans.id');
                        })
                        ->select([
                            'trans.id as transaction_id',
                            'trans.created_at as created_atranst',
                            'article.created_at as created_article',
                            'trans.type as transaction_type',
                            'article_reques.id as id_article_requests',
                            'at.display_name as article_name',
                            'full_name.value as full_name',
                            'recibo.code as recibo',
                            'recibo.path as path',
                            'ct.display_name as course_name',
                            'up_meca.value as matriculation_number',
                            'u1.name as created_by_user',
                            'historic_saldo.valor_credit as valorSaldo_credit',
                            'trans_articl_reques.value as price'
                        ])
                        ->distinct('trans.transaction_id')
                        ->whereDate('trans.created_at',  $date1)
                        ->whereIn('trans.type', ['payment', 'adjust'])
                        ->where('recibo.path', '!=', null)
                        ->where('trans.data_from', '!=', 'estorno')
                        ->where('historic_saldo.data_from', '=', null)
                        ->when($request->get('student') != null, function ($q) use ($student) {
                            return $q->whereIn('us.id', $student);
                        })
                        ->when($request->get('curso') != null, function ($q) use ($course) {
                            return $q->whereIn('ct.courses_id', $course);
                        })
                        ->when($request->get('article') != null, function ($q) use ($article) {
                            return $q->whereIn('at.article_id', $article);
                        })
                        ->orderBy('id_article_requests', 'asc')
                        ->get()
                        ->groupBy(['article_name', 'course_name']);

                    $getTransaction = collect($getTransaction)->map(function ($item) {
                        foreach ($item as $key => $value) {
                            foreach ($value as $k => $valor) {
                                $array = explode('-', $valor->path);
                                $expldeSttring = explode('.', $array[1] . '-' . $array[2]);
                                $valor->recibo = $expldeSttring[0];
                            }
                        }
                        return $item;
                    });


                    // consultar os bancos e referencias de cada respentiva transacaõ
                    $getInfornBanco = DB::table('transactions as trans')
                        ->join('transaction_article_requests as trans_articl_reques', 'trans_articl_reques.transaction_id', '=', 'trans.id')
                        ->join('article_requests as article_reques', 'article_reques.id', '=', 'trans_articl_reques.article_request_id')
                        ->leftJoin('article_translations as at', function ($join) {
                            $join->on('at.article_id', '=', 'article_reques.article_id');
                            $join->on('at.language_id', '=', \DB::raw(LanguageHelper::getCurrentLanguage()));
                            $join->on('at.active', '=', \DB::raw(true));
                        })
                        ->join('articles as article', 'article.id', '=', 'at.article_id')
                        ->join('users as us', 'us.id', '=', 'article_reques.user_id')
                        ->join('transaction_info as info_trans', 'info_trans.transaction_id', '=', 'trans.id')
                        ->join('banks as bank', 'info_trans.bank_id', '=', 'bank.id')
                        ->leftJoin('historic_user_balance as historic_saldo', function ($join) {
                            $join->on('historic_saldo.id_transaction', '=', 'trans.id');
                        })
                        ->select([
                            'trans.id as transaction_id',
                            'trans.value as value_totalTtrans',
                            'article_reques.id as id_article_requests',
                            'bank.display_name as bank_name',
                            'bank.id as id_bank',
                            'info_trans.value as valorreferencia',
                            'historic_saldo.valor_credit as valorSaldo_credit',
                            'info_trans.reference as reference'
                        ])
                        ->distinct('trans.transaction_id')
                        ->whereDate('trans.created_at',  $date1)
                        ->where('trans.data_from', '!=', 'estorno')
                        ->when($request->get('student') != null, function ($q) use ($student) {
                            return $q->whereIn('us.id', $student);
                        })
                        ->when($request->get('article') != null, function ($q) use ($article) {
                            return $q->whereIn('at.article_id', $article);
                        })
                        ->orderBy('id_article_requests', 'asc')
                        ->get();



                    // estrutura que pega o valor total de cada tranzação e o saldo em carteira utilizado na respectiva transacão
                    foreach ($getInfornBanco as  $key => $item) {
                        if ($repeticao == 0) {
                            $repeticao = $item->transaction_id;
                            $objetoValorTransacao[] = (object) ['transaction_id' => $item->transaction_id, 'valorTotal_trans' => $item->value_totalTtrans];
                            $objetoSaldo_cartera[] = (object) ['transaction_id' => $item->transaction_id, 'valor_saldo' => $item->valorSaldo_credit];
                            $valorRepetido[] = $item->transaction_id;
                        }
                        if (in_array($item->transaction_id, $valorRepetido)) {
                        } else {
                            $valorRepetido[] = $item->transaction_id;
                            $repeticao = $item->transaction_id;
                            $objetoValorTransacao[] = (object) ['transaction_id' => $item->transaction_id, 'valorTotal_trans' => $item->value_totalTtrans];
                            $objetoSaldo_cartera[] = (object) ['transaction_id' => $item->transaction_id, 'valor_saldo' => $item->valorSaldo_credit];
                        }
                    }

                    /*
                        Para resolver a situação de dados duplicado foi utilizado, a logica fazer consulta aparte, uma consulta 
                        que serve para dados da transação bancaria, a outra são os dados como nome do aluno matricula, emolumentos...
                    */


                    $pdf_name = "FC[ detalhada ] " . $date1;
                    $pdf = PDF::loadView("Reports::enrollment-income", compact('objetoValorTransacao', 'objetoSaldo_cartera', 'getInfornBanco', 'getTransaction', 'date1', 'date2', 'institution', 'titulo_documento', 'documentoGerado_documento', 'documentoCode_documento'));
                    $pdf->setOption('margin-top', '1mm');
                    $pdf->setOption('margin-left', '1mm');
                    $pdf->setOption('margin-bottom', '2cm');
                    $pdf->setOption('margin-right', '1mm');
                    $pdf->setOption('enable-javascript', true);
                    $pdf->setOption('debug-javascript', true);
                    $pdf->setOption('javascript-delay', 1000);
                    $pdf->setOption('enable-smart-shrinking', true);
                    $pdf->setOption('no-stop-slow-scripts', true);
                    $pdf->setPaper('a4', 'landscape');

                    // $footer_html = view()->make('Reports::partials.enrollment-income-footer', compact('institution'))->render();
                    $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
                    $pdf->setOption('footer-html', $footer_html);
                    // return $pdf->stream('ISPM_T_0001');
                    return $pdf->download($pdf_name . '.pdf');
                } else {

                    $date_from = Carbon::parse($date1)->startOfDay();
                    $date_to = Carbon::parse($date2)->endOfDay();
                    $repeticao = 0;


                    // consulta dos emolumentos e dos alunos.
                    $getTransaction = DB::table('transactions as trans')
                        ->join('transaction_article_requests as trans_articl_reques', 'trans_articl_reques.transaction_id', '=', 'trans.id')
                        ->join('article_requests as article_reques', 'article_reques.id', '=', 'trans_articl_reques.article_request_id')
                        ->leftJoin('article_translations as at', function ($join) {
                            $join->on('at.article_id', '=', 'article_reques.article_id');
                            $join->on('at.language_id', '=', \DB::raw(LanguageHelper::getCurrentLanguage()));
                            $join->on('at.active', '=', \DB::raw(true));
                        })
                        ->leftJoin('articles as article', 'article.id', '=', 'at.article_id')
                        ->join('users as us', 'us.id', '=', 'article_reques.user_id')
                        ->leftJoin('user_parameters as full_name', function ($join) {
                            $join->on('us.id', '=', 'full_name.users_id')
                                ->where('full_name.parameters_id', 1);
                        })
                        ->leftJoin('user_parameters as up_meca', function ($join) {
                            $join->on('us.id', '=', 'up_meca.users_id')
                                ->where('up_meca.parameters_id', 19);
                        })
                        ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'us.id')
                        ->leftJoin('courses_translations as ct', function ($join) {
                            $join->on('ct.courses_id', '=', 'uc.courses_id');
                            $join->on('ct.language_id', '=', \DB::raw(LanguageHelper::getCurrentLanguage()));
                            $join->on('ct.active', '=', \DB::raw(true));
                        })
                        ->leftJoin('users as u1', 'u1.id', '=', 'trans.created_by')
                        ->leftJoin('user_parameters as user_va', function ($join) {
                            $join->on('u1.id', '=', 'user_va.users_id')
                                ->where('user_va.parameters_id', 1);
                        })
                        ->leftJoin('transaction_receipts as recibo', function ($join) {
                            $join->on('recibo.transaction_id', '=', 'trans.id');
                        })
                        ->leftJoin('historic_user_balance as historic_saldo', function ($join) {
                            $join->on('historic_saldo.id_transaction', '=', 'trans.id');
                        })
                        ->select([
                            'trans.id as transaction_id',
                            'trans.created_at as created_atranst',
                            'article.created_at as created_article',
                            'trans.type as transaction_type',
                            'article_reques.id as id_article_requests',
                            'at.display_name as article_name',
                            'full_name.value as full_name',
                            'recibo.code as recibo',
                            'recibo.path as path',
                            'ct.display_name as course_name',
                            'up_meca.value as matriculation_number',
                            'u1.name as created_by_user',
                            'historic_saldo.valor_credit as valorSaldo_credit',
                            'trans_articl_reques.value as price'
                        ])
                        ->distinct('trans.transaction_id')
                        ->whereBetween('trans.created_at', [$date_from, $date_to])
                        ->whereIn('trans.type', ['payment', 'adjust'])
                        ->where('recibo.path', '!=', null)
                        ->where('trans.data_from', '!=', 'estorno')
                        ->where('historic_saldo.data_from', '=', null)
                        ->when($request->get('student') != null, function ($q) use ($student) {
                            return $q->whereIn('us.id', $student);
                        })
                        ->when($request->get('curso') != null, function ($q) use ($course) {
                            return $q->whereIn('ct.courses_id', $course);
                        })
                        ->when($request->get('article') != null, function ($q) use ($article) {
                            return $q->whereIn('at.article_id', $article);
                        })
                        ->orderBy('id_article_requests', 'asc')
                        ->get()
                        ->groupBy(['article_name', 'course_name']);

                    $getTransaction = collect($getTransaction)->map(function ($item) {
                        foreach ($item as $key => $value) {
                            foreach ($value as $k => $valor) {
                                $array = explode('-', $valor->path);
                                $expldeSttring = explode('.', $array[1] . '-' . $array[2]);
                                $valor->recibo = $expldeSttring[0];
                            }
                        }
                        return $item;
                    });


                    // consultar os bancos e referencias de cada respentiva transacaõ
                    $getInfornBanco = DB::table('transactions as trans')
                        ->join('transaction_article_requests as trans_articl_reques', 'trans_articl_reques.transaction_id', '=', 'trans.id')
                        ->join('article_requests as article_reques', 'article_reques.id', '=', 'trans_articl_reques.article_request_id')
                        ->leftJoin('article_translations as at', function ($join) {
                            $join->on('at.article_id', '=', 'article_reques.article_id');
                            $join->on('at.language_id', '=', \DB::raw(LanguageHelper::getCurrentLanguage()));
                            $join->on('at.active', '=', \DB::raw(true));
                        })
                        ->join('articles as article', 'article.id', '=', 'at.article_id')
                        ->join('users as us', 'us.id', '=', 'article_reques.user_id')
                        ->join('transaction_info as info_trans', 'info_trans.transaction_id', '=', 'trans.id')
                        ->join('banks as bank', 'info_trans.bank_id', '=', 'bank.id')
                        ->leftJoin('historic_user_balance as historic_saldo', function ($join) {
                            $join->on('historic_saldo.id_transaction', '=', 'trans.id');
                        })
                        ->select([
                            'trans.id as transaction_id',
                            'trans.value as value_totalTtrans',
                            'article_reques.id as id_article_requests',
                            'bank.display_name as bank_name',
                            'bank.id as id_bank',
                            'info_trans.value as valorreferencia',
                            'historic_saldo.valor_credit as valorSaldo_credit',
                            'info_trans.reference as reference'
                        ])
                        ->distinct('trans.transaction_id')
                        ->whereBetween('trans.created_at', [$date_from, $date_to])
                        ->whereIn('trans.type', ['payment', 'adjust'])
                        ->where('trans.data_from', '!=', 'estorno')
                        ->when($request->get('student') != null, function ($q) use ($student) {
                            return $q->whereIn('us.id', $student);
                        })
                        ->when($request->get('article') != null, function ($q) use ($article) {
                            return $q->whereIn('at.article_id', $article);
                        })
                        ->orderBy('id_article_requests', 'asc')
                        ->get();

                       

                    // estrutura que pega o valor total de cada tranzação e o saldo em carteira utilizado na respectiva transacão
                    foreach ($getInfornBanco as  $key => $item) {
                        if ($repeticao == 0) {
                            $repeticao = $item->transaction_id;
                            $objetoValorTransacao[] = (object) ['transaction_id' => $item->transaction_id, 'valorTotal_trans' => $item->value_totalTtrans];
                            $objetoSaldo_cartera[] = (object) ['transaction_id' => $item->transaction_id, 'valor_saldo' => $item->valorSaldo_credit];
                            $valorRepetido[] = $item->transaction_id;
                        }
                        if (in_array($item->transaction_id, $valorRepetido)) {
                        } else {
                            $valorRepetido[] = $item->transaction_id;
                            $repeticao = $item->transaction_id;
                            $objetoValorTransacao[] = (object) ['transaction_id' => $item->transaction_id, 'valorTotal_trans' => $item->value_totalTtrans];
                            $objetoSaldo_cartera[] = (object) ['transaction_id' => $item->transaction_id, 'valor_saldo' => $item->valorSaldo_credit];
                        }
                    }

                    /*
                        Para resolver a situação de dados duplicado foi utilizado, a logica fazer consulta aparte, uma consulta 
                        que serve para dados da transação bancaria, a outra são os dados como nome do aluno matricula, emolumentos...
                    */

                    $pdf_name = "FC[ detalhada ] " . $date1 . "___" . $date2;
                    $institution = Institution::latest()->first();
                    // return view('Reports::partials.enrollment-income-footer'); Claudio Teste.
                    //return view("Reports::enrollment-income", compact('emoluments', 'date1', 'date2'));

                    $data = [
                        'objetoValorTransacao' => $objetoValorTransacao,
                        'objetoSaldo_cartera' => $objetoSaldo_cartera,
                        'getInfornBanco' => $getInfornBanco,
                        'getTransaction' => $getTransaction,
                        'date1' => $date1,
                        'date2' => $date2,
                        'institution' => $institution,
                        'titulo_documento' => $titulo_documento,
                        'documentoGerado_documento' => $documentoGerado_documento,
                        'documentoCode_documento' => $documentoCode_documento
                    ];


                    return Excel::download(new IncomeExport($data), 'FCD' . now() . '.xlsx');
                }

                // return Excel::download(new Detalhada, 'Detalhada.xlsx');


                break;
            // case 'download':
            //     return "";

            //     break;
            default:
                return redirect()->back();
                break;
        }
    }



    private function FunctionName()
    {
        # code...
    }

    public function retormar_emolumento()
    {

        //--------------------------Pegar as transaction-------------------------//
        return $transation = DB::table('transaction_article_requests as tras')
            ->join('article_requests as art', 'art.id', '=', 'tras.article_request_id')
            ->select(['article_request_id as id_article', 'value',])
            ->get();
        //----------------------------------------------------------------------//

    }


    public function enrollmentIncome()
    {
        //trazer todos os emolumentos pagos num dia -> x
        //mostrar o subtotal dos emolumentos pagos
        //Agrupar por por curso
        //Ordernar por ANO

        $emoluments = Transaction::join('transaction_article_requests', 'transaction_article_requests.transaction_id', '=', 'transactions.id')
            ->join('article_requests', 'article_requests.id', '=', 'transaction_article_requests.article_request_id')
            ->leftJoin('article_translations as at', function ($join) {
                $join->on('at.article_id', '=', 'article_requests.article_id');
                $join->on('at.language_id', '=', \DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('at.active', '=', \DB::raw(true));
            })
            //->leftJoin('transaction_article_requests as tar1', 'tar1.article_request_id','=','article_requests')
            ->join('users', 'users.id', '=', 'article_requests.user_id')
            ->join('matriculations', 'matriculations.user_id', '=', 'users.id')
            ->leftJoin('user_parameters as u_p', function ($join) {
                $join->on('users.id', '=', 'u_p.users_id')
                    ->where('u_p.parameters_id', 1);
            })
            ->join('transaction_info', 'transaction_info.transaction_id', '=', 'transactions.id')
            ->join('banks', 'transaction_info.bank_id', '=', 'banks.id')
            ->join('user_courses', 'user_courses.users_id', '=', 'users.id')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'user_courses.courses_id');
                $join->on('ct.language_id', '=', \DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', \DB::raw(true));
            })
            ->leftJoin('user_parameters as up_meca', function ($join) {
                $join->on('users.id', '=', 'up_meca.users_id')
                    ->where('up_meca.parameters_id', 19);
            })
            ->leftJoin('users as u1', 'u1.id', '=', 'transactions.created_by')
            ->leftJoin('user_parameters as user_va', function ($join) {
                $join->on('u1.id', '=', 'user_va.users_id')
                    ->where('user_va.parameters_id', 1);
            })
            ->select(
                'at.article_id as article_id',
                'at.display_name as article_name',
                'users.name as user_name',
                //'transactions.value as price',
                'transaction_article_requests.value as price',
                'banks.display_name as bank_name',
                'transaction_info.reference as reference',
                'ct.display_name as course_name',
                'ct.courses_id as course_id',
                'up_meca.value as matriculation_number',
                'matriculations.course_year as course_year',
                'transaction_info.fulfilled_at as fulfilled_at',
                'u1.name as created_by'
            )
            ->whereDate('transactions.created_at', '2020-03-04') #\DB::raw('CURDATE()')
            ->where('transactions.type', 'payment')
            ->get()
            ->groupBy(['article_name', 'course_name']);


        $institution = Institution::latest()->first();

        //return view("Reports::enrollment-income", compact('emoluments'));
        //$pdf = \App::make('dompdf.wrapper');
        //$pdf->loadView("Reports::enrollment-income", compact('emoluments'));
        $pdf = PDF::loadView("Reports::enrollment-income", compact('emoluments'));
        $pdf->setOption('margin-top', '2cm');
        $pdf->setOption('margin-left', '1cm');
        $pdf->setOption('margin-bottom', '3cm');
        $pdf->setOption('margin-right', '1cm');
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('debug-javascript', true);
        $pdf->setOption('javascript-delay', 1000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);
        $pdf->setPaper('a4');

        // $footer_html = view()->make('Reports::partials.enrollment-income-footer', compact('institution'))->render();
        $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
        $pdf->setOption('footer-html', $footer_html);
        return $pdf->stream('ISPM_T_0001');
    }



    public function generateEnrollmentPending()
    {

        // Article::with('currentTranslation')->get();
        $lectiveYears = LectiveYear::with(['currentTranslation'])
            ->get();

        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
            ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
            ->first();


        $articles = DB::table('articles as articl')
            ->leftJoin('article_translations as art_trans', 'art_trans.article_id', '=', 'articl.id')
            ->leftJoin('article_extra_fees as extra_fees', function ($join) {
                $join->on('extra_fees.article_id', '=', 'articl.id');
            })
            ->select([
                'extra_fees.article_id as article_id_extra_fees',
                'articl.id as id',
                'articl.code as code',
                'art_trans.display_name as display_name'
            ])
            ->whereNull('articl.deleted_at')
            ->where('art_trans.active', '=', 1)
            ->orderByRaw('articl.deleted_at DESC')
            ->distinct()
            ->whereBetween('articl.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
            ->get();


        $lista_Month = [];
        $ordem_Month = [];
        $desor_Month = [];
        $getLocalizedMonths = getLocalizedMonths();
        foreach ($getLocalizedMonths as $key => $value) {
            if ($value['id'] > 7 && $value['id'] < 10) {
            } else {
                $lista_Month[] = $value;
            }
        }
        foreach ($lista_Month as $index => $item) {
            if ($item['id'] > 9) {
                $ordem_Month[] = $item;
            } else {
                $desor_Month[] = $item;
            }
        }
        foreach ($desor_Month as $indexInArray => $element) {
            $ordem_Month[] = $element;
        }

        $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
        $courses = Course::with('currentTranslation')->get();
        $data = ['ordem_Month' => $ordem_Month, 'articles' => $articles, 'courses' => $courses, 'lectiveYears' => $lectiveYears, 'lectiveYearSelected' => $lectiveYearSelected];

        return view("Reports::generate-enrollment-pending")->with($data);
    }


    public function generateEnrollmentTotal()
    {

        // Article::with('currentTranslation')->get();
        $lectiveYears = LectiveYear::with(['currentTranslation'])
            ->get();

        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
            ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
            ->first();


        $articles = DB::table('articles as articl')
            ->leftJoin('article_translations as art_trans', 'art_trans.article_id', '=', 'articl.id')
            ->leftJoin('article_extra_fees as extra_fees', function ($join) {
                $join->on('extra_fees.article_id', '=', 'articl.id');
            })
            ->select([
                'extra_fees.article_id as article_id_extra_fees',
                'articl.id as id',
                'articl.code as code',
                'art_trans.display_name as display_name'
            ])
            ->whereNull('articl.deleted_at')
            ->where('art_trans.active', '=', 1)
            ->orderByRaw('articl.deleted_at DESC')
            ->distinct()
            ->whereBetween('articl.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
            ->get();


        $lista_Month = [];
        $ordem_Month = [];
        $desor_Month = [];
        $getLocalizedMonths = getLocalizedMonths();
        foreach ($getLocalizedMonths as $key => $value) {
            if ($value['id'] > 7 && $value['id'] < 10) {
            } else {
                $lista_Month[] = $value;
            }
        }
        foreach ($lista_Month as $index => $item) {
            if ($item['id'] > 9) {
                $ordem_Month[] = $item;
            } else {
                $desor_Month[] = $item;
            }
        }
        foreach ($desor_Month as $indexInArray => $element) {
            $ordem_Month[] = $element;
        }

        $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
        $courses = Course::with('currentTranslation')->get();
        $data = ['ordem_Month' => $ordem_Month, 'articles' => $articles, 'courses' => $courses, 'lectiveYears' => $lectiveYears, 'lectiveYearSelected' => $lectiveYearSelected];

        // return $data;
        return view("Reports::generate-enrollment-total")->with($data);
    }

    public function enrollmentTotalWithParameters(Request $request)
    {


        $institution = Institution::latest()->first();
        $titulo_documento = "Lista de NÃO DEVEDORES";
        $documentoGerado_documento = "Data: ";
        $documentoCode_documento = 7;
        $month = [];


        $lective = explode(",", $request->lective_years)[0];
        $date1 =  $request->get('dataInicio_id') == null ? date('Y-m-d') : $request->get('dataInicio_id');
        $date2 =  $request->get('dataFim_id');
        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
            ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
            ->first();

        $article = $request->get('article');
        $course = $request->get('curso');
        $student = $request->get('student');

        $articles = DB::table('article_requests')
            ->leftJoin('user_parameters as u_p', function ($join) {
                $join->on('article_requests.user_id', '=', 'u_p.users_id')
                    ->where('u_p.parameters_id', 1);
            })

            ->leftJoin('user_parameters as u_p1', function ($join) {
                $join->on('article_requests.user_id', '=', 'u_p1.users_id')
                    ->where('u_p1.parameters_id', 19);
            })

            ->leftjoin('user_courses as uc', 'uc.users_id', '=', 'article_requests.user_id')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'uc.courses_id');
                $join->on('ct.language_id', '=', \DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', \DB::raw(true));
            })
            ->leftJoin('matriculations as matriculat', function ($join) {
                $join->on('matriculat.user_id', '=', 'article_requests.user_id');
            })
            ->whereIn('article_requests.user_id', $student)
            ->whereNull('article_requests.deleted_at')
            ->where("status", "!=", "total")
            ->whereBetween('article_requests.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
            ->where('matriculat.lective_year', $lective)
            ->select([
                'ct.display_name as course_name',
                'article_requests.year as years',
                'article_requests.user_id as id_user',
                'article_requests.month',
                'article_requests.status',
                'u_p1.value as meca',
                'u_p.value as name',
                'matriculat.id as id',
                'matriculat.course_year as year',
                'matriculat.lective_year as lective_year',
            ])
            ->orderBy("u_p.value", "asc")
            ->get();

        $final = $lectiveYearSelected->end_date;
        $emoluments = collect($articles)->groupBy('meca')->map(function ($item, $key) use ($final) {

            $count = 0;
            foreach ($item as $value) {
                if (($value->month != null || $value->month != "") && ($value->years != null || $value->years != "")) {
                    $mes = $value->month;
                    $ano = $value->years;
                    $qtd_mes = strlen($mes);

                    if (($qtd_mes == 1) && ($mes < 10)) {
                        $mes = "0" . $mes;
                    }


                    $dataactual = date("Y-m");
                    $dataemo = $ano . "-" . $mes;
                    $final = explode("-", $final);
                    $final = $final[0] . "-" . $final[1];

                    if (($dataemo > $dataactual) && ($dataemo <= $final)) {
                        if (date("d") > 15) {

                            $count++;
                        }
                    } else {
                        $count++;
                    }
                } else {
                    $count++;
                }
            }



            if ($count == 0) {

                return $item[0];
            }
        });

        $pdf = PDF::loadView("Reports::total-articles", compact(
            'emoluments',
            'date1',
            'date2',
            'institution',
            'titulo_documento',
            'documentoGerado_documento',
            'documentoCode_documento'
        ));
        $pdf->setOption('margin-top', '1.5mm');
        $pdf->setOption('margin-left', '1.8mm');
        $pdf->setOption('margin-bottom', '2cm');
        $pdf->setOption('margin-right', '1.8mm');
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('debug-javascript', true);
        $pdf->setOption('javascript-delay', 1000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);
        $pdf->setPaper('a4', 'portrait');
        $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
        $pdf->setOption('footer-html', $footer_html);
        return $pdf->stream('ISPM_T_0002');
    }

    public function generateEnrollmentPendingFinalist()
    {



        // Article::with('currentTranslation')->get();
        $lectiveYears = LectiveYear::with(['currentTranslation'])
            ->get();

        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
            ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
            ->first();


        $articles = DB::table('articles as articl')
            ->leftJoin('article_translations as art_trans', 'art_trans.article_id', '=', 'articl.id')
            ->leftJoin('article_extra_fees as extra_fees', function ($join) {
                $join->on('extra_fees.article_id', '=', 'articl.id');
            })
            ->select([
                'extra_fees.article_id as article_id_extra_fees',
                'articl.id as id',
                'articl.code as code',
                'art_trans.display_name as display_name'
            ])
            ->whereNull('articl.deleted_at')
            ->where('art_trans.active', '=', 1)
            ->whereIn('art_trans.display_name', ["Propina - Finalista", "Diploma", "Certificado", "Cerimônia de outorga"])
            ->orderBy('art_trans.display_name')
            ->distinct()
            ->whereBetween('articl.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
            ->get();


        $lista_Month = [];
        $ordem_Month = [];
        $desor_Month = [];
        $getLocalizedMonths = getLocalizedMonths();
        foreach ($getLocalizedMonths as $key => $value) {
            if ($value['id'] > 7 && $value['id'] < 10) {
            } else {
                $lista_Month[] = $value;
            }
        }
        foreach ($lista_Month as $index => $item) {
            if ($item['id'] > 9) {
                $ordem_Month[] = $item;
            } else {
                $desor_Month[] = $item;
            }
        }
        foreach ($desor_Month as $indexInArray => $element) {
            $ordem_Month[] = $element;
        }

        $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
        $courses = Course::with('currentTranslation')->get();
        $data = ['ordem_Month' => $ordem_Month, 'articles' => $articles, 'courses' => $courses, 'lectiveYears' => $lectiveYears, 'lectiveYearSelected' => $lectiveYearSelected];


        return view("Reports::generate-enrollment-pending-finalist")->with($data);
    }

    public function generateEnrollmentIncameEmolumentofinalist($elemento)
    {
        if ($elemento == "todosEmolument") {
            $articles = DB::table('articles as articl')->join('article_translations as art_trans', 'art_trans.article_id', '=', 'articl.id')
                ->select([
                    'articl.id as id',
                    'articl.code as code',
                    'art_trans.display_name as display_name'
                ])
                ->whereNull('articl.deleted_at')
                ->where('art_trans.active', '=', 1)
                ->orderBy('art_trans.display_name')
                ->whereIn('art_trans.display_name', ["Propina - Finalista", "Diploma", "Certificado", "Cerimônia de outorga"])
                ->distinct()
                ->get();
            return  response()->json(['data' => $articles]);
        } else {

            $lectiveYearSelected = LectiveYear::whereId($elemento)->first();

            $articles = DB::table('articles as articl')->join('article_translations as art_trans', 'art_trans.article_id', '=', 'articl.id')
                ->select([
                    'articl.id as id',
                    'articl.code as code',
                    'art_trans.display_name as display_name'
                ])
                ->whereNull('articl.deleted_at')
                ->where('art_trans.active', '=', 1)
                ->orderBy('art_trans.display_name')
                ->whereIn('art_trans.display_name', ["Propina - Finalista", "Diploma", "Certificado", "Cerimônia de outorga"])
                ->distinct()
                ->whereBetween('articl.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                ->get();
            return  response()->json(['data' => $articles]);
        }
    }

    public function enrollmentPendingWithParametersfinalist(Request $request)
    {


        switch ($request->submitButton) {
            case 'pdf':
                $institution = Institution::latest()->first();
                $titulo_documento = "Lista de Pendentes ( Finalistas )";
                $documentoGerado_documento = "Data: ";
                $documentoCode_documento = 7;
                $month = [];
                if (isset($request->month)) {
                    $request->month == "3_2020" ? $month = 3 : $month = $request->month;
                }


                $date1 =  $request->get('dataInicio_id') == null ? date('Y-m-d') : $request->get('dataInicio_id');
                $date2 =  $request->get('dataFim_id');

                $article = $request->get('article');
                $course = $request->get('curso');
                $student = $request->get('student');




                if ($request->get('dataFim_id') == null) {
                    $date2 = null;
                    $emoluments = ArticleRequest::leftJoin('article_translations as at', function ($join) {
                        $join->on('at.article_id', '=', 'article_requests.article_id');
                        $join->on('at.language_id', '=', \DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('at.active', '=', \DB::raw(true));
                    })
                        ->join('users', 'users.id', '=', 'article_requests.user_id')
                        ->leftJoin('matriculations', 'matriculations.user_id', '=', 'users.id')
                        ->leftJoin('user_parameters as u_p', function ($join) {
                            $join->on('users.id', '=', 'u_p.users_id')
                                ->where('u_p.parameters_id', 1);
                        })
                        ->join('user_courses', 'user_courses.users_id', '=', 'users.id')
                        ->leftJoin('courses_translations as ct', function ($join) {
                            $join->on('ct.courses_id', '=', 'user_courses.courses_id');
                            $join->on('ct.language_id', '=', \DB::raw(LanguageHelper::getCurrentLanguage()));
                            $join->on('ct.active', '=', \DB::raw(true));
                        })
                        ->leftJoin('user_parameters as up_meca', function ($join) {
                            $join->on('users.id', '=', 'up_meca.users_id')
                                ->where('up_meca.parameters_id', 19);
                        })
                        ->select([
                            'at.article_id as article_id',
                            'at.display_name as article_name',
                            'users.name as user_name',
                            'users.id as user_id',
                            'ct.display_name as course_name',
                            'ct.courses_id as course_id',
                            'up_meca.value as matriculation_number',
                            'matriculations.course_year as course_year',
                            'article_requests.created_at as created_at',
                            'article_requests.base_value as value'
                        ])
                        ->whereDate('article_requests.created_at', $date1) #\DB::raw('CURDATE()')
                        ->where('article_requests.status', 'pending')
                        ->when($request->get('article') != null, function ($q) use ($article) {
                            return $q->whereIn('at.article_id', $article);
                        })->when($request->get('curso') != null, function ($q) use ($course) {
                            return $q->whereIn('ct.courses_id', $course);
                        })
                        ->when($request->get('student') != null, function ($q) use ($student) {
                            return $q->whereIn('users.id', $student);
                        })

                        ->get()
                        ->groupBy(['matriculation_number', 'course_name', 'user_name']);



                    // return view("Reports::pending-articles", compact('emoluments','date1','date2'));
                    $pdf = PDF::loadView("Reports::pending-articles", compact(
                        'emoluments',
                        'date1',
                        'date2',
                        'institution',
                        'titulo_documento',
                        'documentoGerado_documento',
                        'documentoCode_documento'
                    ));
                    $pdf->setOption('margin-top', '2cm');
                    $pdf->setOption('margin-left', '1cm');
                    $pdf->setOption('margin-bottom', '3cm');
                    $pdf->setOption('margin-right', '1cm');
                    $pdf->setOption('enable-javascript', true);
                    $pdf->setOption('debug-javascript', true);
                    $pdf->setOption('javascript-delay', 1000);
                    $pdf->setOption('enable-smart-shrinking', true);
                    $pdf->setOption('no-stop-slow-scripts', true);
                    $pdf->setPaper('a4');

                    // $footer_html = view()->make('Reports::partials.enrollment-income-footer', compact('institution'))->render();
                    $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
                    $pdf->setOption('footer-html', $footer_html);
                    return $pdf->stream('ISPM_T_0002');
                } else {
                    $emoluments = DB::table('transactions as trans')
                        ->join('transaction_article_requests as trans_articl_reques', 'trans_articl_reques.transaction_id', '=', 'trans.id')
                        ->join('article_requests as article_reques', 'article_reques.id', '=', 'trans_articl_reques.article_request_id')
                        ->leftJoin('article_translations as at', function ($join) {
                            $join->on('at.article_id', '=', 'article_reques.article_id');
                            $join->on('at.language_id', '=', \DB::raw(LanguageHelper::getCurrentLanguage()));
                            $join->on('at.active', '=', \DB::raw(true));
                        })
                        ->leftJoin('articles as article', 'article.id', '=', 'at.article_id')
                        ->join('users as us', 'us.id', '=', 'article_reques.user_id')
                        ->leftJoin('user_parameters as full_name', function ($join) {
                            $join->on('us.id', '=', 'full_name.users_id')
                                ->where('full_name.parameters_id', 1);
                        })
                        ->leftJoin('user_parameters as up_meca', function ($join) {
                            $join->on('us.id', '=', 'up_meca.users_id')
                                ->where('up_meca.parameters_id', 19);
                        })
                        ->join('user_courses as uc', 'uc.users_id', '=', 'us.id')
                        ->leftJoin('courses_translations as ct', function ($join) {
                            $join->on('ct.courses_id', '=', 'uc.courses_id');
                            $join->on('ct.language_id', '=', \DB::raw(LanguageHelper::getCurrentLanguage()));
                            $join->on('ct.active', '=', \DB::raw(true));
                        })
                        ->leftJoin('users as u1', 'u1.id', '=', 'trans.created_by')
                        ->leftJoin('user_parameters as user_va', function ($join) {
                            $join->on('u1.id', '=', 'user_va.users_id')
                                ->where('user_va.parameters_id', 1);
                        })
                        ->leftJoin('transaction_info as tran_info', function ($join) {
                            $join->on('trans.id', '=', 'tran_info.transaction_id');
                        })
                        ->leftJoin('disciplines_translations as dcp', function ($join) {
                            $join->on('dcp.discipline_id', '=', 'article_reques.discipline_id');
                            $join->on('dcp.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                            $join->on('dcp.active', '=', DB::raw(true));
                        })
                        // ->leftJoin('matriculations as matriculat', function ($join) {
                        //     $join->on('matriculat.user_id', '=', 'article_reques.user_id');
                        // })

                        ->select([
                            'tran_info.transaction_id as transaction_id_info',
                            'trans.id as transaction_id',
                            'trans.type as type',
                            // 'matriculat.id as id',
                            // 'matriculat.course_year as year',
                            // 'matriculat.lective_year as lective_year',
                            'article_reques.id as id_article_requests',
                            'at.display_name as article_name',
                            'full_name.value as user_name',
                            'ct.display_name as course_name',
                            'up_meca.value as matriculation_number',
                            'u1.name as created_by_user',
                            'at.article_id as article_id',
                            'u1.id as user_id',
                            'dcp.display_name as discplina_display_name',
                            'ct.courses_id as course_id',
                            'article_reques.created_at as created_at',
                            'article_reques.base_value as value',
                            'article_reques.year as article_year',
                            'article_reques.month as article_month',
                            'article_reques.status as status',
                            'trans_articl_reques.value as price'
                        ])
                        // ->whereBetween('matriculat.created_at', [$date1, $date2])
                        ->whereNull('article_reques.deleted_at')
                        ->whereNull('article_reques.deleted_by')
                        ->whereNull('trans.deleted_at')
                        ->distinct('article_reques.id')
                        ->where('trans.data_from', '!=', 'Estorno')
                        ->where('article_reques.status', '!=', 'total')
                        ->when($request->get('student') != null, function ($q) use ($student) {
                            return $q->whereIn('us.id', $student);
                        })
                        ->when($request->get('curso') != null, function ($q) use ($course) {
                            return $q->whereIn('ct.courses_id', $course);
                        })
                        ->when($request->get('article') != null, function ($q) use ($article) {
                            return $q->whereIn('at.article_id', $article);
                        })
                        ->when(count($month) == 1  && in_array('3_2020', $month) && $month != 0, function ($q) use ($month) {
                            $q->where('article_reques.year', '=', "2020");
                            return $q->whereIn('article_reques.month', $month);
                        })
                        ->when(count($month) > 0  && in_array('3_2020', $month) && in_array('3', $month) && $month != 0, function ($q) use ($month) {
                            return $q->whereIn('article_reques.month', $month);
                        })
                        ->when(count($month) > 0  && in_array('3_2020', $month) == false && in_array('3', $month) == false && $month != 0, function ($q) use ($month) {
                            return $q->whereIn('article_reques.month', $month);
                        })
                        ->when(count($month) > 0  && in_array('3', $month) == true && $month != 0, function ($q) use ($month) {
                            $q->where('article_reques.year', '>', "2020");
                            return $q->whereIn('article_reques.month', $month);
                        })
                        ->when(count($month) > 0  && in_array('3', $month) == false && $month != 0, function ($q) use ($month) {
                            return $q->whereIn('article_reques.month', $month);
                        })
                        ->orderBy('article_reques.year', 'ASC')
                        ->orderBy('article_reques.month', 'ASC')
                        ->orderBy('id_article_requests', 'asc')
                        ->get()
                        ->groupBy(['matriculation_number', 'course_name', 'user_name', 'id_article_requests']);


                    $emoluments = collect($emoluments)->map(function ($item) {
                        $balance = 0;
                        $trans_info = [];
                        foreach ($item  as  $curso) {
                            foreach ($curso  as  $nome_student) {
                                foreach ($nome_student as $key => $value) {
                                    $balance = 0;
                                    $trans_info = [];
                                    if (count($value) > 1) {
                                        foreach ($value as  $element) {
                                            $element->transaction_id_info == null ? $trans_info[] = 'N-info' : $trans_info[] = 'S_info';
                                        }
                                        if (in_array('S_info', $trans_info)) {
                                            foreach ($value  as  $element) {
                                                if ($element->price == $element->value || $element->transaction_id_info != null) {
                                                    $op = $element->type == 'debit' ? -1 : 1;
                                                    $balance += ($element->price * $op);
                                                }
                                            }
                                        } else {
                                            $id_article_requests = null;
                                            foreach ($value as $element) {
                                                if ($id_article_requests == null) {
                                                    $id_article_requests = $element->id_article_requests;
                                                    $balance += $element->value;
                                                } else if ($id_article_requests != $element->id_article_requests) {
                                                    $balance += $element->value;
                                                }
                                            }
                                        }
                                        $id_article_requests = null;
                                        foreach ($value as $chave => $element) {
                                            if ($id_article_requests == null) {
                                                $id_article_requests = $element->id_article_requests;
                                                $element->{'balance'} = $balance;
                                            } else if ($id_article_requests == $element->id_article_requests) {
                                                unset($value[$chave]);
                                            }
                                        }
                                    } else {
                                        foreach ($value as $chave => $element) {
                                            $element->{'balance'} = $element->value;
                                        }
                                    }
                                }
                            }
                        }
                        return $item;
                    });



                    $finalist = true;
                    //return view("Reports::pending-articles", compact('emoluments', 'date1', 'date2'));
                    $pdf = PDF::loadView("Reports::pending-articles", compact(
                        'emoluments',
                        'date1',
                        'date2',
                        'institution',
                        'finalist',
                        'titulo_documento',
                        'documentoGerado_documento',
                        'documentoCode_documento'
                    ));
                    $pdf->setOption('margin-top', '1.5mm');
                    $pdf->setOption('margin-left', '1.8mm');
                    $pdf->setOption('margin-bottom', '2cm');
                    $pdf->setOption('margin-right', '1.8mm');
                    $pdf->setOption('enable-javascript', true);
                    $pdf->setOption('debug-javascript', true);
                    $pdf->setOption('javascript-delay', 1000);
                    $pdf->setOption('enable-smart-shrinking', true);
                    $pdf->setOption('no-stop-slow-scripts', true);
                    $pdf->setPaper('a4', 'landscape');


                    // $footer_html = view()->make('Reports::partials.enrollment-income-footer', compact('institution'))->render();
                    $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
                    $pdf->setOption('footer-html', $footer_html);
                    return $pdf->stream('ISPM_T_0002');
                }

                break;
            case 'excel':
                return Excel::download(new PendingExport, 'ISPM_T_0002.xlsx');
                break;
            default:
                # code...
                break;
        }
    }






    public function enrollmentPendingWithParameters(Request $request)
    {

        $rules = $request->get('rules');

        switch ($request->submitButton) {
            case 'pdf':
                $institution = Institution::latest()->first();
                $titulo_documento = "Lista de Pendentes";
                $documentoGerado_documento = "Data: ";
                $documentoCode_documento = 7;
                $month = [];
                if (isset($request->month)) {
                    $request->month == "3_2020" ? $month = 3 : $month = $request->month;
                }


                $date1 =  $request->get('dataInicio_id') == null ? date('Y-m-d') : $request->get('dataInicio_id');
                $date2 =  $request->get('dataFim_id');

                $article = $request->get('article');
                $course = $request->get('curso');
                $student = $request->get('student');
                $classes = $request->get('classes');


                if ($request->get('dataFim_id') == null) {

                    $emoluments =  DocsReportsUtil::getArticleRequestStudents($date1, $date2, $course, $classes, $article, $rules, $student, $month);

                    // return view("Reports::pending-articles", compact('emoluments','date1','date2'));
                    $pdf = PDF::loadView("Reports::pending-articles", compact(
                        'emoluments',
                        'date1',
                        'date2',
                        'institution',
                        'titulo_documento',
                        'documentoGerado_documento',
                        'documentoCode_documento'
                    ));
                    $pdf->setOption('margin-top', '2cm');
                    $pdf->setOption('margin-left', '1cm');
                    $pdf->setOption('margin-bottom', '3cm');
                    $pdf->setOption('margin-right', '1cm');
                    $pdf->setOption('enable-javascript', true);
                    $pdf->setOption('debug-javascript', true);
                    $pdf->setOption('javascript-delay', 1000);
                    $pdf->setOption('enable-smart-shrinking', true);
                    $pdf->setOption('no-stop-slow-scripts', true);
                    $pdf->setPaper('a4');

                    // $footer_html = view()->make('Reports::partials.enrollment-income-footer', compact('institution'))->render();
                    $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
                    $pdf->setOption('footer-html', $footer_html);
                    return $pdf->stream('ISPM_T_0002');
                } else {

                    $emoluments =  DocsReportsUtil::getArticleRequestStudents($date1, $date2, $course, $classes, $article, $rules, $student, $month);

                    $out_art_requests = [];
                    $emoluments = collect($emoluments)->map(function ($item) use (&$out_art_requests) {
                        $balance = 0;
                        $trans_info = [];
                        foreach ($item  as  $curso) {
                            foreach ($curso  as  $nome_student) {
                                foreach ($nome_student as $key => $value) {
                                    $balance = 0;
                                    $trans_info = [];



                                    foreach ($value as  $element) {
                                        $element->final_value = isset($element->rule_value) ? $element->rule_value : $element->value;
                                    }


                                    if (count($value) > 1) {
                                        foreach ($value as  $element) {
                                            $element->transaction_id_info == null ? $trans_info[] = 'N-info' : $trans_info[] = 'S_info';
                                        }

                                        if (in_array('S_info', $trans_info)) {
                                            $t_payments = 0;
                                            foreach ($value  as  $element) {

                                                if ($element->price == $element->value || $element->transaction_id_info != null) {
                                                    $op = $element->type == 'debit' ? -1 : 1;
                                                    $t_payments += $element->type == 'payment' &&  $element->type == 'adjust' ?  $element->price : 0;
                                                    $balance += ($element->price * $op);
                                                }
                                            }
                                            if ($t_payments === $element->final_value)
                                                array_push($out_art_requests, $key);
                                        } else {
                                            $id_article_requests = null;
                                            foreach ($value as $element) {
                                                if ($id_article_requests == null) {
                                                    $id_article_requests = $element->id_article_requests;
                                                    $balance += isset($element->rule_value) ? $element->rule_value : $element->value;
                                                } else if ($id_article_requests != $element->id_article_requests) {
                                                    $balance += isset($element->rule_value) ? $element->rule_value : $element->value;
                                                }
                                            }
                                        }
                                        $id_article_requests = null;
                                        foreach ($value as $chave => $element) {
                                            if ($id_article_requests == null) {
                                                $id_article_requests = $element->id_article_requests;
                                                $element->{'balance'} = $balance;
                                            } else if ($id_article_requests == $element->id_article_requests) {
                                                unset($value[$chave]);
                                            }
                                        }
                                    } else {

                                        foreach ($value as $chave => $element) {
                                            $element->{'balance'} = isset($element->rule_value) ? $element->rule_value : $element->value;
                                        }
                                    }
                                }
                            }
                        }
                        return $item;
                    });


                    //return view("Reports::pending-articles", compact('emoluments', 'date1', 'date2'));
                    $pdf = PDF::loadView("Reports::pending-articles", compact(
                        'emoluments',
                        'date1',
                        'date2',
                        'institution',
                        'titulo_documento',
                        'documentoGerado_documento',
                        'documentoCode_documento',
                        'out_art_requests'
                    ));
                    $pdf->setOption('margin-top', '1.5mm');
                    $pdf->setOption('margin-left', '1.8mm');
                    $pdf->setOption('margin-bottom', '2cm');
                    $pdf->setOption('margin-right', '1.8mm');
                    $pdf->setOption('enable-javascript', true);
                    $pdf->setOption('debug-javascript', true);
                    $pdf->setOption('javascript-delay', 1000);
                    $pdf->setOption('enable-smart-shrinking', true);
                    $pdf->setOption('no-stop-slow-scripts', true);
                    $pdf->setPaper('a4', 'landscape');


                    // $footer_html = view()->make('Reports::partials.enrollment-income-footer', compact('institution'))->render();
                    $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
                    $pdf->setOption('footer-html', $footer_html);
                    return $pdf->stream('ISPM_T_0002');
                }

                break;
            case 'excel':
                $institution = Institution::latest()->first();
                $titulo_documento = "Lista de Pendentes";
                $documentoGerado_documento = "Data: ";
                $documentoCode_documento = 7;
                $month = [];
                if (isset($request->month)) {
                    $request->month == "3_2020" ? $month = 3 : $month = $request->month;
                }

                $date1 =  $request->get('dataInicio_id') == null ? date('Y-m-d') : $request->get('dataInicio_id');
                $date2 =  $request->get('dataFim_id');

                $article = $request->get('article');
                $course = $request->get('curso');
                $student = $request->get('student');
                $classes = $request->get('classes');

                if ($request->get('dataFim_id') == null) {

                    $emoluments =  DocsReportsUtil::getArticleRequestStudents($date1, $date2, $course, $classes, $article, $rules, $student, $month);

                    $pdf = PDF::loadView("Reports::pending-articles", compact(
                        'emoluments',
                        'date1',
                        'date2',
                        'institution',
                        'titulo_documento',
                        'documentoGerado_documento',
                        'documentoCode_documento'
                    ));
                    $pdf->setOption('margin-top', '2cm');
                    $pdf->setOption('margin-left', '1cm');
                    $pdf->setOption('margin-bottom', '3cm');
                    $pdf->setOption('margin-right', '1cm');
                    $pdf->setOption('enable-javascript', true);
                    $pdf->setOption('debug-javascript', true);
                    $pdf->setOption('javascript-delay', 1000);
                    $pdf->setOption('enable-smart-shrinking', true);
                    $pdf->setOption('no-stop-slow-scripts', true);
                    $pdf->setPaper('a4');

                    // $footer_html = view()->make('Reports::partials.enrollment-income-footer', compact('institution'))->render();
                    $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
                    $pdf->setOption('footer-html', $footer_html);
                    return $pdf->stream('ISPM_T_0002');
                } else {

                    $emoluments =  DocsReportsUtil::getArticleRequestStudents($date1, $date2, $course, $classes, $article, $rules, $student, $month);

                    $out_art_requests = [];
                    $emoluments = collect($emoluments)->map(function ($item) use (&$out_art_requests) {
                        $balance = 0;
                        $trans_info = [];
                        foreach ($item  as  $curso) {
                            foreach ($curso  as  $nome_student) {
                                foreach ($nome_student as $key => $value) {
                                    $balance = 0;
                                    $trans_info = [];



                                    foreach ($value as  $element) {
                                        $element->final_value = isset($element->rule_value) ? $element->rule_value : $element->value;
                                    }


                                    if (count($value) > 1) {
                                        foreach ($value as  $element) {
                                            $element->transaction_id_info == null ? $trans_info[] = 'N-info' : $trans_info[] = 'S_info';
                                        }

                                        if (in_array('S_info', $trans_info)) {
                                            $t_payments = 0;
                                            foreach ($value  as  $element) {

                                                if ($element->price == $element->value || $element->transaction_id_info != null) {
                                                    $op = $element->type == 'debit' ? -1 : 1;
                                                    $t_payments += $element->type == 'payment' &&  $element->type == 'adjust' ?  $element->price : 0;
                                                    $balance += ($element->price * $op);
                                                }
                                            }
                                            if ($t_payments === $element->final_value)
                                                array_push($out_art_requests, $key);
                                        } else {
                                            $id_article_requests = null;
                                            foreach ($value as $element) {
                                                if ($id_article_requests == null) {
                                                    $id_article_requests = $element->id_article_requests;
                                                    $balance += isset($element->rule_value) ? $element->rule_value : $element->value;
                                                } else if ($id_article_requests != $element->id_article_requests) {
                                                    $balance += isset($element->rule_value) ? $element->rule_value : $element->value;
                                                }
                                            }
                                        }
                                        $id_article_requests = null;
                                        foreach ($value as $chave => $element) {
                                            if ($id_article_requests == null) {
                                                $id_article_requests = $element->id_article_requests;
                                                $element->{'balance'} = $balance;
                                            } else if ($id_article_requests == $element->id_article_requests) {
                                                unset($value[$chave]);
                                            }
                                        }
                                    } else {

                                        foreach ($value as $chave => $element) {
                                            $element->{'balance'} = isset($element->rule_value) ? $element->rule_value : $element->value;
                                        }
                                    }
                                }
                            }
                        }
                        return $item;
                    });

                    $data = [
                        'emoluments' => $emoluments,
                        'date1' => $date1,
                        'date2' => $date2,
                        'institution' => $institution,
                        'titulo_documento' => $titulo_documento,
                        'documentoGerado_documento' => $documentoGerado_documento,
                        'documentoCode_documento' => $documentoCode_documento,
                        'out_art_requests' => $out_art_requests
                    ];
                    return Excel::download(new PendingExport($data), 'Lista_de_pendentes' . now() . '.xlsx');
                }
                break;
            default:
                # code...
                break;
        }
    }





    public function pendingArticles()
    {
        $institution = Institution::latest()->first();
        $titulo_documento = "Lista de Pendentes";
        $documentoGerado_documento = "Data: ";
        $documentoCode_documento = 7;

        $emoluments = ArticleRequest::leftJoin('article_translations as at', function ($join) {
            $join->on('at.article_id', '=', 'article_requests.article_id');
            $join->on('at.language_id', '=', \DB::raw(LanguageHelper::getCurrentLanguage()));
            $join->on('at.active', '=', \DB::raw(true));
        })
            ->join('users', 'users.id', '=', 'article_requests.user_id')
            ->join('matriculations', 'matriculations.user_id', '=', 'users.id')
            ->leftJoin('user_parameters as u_p', function ($join) {
                $join->on('users.id', '=', 'u_p.users_id')
                    ->where('u_p.parameters_id', 1);
            })
            ->join('user_courses', 'user_courses.users_id', '=', 'users.id')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'user_courses.courses_id');
                $join->on('ct.language_id', '=', \DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', \DB::raw(true));
            })
            ->leftJoin('user_parameters as up_meca', function ($join) {
                $join->on('users.id', '=', 'up_meca.users_id')
                    ->where('up_meca.parameters_id', 19);
            })
            ->select([
                'at.article_id as article_id',
                'at.display_name as article_name',
                'users.name as user_name',
                'ct.display_name as course_name',
                'ct.courses_id as course_id',
                'up_meca.value as matriculation_number',
                'matriculations.course_year as course_year',
                'article_requests.created_at as created_at',
                'article_requests.base_value as value'
            ])
            ->whereDate('article_requests.created_at', '2020-02-05') #\DB::raw('CURDATE()')
            ->where('article_requests.status', 'pending')
            ->get()
            ->groupBy(['matriculation_number', 'course_name', 'user_name']);


        return view("Reports::pending-articles", compact(
            'emoluments',
            'institution',
            'titulo_documento',
            'documentoGerado_documento',
            'documentoCode_documento'
        ));

        /*$pdf = PDF::loadView("Reports::pending-articles", compact('emoluments'));
                        $pdf->setOption('margin-top', '2cm');
                        $pdf->setOption('margin-left', '1cm');
                        $pdf->setOption('margin-bottom', '3cm');
                        $pdf->setOption('margin-right', '1cm');
                        $pdf->setOption('enable-javascript', true);
                        $pdf->setOption('debug-javascript', true);
                        $pdf->setOption('javascript-delay', 1000);
                        $pdf->setOption('enable-smart-shrinking', true);
                        $pdf->setOption('no-stop-slow-scripts', true);
                        $pdf->setPaper('a4');

                        $footer_html = view()->make('Reports::partials.enrollment-income-footer')->render();
                        $pdf->setOption('footer-html', $footer_html);
         return $pdf->stream('ISPM_T_0002');*/
    }


    protected function formatUserName($user)
    {
        $fullNameParameter = $user->parameters->firstWhere('code', 'nome');
        $fullName = $fullNameParameter && $fullNameParameter->pivot->value ?
            $fullNameParameter->pivot->value : $user->name;

        $studentNumberParameter = $user->parameters->firstWhere('code', 'n_mecanografico');
        $studentNumber = $studentNumberParameter && $studentNumberParameter->pivot->value ?
            $studentNumberParameter->pivot->value : "000";

        return "$fullName #$studentNumber ($user->email)";
    }

    public function generateEnrollmentIncameEmolumento($elemento)
    {
        if ($elemento == "todosEmolument") {
            $articles = DB::table('articles as articl')->join('article_translations as art_trans', 'art_trans.article_id', '=', 'articl.id')
                ->select([
                    'articl.id as id',
                    'articl.code as code',
                    'art_trans.display_name as display_name'
                ])
                ->whereNull('articl.deleted_at')
                ->where('art_trans.active', '=', 1)
                ->orderByRaw('articl.deleted_at DESC')
                ->distinct()
                ->get();
            return  response()->json(['data' => $articles]);
        } else {

            $lectiveYearSelected = LectiveYear::whereId($elemento)->first();

            $articles = DB::table('articles as articl')->join('article_translations as art_trans', 'art_trans.article_id', '=', 'articl.id')
                ->select([
                    'articl.id as id',
                    'articl.code as code',
                    'art_trans.display_name as display_name'
                ])
                ->whereNull('articl.deleted_at')
                ->where('art_trans.active', '=', 1)
                ->orderByRaw('articl.deleted_at DESC')
                ->distinct()
                ->whereBetween('articl.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                ->get();
            return  response()->json(['data' => $articles]);
        }
    }


    # Listar 

    public function getStudent($curso, $classes)
    {

        //    return $classes;
        $curso = explode(",", $curso);
        $classes = explode(",", $classes);


        $student = DB::table('matriculation_classes as mc')
            ->join("matriculations as mt", "mt.id", "=", "mc.matriculation_id")
            ->whereIn('mc.class_id', $classes)
            ->leftJoin('users as u0', 'u0.id', '=', 'mt.user_id')
            ->leftJoin('user_parameters as u_p', function ($join) {
                $join->on('mt.user_id', '=', 'u_p.users_id')
                    ->where('u_p.parameters_id', 1);
            })

            ->leftJoin('user_parameters as u_p0', function ($join) {
                $join->on('mt.user_id', '=', 'u_p0.users_id')
                    ->where('u_p0.parameters_id', 19);
            })
            ->orderBy('u_p.value')
            ->select([
                'u_p.value as display_name',
                'mt.user_id as id',
                'u0.email as email',
                'u_p0.value as meca',
            ])
            ->get();

        $student = $student->unique('id');

        return ["data" => $student];
    }

    public function getArticleRules($lectivo, $classes, $months, $articles)
    {


        $months = explode(",", $months);
        $articles = explode(",", $articles);
        $classes = explode(',', $classes);

        $classes = DB::table('classes')
            ->whereNull('deleted_at')
            ->whereIn('id', $classes)
            ->get();

        $curricularYears = $classes
            ->pluck('year')
            ->unique()
            ->toArray();

        $periods = $classes
            ->pluck('schedule_type_id')
            ->unique()
            ->toArray();


        $articlesUtil = new ArticlesUtil();

        $articleRules =  $articlesUtil->getArticleRules($lectivo)
            ->whereIn('id_articles', $articles)
            ->whereIn('mes', $months)
            ->whereIn('ano_curricular', $curricularYears)
            ->whereIn('schedule_type_id', $periods);

        $protocolo = true;
        $articleRulesProtocolo = $articlesUtil->getArticleRules($lectivo, $protocolo)
            ->whereIn('id_articles', $articles);

        $articleRules = $articleRules->concat($articleRulesProtocolo);


        $finalArticleRules = [];
        $i = 0;

        $articleRulesArray = array_values(collect($articleRules)->all()); // Garante índices sequenciais
        $count = count($articleRulesArray);

        for ($i = 0; $i < $count; $i++) {
            $rule = $articleRulesArray[$i];

            $acronym = isset($rule->acronym) ? "({$rule->acronym})" : 'Abreviatura';

            $company = isset($rule->company)
                ? "({$rule->company})"
                : (!isset($rule->mes) ? 'Entidade Protocolo' : '');

            $period = isset($rule->periodo_name)
                ? "({$rule->periodo_name})"
                : (!isset($rule->company) ? 'Período' : '');

            $year = isset($rule->ano_curricular)
                ? "({$rule->ano_curricular}º)"
                : (!isset($rule->company) ? 'Ano Curricular' : '');

            $month = isset($rule->mes)
                ? "({$this->nomeDoMes($rule->mes)})"
                : (!isset($rule->company) ? 'Mês' : '');

            $value = isset($rule->valor) ? "({$rule->valor})" : 'Valor';

            $name = "$acronym - $month $company  $year $period $value";

            $finalArticleRules[$i] = $rule->id_ruleArtc . '+' . $name;
        }



        return [
            "data" => $finalArticleRules
        ];
    }

    private function nomeDoMes($numeroMes)
    {
        $meses = [
            1 => 'Janeiro',
            2 => 'Fevereiro',
            3 => 'Março',
            4 => 'Abril',
            5 => 'Maio',
            6 => 'Junho',
            7 => 'Julho',
            8 => 'Agosto',
            9 => 'Setembro',
            10 => 'Outubro',
            11 => 'Novembro',
            12 => 'Dezembro'
        ];

        return $meses[$numeroMes] ?? 'Mês inválido';
    }
    public function getClasses($curso, $year)
    {


        $curso = explode(",", $curso);
        $year_s = explode(",", $year);






        $classes = DB::table("courses")
            ->leftJoin('classes', 'classes.courses_id', '=', 'courses.id')
            ->whereIn("courses.id", $curso)
            ->where("classes.lective_year_id", $year_s[0])
            ->orderBy("classes.year")
            ->select([
                'classes.id',
                'classes.code',
            ])
            ->get();

        return ["data" => $classes];
    }

    public function getStudentFinalist($curso, $year)
    {
        $curso = explode(",", $curso);

        $students = DB::table('matriculation_finalist as mf')
            ->Join('users as u0', 'mf.user_id', '=', 'u0.id')

            ->leftJoin('user_parameters as u_p', function ($join) {
                $join->on('mf.user_id', '=', 'u_p.users_id')
                    ->where('u_p.parameters_id', 1);
            })
            ->leftJoin('user_parameters as u_p0', function ($join) {
                $join->on('mf.user_id', '=', 'u_p0.users_id')
                    ->where('u_p0.parameters_id', 19);
            })
            ->select([
                "mf.user_id as id",
                "u_p0.value as meca",
                "u_p.value as display_name",
                "u0.email",
            ])
            ->whereIn("mf.id_curso", $curso)
            ->where("mf.year_lectivo", $year)
            ->whereNull("mf.deleted_at")
            ->orderBy("u_p.value")
            ->get();

        return ["data" => $students];
    }

    public function pendinganulate()
    {
        return view("Reports::anulate.index");
    }
    public function pendingDelete($id, $year)
    {



        $lective = LectiveYear::whereId($year)
            // ->select(['end_date'])
            ->first();

        $emolumentos = DB::table("article_requests")
            ->where("user_id", $id)
            ->where("status", "pending")
            // ->where("month",">",date("m"))
            ->whereNotNull("month")
            ->whereNotNull("year")
            ->where("discipline_id", "")
            ->whereNull("deleted_at")
            ->select(['id', 'month', 'year'])
            // ->whereBetween('article_requests.created_at', [$lective->start_date, $lective->end_date])
            ->get();


        $final = $lective->end_date;

        $emo = collect($emolumentos)->groupBy('id')->map(function ($item, $key) use ($final) {

            $mes = $item[0]->month;
            $ano = $item[0]->year;
            $qtd_mes = strlen($mes);

            if (($qtd_mes == 1) && ($mes < 10)) {
                $mes = "0" . $mes;
            }


            $dataactual = date("Y-m");
            $dataemo = $ano . "-" . $mes;
            $final = explode("-", $final);
            $final = $final[0] . "-" . $final[1];

            if (($dataemo > $dataactual) && ($dataemo <= $final)) {
                return $item[0]->id;
            }
        });


        foreach ($emo as $item) {
            $deletar = DB::table('article_requests')
                ->where('id', "=", $item)
                ->update(
                    [
                        "deleted_by" => auth()->user()->id,
                        "deleted_at" => Carbon::now()
                    ]
                );

            if ($item == "" || $item == 0 || $item == null) {
            } else {
                $arti = DB::table('article_requests as ar')
                    ->leftJoin('user_parameters as up', 'up.users_id', "=", "ar.user_id")
                    ->leftJoin('article_translations as at', 'at.article_id', "=", "ar.article_id")
                    ->leftJoin('articles as art', 'art.id', "=", "ar.article_id")
                    ->where('up.parameters_id', 1)
                    ->where('at.active', 1)
                    ->select([
                        "ar.id",
                        "ar.user_id",
                        "at.display_name as emolumento"
                    ])
                    ->where('ar.id', $item)
                    ->first();

                $obs = "O emolumento '" . $arti->emolumento . "' foi eliminado automáticamente por razões de
                     anulação de matrícula...";
                $Observation = DB::table('current_account_observations')
                    ->insert([
                        'user_id' => $arti->user_id,
                        'observation' => $obs,
                        'file' => "Sem arquivo anexado...",
                    ]);
            }
        }



        // return "".
        // "Nenhum emolumento encontrado!!!".
        // "<br> Ao efectuar a anulação de matrícula "
        // .$final;


    }

    public static function getTurma($user, $lective_year, $year)
    {
        $classes = DB::table("matriculation_classes as mc")
            ->Join('classes', 'classes.id', '=', 'mc.class_id')
            ->where("mc.matriculation_id", $user)
            ->where("classes.year", $year)
            ->where("classes.lective_year_id", $lective_year)
            ->select([
                'classes.code',
            ])
            ->get();
        $turmas = array();
        foreach ($classes as $item) {
            array_push($turmas, $item->code);
        }


        if (count($classes)) {
            return implode($turmas);
        } else {
            return "";
        }
    }

    public function extract($student, $year)
    {

        if ($year == 0) {
            Toastr::warning(__('Nenhuma matrícula foi detectada neste ano lectivo'), __('toastr.warning'));
            return redirect()->back();
        }


        //Avaliar datas vazias
        //    return $request;
        $valormatriculaDupl = [];
        $antransoLectivoArticl = [];
        $matricula = 0;
        $transacao = 0;
        $qtdRegisto = 0;
        $objetoValorTransacao = [];
        $objetoSaldo_cartera = [];
        $valorRepetido = [];


        $institution = Institution::latest()->first();
        $titulo_documento = "EXTRACTO DE CONTA";
        $documentoGerado_documento = "Data da Transação: ";
        $documentoCode_documento = 8;

        $repeticao = 0;


        // consulta dos emolumentos e dos alunos.
        $getTransaction = DB::table('transactions as trans')
            ->leftjoin('transaction_article_requests as trans_articl_reques', 'trans_articl_reques.transaction_id', '=', 'trans.id')
            ->leftjoin('article_requests as article_reques', 'article_reques.id', '=', 'trans_articl_reques.article_request_id')
            ->leftJoin('article_translations as at', function ($join) {
                $join->on('at.article_id', '=', 'article_reques.article_id');
                $join->on('at.language_id', '=', \DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('at.active', '=', \DB::raw(true));
            })
            ->leftJoin('articles as article', 'article.id', '=', 'at.article_id')
            ->join('users as us', 'us.id', '=', 'article_reques.user_id')
            ->leftJoin('user_parameters as full_name', function ($join) {
                $join->on('us.id', '=', 'full_name.users_id')
                    ->where('full_name.parameters_id', 1);
            })
            ->leftJoin('user_parameters as up_meca', function ($join) {
                $join->on('us.id', '=', 'up_meca.users_id')
                    ->where('up_meca.parameters_id', 19);
            })
            ->join('user_courses as uc', 'uc.users_id', '=', 'us.id')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'uc.courses_id');
                $join->on('ct.language_id', '=', \DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', \DB::raw(true));
            })
            ->leftJoin('users as u1', 'u1.id', '=', 'trans.created_by')
            ->leftJoin('user_parameters as user_va', function ($join) {
                $join->on('u1.id', '=', 'user_va.users_id')
                    ->where('user_va.parameters_id', 1);
            })
            ->leftJoin('transaction_receipts as recibo', function ($join) {
                $join->on('recibo.transaction_id', '=', 'trans.id');
            })
            ->leftJoin('historic_user_balance as historic_saldo', function ($join) {
                $join->on('historic_saldo.id_transaction', '=', 'trans.id');
            })
            ->leftjoin('disciplines as dc', 'dc.id', '=', 'article_reques.discipline_id')
            ->leftJoin('disciplines_translations as dct', function ($join) {
                $join->on('dct.discipline_id', '=', 'dc.id');
                $join->on('dct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dct.active', '=', DB::raw(true));
            })
            ->select([
                'trans.id as transaction_id',
                'trans.created_at as created_atranst',
                'article.created_at as created_article',
                'trans.type as transaction_type',
                'article_reques.id as id_article_requests',
                'article_reques.month as article_month',
                'article_reques.year as article_year',
                'at.display_name as article_name',
                'full_name.value as full_name',
                'recibo.code as recibo',
                'recibo.path as path',
                'ct.display_name as course_name',
                'up_meca.value as matriculation_number',
                'u1.name as created_by_user',
                'historic_saldo.valor_credit as valorSaldo_credit',
                'trans_articl_reques.value as price',
                'dct.display_name as name_discipline',
                'dc.code as code_discipline',
            ])
            ->distinct('trans.transaction_id')
            ->whereIn('trans.type', ['payment', 'adjust'])
            ->where('recibo.path', '!=', null)
            ->where('trans.data_from', '!=', 'estorno')
            ->where('historic_saldo.data_from', '=', null)
            ->where('article_reques.user_id', '=', $student)
            ->where('article.anoLectivo', '=', $year)
            ->orderBy('id_article_requests', 'asc')
            ->get()
            ->groupBy(['article_name', 'course_name']);

        $getTransaction = collect($getTransaction)->map(function ($item) {
            foreach ($item as $key => $value) {
                foreach ($value as $k => $valor) {
                    $array = explode('-', $valor->path);
                    $expldeSttring = explode('.', $array[1] . '-' . $array[2]);
                    $valor->recibo = $expldeSttring[0];
                }
            }
            return $item;
        });


        // consultar os bancos e referencias de cada respentiva transacaõ
        $getInfornBanco = DB::table('transactions as trans')
            ->join('transaction_article_requests as trans_articl_reques', 'trans_articl_reques.transaction_id', '=', 'trans.id')
            ->join('article_requests as article_reques', 'article_reques.id', '=', 'trans_articl_reques.article_request_id')
            ->leftJoin('article_translations as at', function ($join) {
                $join->on('at.article_id', '=', 'article_reques.article_id');
                $join->on('at.language_id', '=', \DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('at.active', '=', \DB::raw(true));
            })
            ->join('articles as article', 'article.id', '=', 'at.article_id')
            ->join('users as us', 'us.id', '=', 'article_reques.user_id')
            ->join('transaction_info as info_trans', 'info_trans.transaction_id', '=', 'trans.id')
            ->join('banks as bank', 'info_trans.bank_id', '=', 'bank.id')
            ->leftJoin('historic_user_balance as historic_saldo', function ($join) {
                $join->on('historic_saldo.id_transaction', '=', 'trans.id');
            })
            ->select([
                'trans.id as transaction_id',
                'trans.value as value_totalTtrans',
                'article_reques.id as id_article_requests',
                'article_reques.month as article_month',
                'article_reques.year as article_year',
                'bank.display_name as bank_name',
                'bank.id as id_bank',
                'info_trans.value as valorreferencia',
                'historic_saldo.valor_credit as valorSaldo_credit',
                'info_trans.reference as reference'
            ])
            ->distinct('trans.transaction_id')
            ->where('article_reques.user_id', '=', $student)
            ->where('article.anoLectivo', '=', $year)
            ->whereIn('trans.type', ['payment', 'adjust'])
            ->where('trans.data_from', '!=', 'estorno')
            ->orderBy('id_article_requests', 'asc')
            ->get();



        // estrutura que pega o valor total de cada transação e o saldo em carteira utilizado na respectiva transacão
        foreach ($getInfornBanco as  $key => $item) {
            if ($repeticao == 0) {
                $repeticao = $item->transaction_id;
                $objetoValorTransacao[] = (object) ['transaction_id' => $item->transaction_id, 'valorTotal_trans' => $item->value_totalTtrans];
                $objetoSaldo_cartera[] = (object) ['transaction_id' => $item->transaction_id, 'valor_saldo' => $item->valorSaldo_credit];
                $valorRepetido[] = $item->transaction_id;
            }
            if (in_array($item->transaction_id, $valorRepetido)) {
            } else {
                $valorRepetido[] = $item->transaction_id;
                $repeticao = $item->transaction_id;
                $objetoValorTransacao[] = (object) ['transaction_id' => $item->transaction_id, 'valorTotal_trans' => $item->value_totalTtrans];
                $objetoSaldo_cartera[] = (object) ['transaction_id' => $item->transaction_id, 'valor_saldo' => $item->valorSaldo_credit];
            }
        }

        $LY = DB::table('lective_year_translations')
            ->join('lective_years as ly', 'ly.id', '=', 'lective_year_translations.lective_years_id')
            ->where("active", 1)
            ->where("lective_years_id", $year)
            ->first();

        $students = DB::table('matriculations as mt')
            ->join("user_courses as uc", "uc.users_id", "=", "mt.user_id")
            ->join("courses_translations as ct", "ct.courses_id", "=", "uc.courses_id")
            ->leftJoin('users as u0', 'u0.id', '=', 'mt.user_id')
            ->leftJoin('user_parameters as u_p', function ($join) {
                $join->on('mt.user_id', '=', 'u_p.users_id')
                    ->where('u_p.parameters_id', 1);
            })
            ->leftJoin('user_parameters as u_p0', function ($join) {
                $join->on('mt.user_id', '=', 'u_p0.users_id')
                    ->where('u_p0.parameters_id', 19);
            })
            ->orderBy('u_p.value')
            ->where('mt.user_id', $student)
            ->where('mt.lective_year', $year)
            ->where('ct.active', 1)
            ->select([
                'u_p.value as display_name',
                'mt.user_id as id',
                'mt.course_year as year',
                'u0.email as email',
                'u_p0.value as meca',
                'mt.lective_year',
                'mt.id as mt_id',
                'ct.display_name as course',
            ])
            ->first();

        if (!isset($students->meca)) {
            Toastr::warning(__('Nenhuma matrícula foi detectada neste ano lectivo'), __('toastr.warning'));
            return redirect()->back();
        }

        # PENDING

        $emolumento = DB::table('transactions as trans')
            ->join('transaction_article_requests as trans_articl_reques', 'trans_articl_reques.transaction_id', '=', 'trans.id')
            ->join('article_requests as article_reques', 'article_reques.id', '=', 'trans_articl_reques.article_request_id')
            ->leftJoin('article_translations as at', function ($join) {
                $join->on('at.article_id', '=', 'article_reques.article_id');
                $join->on('at.language_id', '=', \DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('at.active', '=', \DB::raw(true));
            })
            ->leftJoin('articles as article', 'article.id', '=', 'at.article_id')
            ->join('users as us', 'us.id', '=', 'article_reques.user_id')
            ->leftJoin('user_parameters as full_name', function ($join) {
                $join->on('us.id', '=', 'full_name.users_id')
                    ->where('full_name.parameters_id', 1);
            })
            ->leftJoin('user_parameters as up_meca', function ($join) {
                $join->on('us.id', '=', 'up_meca.users_id')
                    ->where('up_meca.parameters_id', 19);
            })
            ->join('user_courses as uc', 'uc.users_id', '=', 'us.id')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'uc.courses_id');
                $join->on('ct.language_id', '=', \DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', \DB::raw(true));
            })
            ->leftJoin('users as u1', 'u1.id', '=', 'trans.created_by')
            ->leftJoin('user_parameters as user_va', function ($join) {
                $join->on('u1.id', '=', 'user_va.users_id')
                    ->where('user_va.parameters_id', 1);
            })
            ->leftJoin('transaction_info as tran_info', function ($join) {
                $join->on('trans.id', '=', 'tran_info.transaction_id');
            })
            ->leftJoin('disciplines_translations as dcp', function ($join) {
                $join->on('dcp.discipline_id', '=', 'article_reques.discipline_id');
                $join->on('dcp.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dcp.active', '=', DB::raw(true));
            })
            // ->leftJoin('matriculations as matriculat', function ($join) {
            //     $join->on('matriculat.user_id', '=', 'article_reques.user_id');
            // })

            ->select([
                'tran_info.transaction_id as transaction_id_info',
                'trans.id as transaction_id',
                'trans.type as type',
                // 'matriculat.id as id',
                // 'matriculat.course_year as year',
                // 'matriculat.lective_year as lective_year',
                'article_reques.id as id_article_requests',
                'at.display_name as article_name',
                'full_name.value as user_name',
                'ct.display_name as course_name',
                'up_meca.value as matriculation_number',
                'u1.name as created_by_user',
                'at.article_id as article_id',
                'u1.id as user_id',
                'dcp.display_name as discplina_display_name',
                'ct.courses_id as course_id',
                'article_reques.created_at as created_at',
                'article_reques.base_value as value',
                'article_reques.year as article_year',
                'article_reques.month as article_month',
                'article_reques.status as status',
                'trans_articl_reques.value as price'
            ])
            // ->whereBetween('matriculat.created_at', [$date1, $date2])
            ->whereNull('article_reques.deleted_at')
            ->whereNull('article_reques.deleted_by')
            ->whereNull('tran_info.transaction_id')
            ->whereNull('trans.deleted_at')
            ->distinct('article_reques.id')
            ->where('trans.data_from', '!=', 'Estorno')
            ->where('article_reques.status', '!=', 'total')
            ->when($student != null, function ($q) use ($student) {
                return $q->where('us.id', $student);
            })
            ->where('article.anoLectivo', '=', $year)
            ->orderBy('article_reques.year', 'ASC')
            ->orderBy('article_reques.month', 'ASC')
            ->orderBy('id_article_requests', 'asc')
            ->get();



        $lectiveYears = $LY->display_name;
        $date1 = $LY->start_date;
        $date2 = $LY->end_date;
        $pdf_name = "FC[ detalhada ]";
        $institution = Institution::latest()->first();
        // return view('Reports::partials.enrollment-income-footer'); Claudio Teste.
        //return view("Reports::enrollment-income", compact('emoluments', 'date1', 'date2'));
        $pdf = PDF::loadView("Reports::extract", compact('objetoValorTransacao', 'objetoSaldo_cartera', 'getInfornBanco', 'getTransaction', 'date1', 'date2', 'institution', 'titulo_documento', 'documentoGerado_documento', 'documentoCode_documento', 'lectiveYears', 'students', 'emolumento'));
        $pdf->setOption('margin-top', '1mm');
        $pdf->setOption('margin-left', '1mm');
        $pdf->setOption('margin-bottom', '2cm');
        $pdf->setOption('margin-right', '1mm');
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('debug-javascript', true);
        $pdf->setOption('javascript-delay', 1000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);
        $pdf->setPaper('a4', 'landscape');
        // $footer_html = view()->make('Reports::partials.enrollment-income-footer', compact('institution'))->render();
        $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution', 'qtdRegisto'))->render();
        $pdf->setOption('footer-html', $footer_html);
        return $pdf->stream('Extracto_' . $students->display_name . '[' . $lectiveYears . '].pdf');
        // return $pdf->download($pdf_name.'.pdf');
    }
}
