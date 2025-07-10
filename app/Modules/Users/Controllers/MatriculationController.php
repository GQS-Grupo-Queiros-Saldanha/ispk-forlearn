<?php

namespace App\Modules\Users\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\Course;
use App\Modules\GA\Models\Discipline;
use App\Modules\Payments\Models\Article;
use App\Modules\Payments\Models\ArticleRequest;
use App\Modules\Users\Models\Matriculation;
use App\Modules\Users\Models\ParameterGroup;
use App\Modules\Users\Models\User;
use App\Modules\Users\Requests\MatriculationRequest;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use LynX39\LaraPdfMerger\Facades\PdfMerger;
use PDF;
use Throwable;
use Toastr;
use Auth;
use Yajra\DataTables\Facades\DataTables;
use App\Modules\GA\Models\Classes;
use App\Modules\GA\Models\LectiveYear;
use App\Modules\Payments\Models\DisciplineArticle;
use App\Modules\Users\Models\UserState;
use App\Modules\Users\Models\UserStateHistoric;
use App\Model\Institution;
use App\Modules\Reports\Controllers\DeclarationController;
use Log;
use App\Modules\Users\Exports\MatriculationExport;
use Maatwebsite\Excel\Facades\Excel;
class MatriculationController extends Controller
{      
     public function matriculationspayments(){
        
        $vetorArticl=[];
        $articlAtivo=null;
        $lectiveYears = LectiveYear::with(['currentTranslation'])
        ->get();

        $currentData = Carbon::now();
         $getlectivoPresent = DB::table('lective_years')
            ->leftJoin('lective_year_translations as lective_transl',function ($q){
                $q->on('lective_transl.lective_years_id','=', 'lective_years.id');
            })
            ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
            ->where('lective_transl.language_id', '=', \DB::raw(LanguageHelper::getCurrentLanguage()))
            ->where('lective_transl.active', '=', \DB::raw(true))
            ->get();
                
        $lectiveYearSelected= DB::table('lective_years')
            ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
            ->first();
       
       $lectiveYearSelected = $lectiveYearSelected->id ?? 6;

        $currentData = Carbon::now();
            
        $lectiveYearSelecte = DB::table('lective_years')
        ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
        ->first();
        $emoluments=DB::table('transactions as trans')
                        ->join('transaction_article_requests as trans_articl_reques','trans_articl_reques.transaction_id','=','trans.id')
                        ->join('article_requests as article_reques', 'article_reques.id', '=', 'trans_articl_reques.article_request_id')
                        ->leftJoin('article_translations as at', function ($join) {
                            $join->on('at.article_id', '=', 'article_reques.article_id');
                            $join->on('at.language_id', '=', \DB::raw(LanguageHelper::getCurrentLanguage()));
                            $join->on('at.active', '=', \DB::raw(true));
                        })
                        ->leftJoin('articles as article','article.id','=','at.article_id')
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
                        ->leftJoin('matriculations as matriculat', function ($join) {
                            $join->on('matriculat.user_id', '=', 'article_reques.user_id');
                        })
                        ->join("code_developer as code_dev",'code_dev.id','article.id_code_dev')
                        ->select([
                            'tran_info.transaction_id as transaction_id_info',
                            'trans.id as transaction_id',
                            'trans.type as type',
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
                        ->whereIn('code_dev.code', ["propina"])
                        ->whereBetween('article.created_at', [$lectiveYearSelecte->start_date, $lectiveYearSelecte->end_date])
                        ->whereBetween('matriculat.created_at', [$lectiveYearSelecte->start_date, $lectiveYearSelecte->end_date])
                        ->whereNull('article_reques.deleted_at')
                        ->whereNull('article_reques.deleted_by')
                        ->whereNull('trans.deleted_at')
                        ->distinct('article_reques.id')
                        ->where('trans.data_from','!=','Estorno')
                        ->orderBy('article_reques.status', 'DESC')
                        ->orderBy('article_reques.year', 'ASC')
                        ->orderBy('article_reques.month', 'ASC')
                        ->get()
        ->groupBy(['matriculation_number','course_name','user_name','id_article_requests']);
           $getMonthEmolument=collect($emoluments)->map(function ($item) use($vetorArticl) {
            foreach ($item as $getMatri => $matricula) {
                foreach ($matricula as $getCurso) {
                    foreach ($matricula as $getName => $name_student) {
                        foreach ($name_student as $getArticle => $article) {
                            foreach ($article as $key => $element) {
                                if (empty($vetorArticl)){
                                    $vetorArticl[]=$element->id_article_requests;
                                }else{
                                    if (in_array($element->id_article_requests, $vetorArticl)){
                                       unset($article[$key]); 
                                    }else{
                                        $vetorArticl[]=$element->id_article_requests;
                                    }
                                } 
                            }
                        }   
                    }   
                }   
            }
            return $item;
        });                 

        $lista_Month=[];
        $ordem_Month=[];
        $desor_Month=[];
        $getLocalizedMonths=getLocalizedMonths();
        
        foreach ($getLocalizedMonths as $key => $value) {
            if ($value['id']>7 && $value['id']<10) {
            }else{
                $lista_Month[]=$value;
            }
        }
        foreach ($lista_Month as $index => $item) {
            if ($item['id']>9) {
                $ordem_Month[]=$item;
            } else {
                $desor_Month[]=$item;
            }
        }
        foreach ($desor_Month as $indexInArray => $element) {
            $ordem_Month[]=$element;
        }


        $data = ['lectiveYears' => $lectiveYears,'lectiveYearSelected'=>$lectiveYearSelected,'emoluments'=>$emoluments,'ordem_Month'=>$ordem_Month,'getMonthEmolument'=>$getMonthEmolument,'getlectivoPresent'=>$getlectivoPresent];
        return view('Users::matriculations.matriculations-payments')->with($data);
    } 
    
    public function getMatriculations_paymentsAlectivo($anoLectivo)
    {
        try{
            $vetorArticl=[];
            $articlAtivo=null;
            
            $lectiveYearSelected = DB::table('lective_years')
            ->where('lective_years.id','=',$anoLectivo)
            ->first();
                $emoluments=DB::table('transactions as trans')
                        ->join('transaction_article_requests as trans_articl_reques','trans_articl_reques.transaction_id','=','trans.id')
                        ->join('article_requests as article_reques', 'article_reques.id', '=', 'trans_articl_reques.article_request_id')
                        ->leftJoin('article_translations as at', function ($join) {
                            $join->on('at.article_id', '=', 'article_reques.article_id');
                            $join->on('at.language_id', '=', \DB::raw(LanguageHelper::getCurrentLanguage()));
                            $join->on('at.active', '=', \DB::raw(true));
                        })
                        ->leftJoin('articles as article','article.id','=','at.article_id')
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
                        ->leftJoin('matriculations as matriculat', function ($join) {
                            $join->on('matriculat.user_id', '=', 'article_reques.user_id');
                        })
                        ->join("code_developer as code_dev",'code_dev.id','article.id_code_dev')
                        ->select([
                            'tran_info.transaction_id as transaction_id_info',
                            'trans.id as transaction_id',
                            'trans.type as type',
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
                        ->whereIn('code_dev.code', ["propina"])
                        ->whereBetween('article.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                        ->whereBetween('matriculat.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                        ->whereNull('article_reques.deleted_at')
                        ->whereNull('article_reques.deleted_by')
                        ->whereNull('trans.deleted_at')
                        ->distinct('article_reques.id')
                        ->where('trans.data_from','!=','Estorno')
                        ->orderBy('article_reques.status', 'DESC')
                        ->orderBy('article_reques.year', 'ASC')
                        ->orderBy('article_reques.month', 'ASC')
                        ->get()
                ->groupBy(['matriculation_number','course_name','user_name','id_article_requests']);
                $getMonthEmolument=collect($emoluments)->map(function ($item) use($vetorArticl) {
                    foreach ($item as $getMatri => $matricula) {
                        foreach ($matricula as $getCurso) {
                            foreach ($matricula as $getName => $name_student) {
                                foreach ($name_student as $getArticle => $article) {
                                    foreach ($article as $key => $element) {
                                        if (empty($vetorArticl)){
                                            $vetorArticl[]=$element->id_article_requests;
                                        }else{
                                            if (in_array($element->id_article_requests, $vetorArticl)){
                                               unset($article[$key]); 
                                            }else{
                                                $vetorArticl[]=$element->id_article_requests;
                                            }
                                        } 
                                    }
                                }   
                            }   
                        }   
                    }
                    return $item;
                });                      

            return response()->json(['data'=>$getMonthEmolument]);
               
            } catch (Exception | Throwable $e) {
                // logError($e);
                return response()->json($e);
                return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
            }
    }
    
    // 3
     public function getMatriculations_paymentsgerarPdf($anoLectivo)
    {
        try{
            $anolectivoActivo= DB::table('lective_year_translations')
            ->where('lective_year_translations.lective_years_id','=',$anoLectivo)
            ->where('lective_year_translations.language_id', '=', \DB::raw(LanguageHelper::getCurrentLanguage()))
            ->where('lective_year_translations.active', '=', \DB::raw(true))
           ->get(); 

           $lectiveYearSelected = DB::table('lective_years')
            ->where('lective_years.id','=',$anoLectivo)
            ->first();

                    $emoluments=DB::table('transactions as trans')
                        ->join('transaction_article_requests as trans_articl_reques','trans_articl_reques.transaction_id','=','trans.id')
                        ->join('article_requests as article_reques', 'article_reques.id', '=', 'trans_articl_reques.article_request_id')
                        ->leftJoin('article_translations as at', function ($join) {
                            $join->on('at.article_id', '=', 'article_reques.article_id');
                            $join->on('at.language_id', '=', \DB::raw(LanguageHelper::getCurrentLanguage()));
                            $join->on('at.active', '=', \DB::raw(true));
                        })
                        ->leftJoin('articles as article','article.id','=','at.article_id')
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
                        ->leftJoin('matriculations as matriculat', function ($join) {
                            $join->on('matriculat.user_id', '=', 'article_reques.user_id');
                        })
                        ->join("code_developer as code_dev",'code_dev.id','article.id_code_dev')
                        ->select([
                            'tran_info.transaction_id as transaction_id_info',
                            'trans.id as transaction_id',
                            'trans.type as type',
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
                        ->whereIn('code_dev.code', ["propina"])
                        ->whereBetween('article.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                        ->whereBetween('matriculat.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                        ->whereNull('article_reques.deleted_at')
                        ->whereNull('article_reques.deleted_by')
                        ->whereNull('trans.deleted_at')
                        ->distinct('article_reques.id')
                        ->where('trans.data_from','!=','Estorno')
                        ->orderBy('article_reques.status', 'DESC')
                        ->orderBy('article_reques.year', 'ASC')
                        ->orderBy('article_reques.month', 'ASC')
                        ->get()
                    ->groupBy(['matriculation_number','course_name','user_name','id_article_requests']);
                 
                    
                
                $lista_Month=[];
                $ordem_Month=[];
                $desor_Month=[];
                $getLocalizedMonths=getLocalizedMonths();
                foreach ($getLocalizedMonths as $key => $value) {
                    if ($value['id']>7 && $value['id']<10) {
                    }else{
                        $lista_Month[]=$value;
                    }
                }
                foreach ($lista_Month as $index => $item) {
                    if ($item['id']>9) {
                        $ordem_Month[]=$item;
                    } else {
                        $desor_Month[]=$item;
                    }
                }
                foreach ($desor_Month as $indexInArray => $element) {
                    $ordem_Month[]=$element;
                }
             
                $institution = Institution::latest()->first();
                $titulo_documento = "Estado da mensalidade";
                $documentoGerado_documento = "Documento gerado a";
                $documentoCode_documento = 1;
                $data=[
                    'ordem_Month' => $ordem_Month,
                    'anolectivoActivo' => $anolectivoActivo,
                    'emoluments' => $emoluments,
                    'institution' => $institution,
                    'titulo_documento' => $titulo_documento,
                    'documentoGerado_documento' => $documentoGerado_documento,
                    'documentoCode_documento' => $documentoCode_documento
                ];
                    
                $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
                $pdf = PDF::loadView("Users::matriculations.matriculations-payments-pdf", $data);              
                $pdf->setOption('margin-top', '1mm');
                $pdf->setOption('margin-left', '1mm');
                $pdf->setOption('margin-bottom', '2cm');
                $pdf->setOption('margin-right', '1mm');
                $pdf->setOption('enable-javascript', true);
                $pdf->setOption('debug-javascript', true);
                $pdf->setOption('javascript-delay', 1000);
                $pdf->setOption('enable-smart-shrinking', true);
                $pdf->setOption('no-stop-slow-scripts', true);
                $pdf->setOption('footer-html', $footer_html);
                $pdf->setPaper('a4','landscape');       

                return $pdf->stream('Forlearn | estado-mensalidade');
        } catch (Exception | Throwable $e) {
            // logError($e);
            return response()->json($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
    
    
    
    private function getEmolumentoEstudent($lectiveYearSelected)
    {
            Log::info("log info",$lectiveYearSelected);
            $consultArt = DB::table('articles as art')
            ->leftJoin('article_translations as at', function ($join) {
                $join->on('art.id', '=', 'at.article_id');
                $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('at.active', '=', DB::raw(true));
            })
            ->leftJoin('article_requests as article_ret', function ($join) {
                $join->on('art.id', '=', 'article_ret.article_id');
            })
            ->leftJoin('transaction_article_requests as trans_artic_req', function ($join) {
                $join->on('article_ret.id', '=', 'trans_artic_req.article_request_id');
            })

            ->leftJoin('transactions as tran', function ($join) {
                $join->on('trans_artic_req.transaction_id', '=', 'tran.id');
            })

            ->leftJoin('transaction_receipts as trant_receipts', function ($join) {
                $join->on('tran.id', '=', 'trant_receipts.transaction_id');
            })
            ->leftJoin('historic_user_balance as historic_saldo',function ($join){
                $join->on('tran.id','=','historic_saldo.id_transaction');
            })
            ->join("code_developer as code_dev",'code_dev.id','art.id_code_dev')
            ->leftJoin('user_parameters as u_p', function ($join) {
                $join->on('u_p.users_id', '=', 'article_ret.user_id')
                ->where('u_p.parameters_id', '=', 1);
            })
            ->select([
                'u_p.value as name_student',
                'article_ret.id as article_req_id',
                'article_ret.user_id as user_id',
                'tran.id as transaction_id',
                'historic_saldo.valor_credit as valor_credit',
                'at.display_name as article_name',
                'article_ret.year as article_year',
                'article_ret.month as article_month',
                'article_ret.base_value as base_value',
                'article_ret.extra_fees_value as extra_fees_value',
                'article_ret.status as status',
                'article_ret.discipline_id as art_idDisciplina',
                'article_ret.meta as meta',
                'trant_receipts.created_at as created_at_arti',
                'tran.data_from as data_from',
                'trant_receipts.code as code'
            ])
            // ->whereIn('article_ret.user_id',[1086,3099])
            ->whereIn('code_dev.code', ["propina"])
            ->whereNull('article_ret.deleted_at')
            ->whereNull('article_ret.deleted_by')
            ->whereNull('tran.deleted_at')
            ->where('tran.type','!=','debit')
            ->orderBy('article_ret.year', 'ASC')
            ->orderBy('article_ret.month', 'ASC')
            ->orderBy('tran.id', 'DESC')
            ->whereBetween('art.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
        ->get();
        $collet=collect($consultArt)->map(function($item){
            return $item->article_req_id;
        });


        
        $consultRecibos = DB::table('articles as art')
            ->leftJoin('article_translations as at', function ($join) {
                $join->on('art.id', '=', 'at.article_id');
                $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('at.active', '=', DB::raw(true));
            })
            ->leftJoin('article_requests as article_ret', function ($join) {
                $join->on('art.id', '=', 'article_ret.article_id');
            })
            ->leftJoin('transaction_article_requests as trans_artic_req', function ($join) {
                $join->on('article_ret.id', '=', 'trans_artic_req.article_request_id');
            })

            ->leftJoin('transactions as tran', function ($join) {
                $join->on('trans_artic_req.transaction_id', '=', 'tran.id');
            })

            ->leftJoin('transaction_receipts as trant_receipts', function ($join) {
                $join->on('tran.id', '=', 'trant_receipts.transaction_id');
            })
            ->leftJoin('historic_user_balance as historic_saldo',function ($join){
                $join->on('tran.id','=','historic_saldo.id_transaction');
            })
            ->join("code_developer as code_dev",'code_dev.id','art.id_code_dev')
            ->leftJoin('user_parameters as u_p', function ($join) {
                $join->on('u_p.users_id', '=', 'article_ret.user_id')
                ->where('u_p.parameters_id', '=', 1);

            })
            ->select([
            'u_p.value as name_student',
            'article_ret.id as article_req_id',
            'article_ret.user_id as user_id',
            'tran.id as transaction_id',
            'historic_saldo.valor_credit as valor_credit',
            'at.display_name as article_name',
            'article_ret.year as article_year',
            'article_ret.month as article_month',
            'article_ret.base_value as base_value',
            'article_ret.discipline_id as art_idDisciplina',
            'article_ret.meta as meta',
            'article_ret.extra_fees_value as extra_fees_value',
            'article_ret.status as status',
            'tran.data_from as data_from',
            'trant_receipts.code as code'
            ])
            // ->whereIn('article_ret.user_id',[1086,3099])
            ->where('tran.type', '=', 'debit')
            ->whereIn('code_dev.code', ["propina"])
            ->whereNull('article_ret.deleted_at')
            ->whereNull('article_ret.deleted_by')
            ->whereNull('tran.deleted_at')
            ->whereNotin('trans_artic_req.article_request_id',$collet)
            ->orderBy('article_ret.year', 'ASC')
            ->orderBy('article_ret.month', 'ASC')
            ->orderBy('tran.id', 'DESC')
            ->whereBetween('art.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
        ->get();
        return $model= $consultArt->merge($consultRecibos);
      
    }
    
    //BUSCA A MATRICULA DE ACORDO AO CURSO 
    public function getMatriculasCourse($id_curso, $lective_year)
    {          
        try {
            // return [$id_curso, $lective_year];

            $tranf_type='payment';
            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                // ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
                ->where('lective_years.id', '=', $lective_year)
            ->first();

            $emolumento_confirma_prematricula= self::pre_matricula_confirma_emolumento( $lectiveYearSelected->id);

            $model = Matriculation::join('users as u0', 'u0.id', '=', 'matriculations.user_id')
                ->join('users as u1', 'u1.id', '=', 'matriculations.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'matriculations.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'matriculations.deleted_by')
                ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'u0.id')
                ->join('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'uc.courses_id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })

                ->leftJoin('matriculation_classes as mc', 'mc.matriculation_id', '=', 'matriculations.id')
                ->join('classes as cl', function ($join)  {
                    $join->on('cl.id', '=', 'mc.class_id');
                    $join->on('mc.matriculation_id', '=', 'matriculations.id');
                    $join->on('matriculations.course_year', '=', 'cl.year');
                })

                ->leftJoin('user_parameters as u_p', function ($join) {
                        $join->on('u0.id', '=', 'u_p.users_id')
                        ->where('u_p.parameters_id', 1);
                })
                ->leftJoin('user_parameters as up_meca', function ($join) {
                        $join->on('u0.id', '=', 'up_meca.users_id')
                        ->where('up_meca.parameters_id', 19);
                })
                ->leftJoin('user_parameters as up_bi', function ($join) {
                    $join->on('u0.id','=','up_bi.users_id')
                    ->where('up_bi.parameters_id', 14);
                })

                //Emarq
                ->leftJoin('user_parameters as up_sexo', function ($join) {
                    $join->on('u0.id','=','up_sexo.users_id')
                    ->where('up_sexo.parameters_id', 2);
                })
                ->leftJoin('parameter_options as po_sexo', function ($join) {
                    $join->on('po_sexo.id','=','up_sexo.value');
                    
                })

                ->leftJoin('user_parameters as up_contact', function ($join) {
                    $join->on('u0.id','=','up_contact.users_id')
                    ->where('up_contact.parameters_id', 36);
                })

                ->leftJoin('user_parameters as up_escola', function ($join) {
                    $join->on('u0.id','=','up_escola.users_id')
                    ->where('up_escola.parameters_id', 313);
                })

                ->leftJoin('user_parameters as up_data_nascimento', function ($join) {
                    $join->on('u0.id','=','up_data_nascimento.users_id')
                    ->where('up_data_nascimento.parameters_id', 5);
                })

                ->leftJoin('scholarship_holder as hold', function ($join) {
                    $join->on('hold.user_id', '=', 'u0.id')
                    ->where('hold.are_scholarship_holder', 1);
                })
                ->leftJoin('scholarship_entity as ent', function ($join) {
                    $join->on('ent.id', '=', 'hold.scholarship_entity_id');
                })

                ->leftJoin('regime_especial as re', 're.user_id', '=', 'u0.id')
                ->leftJoin('rotacao_regime_especial as rre', 'rre.id', '=', 're.rotation_id')
                //Emarq
                ->leftJoin('article_requests as art_requests',function ($join) use( $emolumento_confirma_prematricula)
                {
                        $join->on('art_requests.user_id', '=',   'u0.id')
                        ->whereIn('art_requests.article_id',   $emolumento_confirma_prematricula    );
                
                })
            
            ->select([
                'matriculations.*',
                'matriculations.code as code_matricula',
                'up_meca.value as matricula',
                'u0.id as id_usuario',
                'art_requests.status as state',
                'up_bi.value as n_bi',
                //emarq
                'rre.nome as regime',
                 'po_sexo.code as sexo',  
                 'up_contact.value as contacto',  
                 'up_escola.value as escola',  
                 'up_data_nascimento.value as data_nascimento',  
                 'ent.company as entidade',  
                 'ent.type as categoria',
                //emarq
                'cl.display_name as classe',
                'u_p.value as student',
                'u0.email as email',
                'u1.name as criado_por',
                'u2.name as actualizado_por',
                'u3.name as deletador_por',
                'ct.display_name as course',
                //sedrac
                'uc.courses_id as id_course'
            ])
            ->where('art_requests.deleted_by', null) 
            ->where('art_requests.deleted_at', null)
            ->where('uc.courses_id',$id_curso)
            
            ->groupBy('u_p.value')
            ->distinct('id')
            // ->whereBetween('matriculations.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date]);
                ->where('matriculations.lective_year', $lectiveYearSelected->id);

                
            return Datatables::Eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('Users::matriculations.datatables.actions')->with('item', $item);
                })
                ->addColumn('states', function($state){
                    return view('Users::matriculations.datatables.states')->with('state',$state);
                })
                ->rawColumns(['actions', 'states'])
                ->addIndexColumn()
                ->toJson();

        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }
    
    //BUSCA ANO CURRICULAR DE ACORDO AO CURSO 
    public function getCursoAno($curso, $lective_year)
    {        
        try {
            //$tranf_type='payment';
            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                // ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
                ->where('lective_years.id', '=', $lective_year)
                ->first();

            $emolumento_confirma_prematricula= self::pre_matricula_confirma_emolumento( $lectiveYearSelected->id);

            $model = Matriculation::join('users as u0', 'u0.id', '=', 'matriculations.user_id')
                ->join('users as u1', 'u1.id', '=', 'matriculations.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'matriculations.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'matriculations.deleted_by')
                ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'u0.id')
                ->join('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'uc.courses_id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })

                ->leftJoin('matriculation_classes as mc', 'mc.matriculation_id', '=', 'matriculations.id')
                ->join('classes as cl', function ($join)  {
                    $join->on('cl.id', '=', 'mc.class_id');
                    $join->on('mc.matriculation_id', '=', 'matriculations.id');
                    $join->on('matriculations.course_year', '=', 'cl.year');
                })

                ->leftJoin('user_parameters as u_p', function ($join) {
                        $join->on('u0.id', '=', 'u_p.users_id')
                        ->where('u_p.parameters_id', 1);
                })
                ->leftJoin('user_parameters as up_meca', function ($join) {
                        $join->on('u0.id', '=', 'up_meca.users_id')
                        ->where('up_meca.parameters_id', 19);
                })

                ->leftJoin('article_requests as art_requests',function ($join) use( $emolumento_confirma_prematricula)
                {
                        $join->on('art_requests.user_id', '=',   'u0.id')
                        ->whereIn('art_requests.article_id',   $emolumento_confirma_prematricula    );
                
                })                
        ->select([
            'matriculations.course_year as course_year'
            // 'cl.display_name as classe'
        ])
        ->where('art_requests.deleted_by', null) 
        ->where('art_requests.deleted_at', null)                              
        ->where('uc.courses_id',$curso)
        
        ->groupBy('u_p.value')
        ->distinct('id')
        
        ->orderBy('matriculations.course_year')
        // ->whereBetween('matriculations.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date]);
            ->where('matriculations.lective_year', $lectiveYearSelected->id);

            return Datatables::Eloquent($model)                    
                ->addIndexColumn()
                ->toJson();

            // return response()->json(array('data'=>$model));
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    //BUSCA A MATRICULA DE ACORDO AO CURSO E O ANO CURRICULAR
    public function getMatriculasCourseAno($curso, $id_curso_years, $lective_year)
    {
        try {
            
            // return [$curso, $id_curso_years, $lective_year];

            $tranf_type='payment';
            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                // ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
                ->where('lective_years.id', '=', $lective_year)
                ->first();
            
            $emolumento_confirma_prematricula= self::pre_matricula_confirma_emolumento( $lectiveYearSelected->id);

                $model = Matriculation::join('users as u0', 'u0.id', '=', 'matriculations.user_id')
                    ->join('users as u1', 'u1.id', '=', 'matriculations.created_by')
                    ->leftJoin('users as u2', 'u2.id', '=', 'matriculations.updated_by')
                    ->leftJoin('users as u3', 'u3.id', '=', 'matriculations.deleted_by')
                    ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'u0.id')
                    ->join('courses_translations as ct', function ($join) {
                        $join->on('ct.courses_id', '=', 'uc.courses_id');
                        $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('ct.active', '=', DB::raw(true));
                    })
                    

                    ->leftJoin('matriculation_classes as mc', 'mc.matriculation_id', '=', 'matriculations.id')
                    ->join('classes as cl', function ($join)  {
                        $join->on('cl.id', '=', 'mc.class_id');
                        $join->on('mc.matriculation_id', '=', 'matriculations.id');
                        $join->on('matriculations.course_year', '=', 'cl.year');
                    })

                    ->leftJoin('user_parameters as u_p', function ($join) {
                            $join->on('u0.id', '=', 'u_p.users_id')
                            ->where('u_p.parameters_id', 1);
                    })
                    ->leftJoin('user_parameters as up_meca', function ($join) {
                            $join->on('u0.id', '=', 'up_meca.users_id')
                            ->where('up_meca.parameters_id', 19);
                    })
                    ->leftJoin('user_parameters as up_bi', function ($join) {
                        $join->on('u0.id','=','up_bi.users_id')
                        ->where('up_bi.parameters_id', 14);
                   })

                   ->leftJoin('article_requests as art_requests',function ($join) use($emolumento_confirma_prematricula)
                    {
                            $join->on('art_requests.user_id', '=',   'u0.id')
                            ->whereIn('art_requests.article_id', $emolumento_confirma_prematricula);
                    
                    })
                
                ->select([
                    'matriculations.*',
                    'matriculations.code as code_matricula',
                    'up_meca.value as matricula',
                    'u0.id as id_usuario',
                    'art_requests.status as state', 
                    'up_bi.value as n_bi', 
                    
                    'cl.display_name as classe',
                    'u_p.value as student',
                    'u0.email as email',
                    'u1.name as criado_por',
                    'u2.name as actualizado_por',
                    'u3.name as deletador_por',
                    'ct.display_name as course',
                    //sedrac
                    'uc.courses_id as id_course'
                ])
                ->distinct('id')
                ->where('art_requests.deleted_by', null) 
                ->where('art_requests.deleted_at', null)                
                ->where('uc.courses_id',$curso)
                ->where('matriculations.course_year',$id_curso_years)  
                
                ->groupBy('u_p.value')              
                // ->with(['classes'])
                ->distinct('id')
                // ->whereBetween('matriculations.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date]);
                    ->where('matriculations.lective_year', $lectiveYearSelected->id);

                
            return Datatables::Eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('Users::matriculations.datatables.actions')->with('item', $item);
                })
                ->addColumn('states', function($state){
                    return view('Users::matriculations.datatables.states')->with('state',$state);
                })
                ->rawColumns(['actions', 'states'])
                // ->rawColumns(['actions'])
                ->addIndexColumn()
                ->toJson();

        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }


    public function index()
    {           
        try {
            
           $lectiveYears = LectiveYear::with(['currentTranslation'])
             ->get();

            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                            ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
                            ->first();
            $lectiveYearSelected = $lectiveYearSelected->id ?? 6;

            return view('Users::matriculations.index', compact('lectiveYears', 'lectiveYearSelected'));
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
























    
    public function create()
    {
        try {
            $data = [
                'action' => 'create',
                'languages' => Language::whereActive(true)->get(),
                'users' => $this->studentsWithCourseWithoutMatriculationSelectList()
            ];
          
            return view('Users::matriculations.matriculation')->with($data);
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function store(MatriculationRequest $request)
    {

        // return 0;  
        try {
            DB::beginTransaction();
           $user = User::findOrFail($request->get('user'));
            //Saber o Status do Estudante quanto a Inscricao ao exame de Acesso
            //Se for Pendente, nao efeturar a matricula
            //id do article para o exame::6

      $estadoInscricao = DB::table('article_requests')->where([
                ['user_id','=', $user->id],
                ['article_id','=','6']
            ])->first();
            //TODO: TRATAR QUANDO O ARRAY VIER VAZIO;
            if ($user->hasRole('candidado-a-estudante')) {
                if ($estadoInscricao->status == "pending" || $estadoInscricao->status == "partial") {
                    //Error message (Pagamento de Inscrição de Accesso Pendente ou Parcial)
                    Toastr::error(__('Pagamento em atraso'), __('toastr.error'));
                    return redirect()->route('matriculations.index');
                }
            }
            //return $estadoInscricao;
            //ERRO: CODIGO DA MATRICULA REPETIDO (CASO RARO)
            //SOLUC: AVALIAR SE O CODIGO A SER CRIADO JA EXISTE NA BD
            
             $nextCodeNumber = Carbon::now()->format('y') . '0001';
         
            $latestsMatriculation = Matriculation::latest()->where('lective_year',6)->first();
            if ($latestsMatriculation) {
             $nextCodeNumber = ((int)ltrim($latestsMatriculation->code, 'MT') + 1);
            }
            
            
            $nextCode = 'MT' . $nextCodeNumber;


            $maxSelectedYear = (int)collect($request->get('years'))->max();
            $matriculation = Matriculation::create([
                'user_id' => $user->id,
                'code' => $nextCode,
                'course_year' => $maxSelectedYear,
                'lective_year'=>6,
            ]);

            $estadoAntigo = $user->hasRole('candidado-a-estudante') ? 1 : 2;

            // role candidate to student
            if ($user->hasRole('candidado-a-estudante')) {
                $user->syncRoles('student');
                
                // Número de estudante
                $currentNumber = $user->parameters()->where('parameters.id', 19)->first();
                $courseNumericCode = $user->courses()->first()->numeric_code;
                $newNumber = Carbon::now()->format('y') . $courseNumericCode . ltrim($nextCodeNumber, '20');

                if ($currentNumber) {
                    $currentNumber->pivot->value = $newNumber;
                    $currentNumber->pivot->save();
                } else {
                    $user_n_mecanografico[] = [
                        'parameters_id' => 19,
                        'created_by' => auth()->user()->id ?? 0,
                        'parameter_group_id' => 1,
                        'value' => $newNumber
                    ];

                    $user->parameters()->attach($user_n_mecanografico);
                }
                
                //Duplicar fotografia
                
              $current_foto = DB::table('user_parameters')
                                ->where('users_id', $user->id)
                                ->where('parameters_id', 25)
                                ->orderBy('id', 'DESC')
                                ->first();
                
                
                 if(isset($current_foto) && $current_foto->parameter_group_id == 11){
                      
                      DB::table('user_parameters')->insert(
                          [
                        'users_id' => $user->id,
                        'parameters_id' => 25,
                        'created_by' => auth()->user()->id ?? 0,
                        'parameter_group_id' => 1,
                        'value' => $current_foto->value
                    ]
                          );
                 }             
               
            }
            
            //return $estadoAntigo;


            // disciplines
            $userDisciplines = [];
            $yearsWithDisciplines = [];
            $allDisciplinesInCurricularYear = [];
            $allDisciplinesOffCurricularYear = [];
            $disciplineByYear = $request->get('disciplines');
            foreach ($disciplineByYear as $year => $disciplines) {
                if (is_array($disciplines) && count($disciplines)) {
                    $yearsWithDisciplines[] = (int)$year;
                    foreach ($disciplines as $d) {
                        $userDisciplines[$d] = ['exam_only' => false];
                        if ((int)$year !== $maxSelectedYear) {
                            $allDisciplinesOffCurricularYear[$d] = false;
                        } else {
                            $allDisciplinesInCurricularYear[$d] = false;
                        }
                    }
                }
            }

            // exam only disciplines
            $examOnlyDisciplinesByYear = $request->get('disciplines_exam_only');
            if (is_array($examOnlyDisciplinesByYear)) {
                foreach ($examOnlyDisciplinesByYear as $year => $disciplines) {
                    if (is_array($disciplines) && count($disciplines)) {
                        foreach ($disciplines as $d) {
                            $userDisciplines[$d]['exam_only'] = true;
                            $allDisciplinesOffCurricularYear[$d] = true;
                        }
                    }
                }
            }



            // classes
            
            $userClasses = [];
            $yearsWithClasses = [];
            foreach ($request->get('classes') as $year => $class) {
                if ($class) {
                    $yearsWithClasses[] = $year;
                    $userClasses[] = $class;
                }
            }

            //return $allDisciplinesOffCurricularYear;

            //Obter o total de matriculas feitas em uma determinada turma
            $get_matriculation_class_total = DB::table('matriculation_classes')
                                        ->where('class_id', $userClasses)
                                        ->count();

            //Obter o total de vagas de uma determinada turma
            $get_class_vacancies = Classes::whereId($userClasses)->firstOrFail();

            //Avaliar se o total de matriculas feitas em uma determinada turma + 1 (mais a proxima a ser feita) for menor ou igual ao total de vagas
            if ($get_matriculation_class_total + 1 <= $get_class_vacancies->vacancies) {
                //return "Pode Efetuar Matrícula";


                if ($yearsWithDisciplines !== $yearsWithClasses) {
                    return redirect()->back()->withErrors(['Definição de turmas e/ou disciplinas inválida.'])->withInput();
                }

                if (!empty($userDisciplines)) {
                    $matriculation->disciplines()->sync($userDisciplines);
                }

                if (!empty($userClasses)) {
                    $matriculation->classes()->sync($userClasses);
                }

                $articleRequets = [];
                // Confirmação de matrícula (id: 8)
                $r1 = createAutomaticArticleRequest($user->id, $estadoAntigo == 1 ? 7 : 8, null, null);
                if (!$r1) {
                    throw new Exception('Could not create automatic [Confirmação de matrícula (id: 8)] article request payment for student (id: ' . $user->id . ') matriculation');
                } 
                $articleRequets[$r1]['updatable'] = false;

                // Emissão de Cartão de Estudante (id: 31)
                $r2 = createAutomaticArticleRequest($user->id, 31, null, null);
                if (!$r2) {
                    throw new Exception('Could not create automatic [Emissão de Cartão de Estudante (id: 31)] article request payment for student (id: ' . $user->id . ') matriculation');
                }
                $articleRequets[$r2]['updatable'] = false;

                // Pagamento de Propina
                $articlePropinaId = null;
                $courseID = $user->courses()->first()->id;

                $courseData = Course::where('id', $courseID)
                ->with([
                    'studyPlans' => function ($q) use ($maxSelectedYear) {
                        $q->with([
                            'study_plans_has_disciplines' => function ($q) use ($maxSelectedYear) {
                                $q->where('years', $maxSelectedYear);
                                $q->with('discipline');
                            }
                        ]);
                    },
                ])
                ->first();

                $curricularYearAllDisciplinesCount = $courseData ? $courseData->studyPlans->study_plans_has_disciplines->count() : 0;
                $curricularYearSelectedDisciplinesCount = count($allDisciplinesInCurricularYear);

                $currentYearToValidate = $maxSelectedYear;
                if ($courseData && $courseID === 25 && $currentYearToValidate > 2) {
                    $specializationCode = null;

                    $courseData = Course::where('id', $courseID)
                    ->with([
                        'studyPlans' => function ($q) use ($maxSelectedYear) {
                            $q->with([
                                'study_plans_has_disciplines' => function ($q) {
                                    $q->with('discipline');
                                }
                            ]);
                        },
                    ])
                    ->first();

                    while ($currentYearToValidate > 2) {
                        if (collect($request->get('years'))->contains((string)$currentYearToValidate)) {
                            $disciplineGlobalCount = [
                            'GEE' => 0,
                            'COA' => 0,
                            'ECO' => 0,
                        ];
                            $disciplineSelectedCount = [
                            'GEE' => 0,
                            'COA' => 0,
                            'ECO' => 0,
                        ];
                            $specializationCodeForTheYear = null;

                            foreach ($courseData->studyPlans->study_plans_has_disciplines as $spDiscipline) {
                                if ($spDiscipline->years === $currentYearToValidate) {
                                    $code = substr($spDiscipline->discipline->code, 0, 3);
                                    if (isset($disciplineGlobalCount[$code])) {
                                        ++$disciplineGlobalCount[$code];
                                        if (in_array((string)$spDiscipline->discipline->id, $disciplineByYear[$currentYearToValidate], true)) {
                                            ++$disciplineSelectedCount[$code];
                                        }
                                    }
                                }
                            }

                            $courseBranchesWithSelectedDisciplines = array_filter($disciplineSelectedCount, function ($item) {
                                return $item;
                            });
                            $specializationCodeForTheYear = array_key_first($courseBranchesWithSelectedDisciplines);

                            if ($currentYearToValidate === $maxSelectedYear) {
                                $specializationCode = $specializationCodeForTheYear;
                                $curricularYearAllDisciplinesCount = $disciplineGlobalCount[$specializationCode];
                            }

                            $differentSpecializationsBetweenYears = $specializationCodeForTheYear !== $specializationCode;
                            $notOneSpecializationSelected = count($courseBranchesWithSelectedDisciplines) !== 1;

                            if ($notOneSpecializationSelected || $differentSpecializationsBetweenYears) {
                                return redirect()->back()->withErrors(['Disciplinas de especialidades selecionadas de forma inválida.'])->withInput();
                            }
                        }

                        --$currentYearToValidate;
                    }

                    switch ($specializationCode) {
                    case 'GEE':
                        $articlePropinaId = 47;
                        break;
                    case 'COA':
                        $articlePropinaId = 45;
                        break;
                    case 'ECO':
                        $articlePropinaId = 46;
                        break;
                }
                }

                if ($curricularYearAllDisciplinesCount === $curricularYearSelectedDisciplinesCount) {
                    if (!$articlePropinaId) {
                        $articlePropina = Article::whereHas('monthly_charges', function ($q) use ($courseID) {
                            $q->where('course_id', $courseID);
                        })->first();

                        $articlePropinaId = $articlePropina->id;
                    }

                    $r3 = createAutomaticArticleRequest($user->id, $articlePropinaId, Carbon::now()->format('Y'), 3);
                    if (!$r3) {
                        throw new Exception('Could not create automatic [Pagamento de Propina (id: ' . $articlePropinaId . ')] article request payment for student (id: ' . $user->id . ') matriculation');
                    }
                    $articleRequets[$r3]['updatable'] = false;
                } else {
                    $allDisciplinesOffCurricularYear = array_replace($allDisciplinesInCurricularYear, $allDisciplinesOffCurricularYear);
                }

                // Cadeiras em atraso
               $data=Carbon::now();
                foreach ($allDisciplinesOffCurricularYear as $offDiscipline => $examOnly) {
                    if ($examOnly) {
                        // Inscrição Por Exame Cadeira Em Atraso (id: 41)
                        $r4 = createAutomaticArticleRequest($user->id, 41, null, null);

                        //return $r4;

                        //Array que vai para a tabela 'disciplines_request' caso existirem disciplinas em atraso registados para exame
                        $group = [
                            'discipline_id' => $offDiscipline,
                            'article_request_id' => $r4,
                            'user_id' => $user->id,
                            'created_at' => $data,
                            'updated_at' => $data
                        ];

                        DisciplineArticle::insert($group);

                        if (!$r4) {
                            throw new Exception('Could not create automatic [Inscrição Por Exame Cadeira Em Atraso (id: 41)] article request payment for student (id: ' . $user->id . ') matriculation');
                        }
                        $articleRequets[$r4]['updatable'] = true;
                    } else {
                        // Inscrição Por Frequência Cadeira Em Atraso (id: 42)
                        $r5 = createAutomaticArticleRequest($user->id, 42, null, null);

                        //Array que vai para a tabela 'disciplines_request' caso existirem disciplinas em atraso registados para frequencia
                        $group = [
                            'discipline_id' => $offDiscipline,
                            'article_request_id' => $r5,
                            'user_id' => $user->id,
                            'created_at' => $data,
                            'updated_at' => $data

                        ];

                        DisciplineArticle::insert($group);

                        if (!$r5) {
                            throw new Exception('Could not create automatic [Inscrição Por Frequência Cadeira Em Atraso (id: 42)] article request payment for student (id: ' . $user->id . ') matriculation');
                        }
                        $articleRequets[$r5]['updatable'] = true;
                    }
                }

                $matriculation->articleRequests()->sync($articleRequets);

                $this->changeState("STORE", $request->get('user'), $maxSelectedYear, $courseID);

                DB::commit();
                // Success message
                Toastr::success(__('Users::matriculations.store_success_message'), __('toastr.success'));
                return redirect()->route('matriculations.index');
            } else {
                //Error message (total de vagas excedidos)
                Toastr::error(__('Total de vagas excedidas para esta turma'), __('toastr.error'));
                return redirect()->route('matriculations.index');
            }
        } catch (Exception | Throwable $e) {
            return $e;
            Toastr::error($e->getMessage(), __('toastr.error'));
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
    


    public function fetch($id, $action)
    {
        try {
            $matriculation = $matriculation = Matriculation::where('id', $id)
                ->with([
                    'disciplines',
                    'classes',
                    'user' => function ($q) {
                        $q->with([
                            'courses' => function ($q) {
                                $q->with([
                                    'currentTranslation',
                                    'studyPlans' => function ($q) {
                                        $q->with([
                                            'study_plans_has_disciplines' => function ($q) {
                                                $q->with([
                                                    'discipline' => function ($q) {
                                                        $q->with('currentTranslation');
                                                    }
                                                ]);
                                            }
                                        ]);
                                    },
                                    'classes'
                                ]);
                            }
                        ]);
                    }])
                ->first();

            if ($matriculation) {
                $stored = [];
                foreach ($matriculation->classes as $class) {
                    $stored['years'][] = $class->year;
                    $stored['classes'][$class->year] = $class->id;
                }

                foreach ($matriculation->disciplines as $discipline) {
                    $stored['disciplines'][] = $discipline->id;
                    if ($discipline->pivot->exam_only) {
                        $stored['disciplines_exam_only'][] = $discipline->id;
                    }
                }

                $data = [
                    'action' => $action,
                    'userName' => $this->formatUserName($matriculation->user),
                    'matriculation' => $matriculation,
                    'stored' => $stored
                ];
            } else {
                $data = ['action' => $action];
            }

            return view('Users::matriculations.matriculation')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Users::matriculations.not_found_message'), __('toastr.error'));
            logError($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            logError($e);
            return abort(500);
        }
    }

    public function show($id)
    {
        try {
            return $this->fetch($id, 'show');
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function edit($id)
    {
        try {
            return $this->fetch($id, 'edit');
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function update(MatriculationRequest $request, $id)
    {
        try {
            DB::beginTransaction();


            $matriculation = Matriculation::findOrFail($id);
            $matriculation->touch();

            $maxSelectedYear = collect($request->get('years'))->max();

            $matriculation->course_year = $maxSelectedYear;

            $matriculation->save();

            // disciplines
            $userDisciplines = [];
            $yearsWithDisciplines = [];
            $allDisciplinesOffCurricularYear = [];
            $disciplineByYear = $request->get('disciplines');
            foreach ($disciplineByYear as $year => $disciplines) {
                if (is_array($disciplines) && count($disciplines)) {
                    $yearsWithDisciplines[] = (int)$year;
                    foreach ($disciplines as $d) {
                        $userDisciplines[$d] = ['exam_only' => false];
                        if ((int)$year !== (int)$maxSelectedYear) {
                            $allDisciplinesOffCurricularYear[$d] = false;
                        }
                    }
                }
            }

            // exam only disciplines
            $examOnlyDisciplinesByYear = $request->get('disciplines_exam_only');
            if (is_array($examOnlyDisciplinesByYear)) {
                foreach ($examOnlyDisciplinesByYear as $year => $disciplines) {
                    if (is_array($disciplines) && count($disciplines)) {
                        foreach ($disciplines as $d) {
                            $userDisciplines[$d]['exam_only'] = true;
                            $allDisciplinesOffCurricularYear[$d] = true;
                        }
                    }
                }
            }

            // classes
            
            $myClasses= $matriculation->classes;
            $userClasses = [];
            $yearsWithClasses = [];
            foreach ($request->get('classes') as $year => $class) {
                if ($class) {
                    $yearsWithClasses[] = $year;
                    $userClasses[] = $class;
                }
            }
            
              $myClassIds = $myClasses->pluck('id')->toArray();
           // Verifique se os arrays são diferentes
            if (count($myClassIds) !== count($userClasses) || !empty(array_diff($myClassIds, $userClasses))) {
                // Os arrays são diferentes
                echo "Os arrays são diferentes\n";
                $ConfirmaClasseChange = DB::table('tb_change_class_student')
                ->where('id_confirmation_matriculation',$matriculation->id)
                ->where('lectiveYear',$matriculation->lective_year)
                ->whereNull('deleted_at')
                ->get();

                if($ConfirmaClasseChange->isEmpty()){
                    Toastr::warning(__('A forLEARN detectou uma alteração na turma do estudante, para efectuar este processo, deve-se criar primeiramente um requerimento de mudança de turma.'), __('toastr.warning'));
                    return redirect()->route('matriculations.show', $id);
                }

                foreach($userClasses as $change){
                    $ClasseChange = DB::table('tb_change_class_student as ch')
                    ->where('id_confirmation_matriculation',$matriculation->id)
                    ->join('article_requests as art', 'art.id', '=','ch.article_request_id')
                    ->where('ch.lectiveYear',$matriculation->lective_year)
                    ->where('id_new_class',$change)
                    ->where('ch.status',"Pending")
                    ->whereNull('ch.deleted_at')
                    ->select(['ch.id','ch.id_old_class','ch.id_new_class','ch.lectiveYear','ch.status as statuschange','art.status'])
                    ->first();

                    if($ClasseChange){
                        switch($ClasseChange->status){
                            case 'pending':
                                Toastr::warning(__('A forLEARN detectou um requerimento de mudança de turma, mas para prosseguires com a mudança, deves fazer o pagamento do emolumento na tesouraria.'), __('toastr.warning'));
                                return redirect()->route('matriculations.show', $id);
                            break;
                            case'total':
                                DB::table('tb_change_class_student')
                                ->where('id', $ClasseChange->id)
                                ->update([
                                    'status' => "done",
                                    'updated_by' => Auth::user()->id,
                                    'updated_at' => Carbon::now()
                                 ]);
                            break;
                        }
                    };
                }


            } 

            if ($yearsWithDisciplines !== $yearsWithClasses) {
                return redirect()->back()->withErrors(['Definição de turmas e/ou disciplinas inválida.'])->withInput();
            }

            if (!empty($userDisciplines)) {
                $matriculation->disciplines()->sync($userDisciplines);
            }

            if (!empty($userClasses)) {
                $matriculation->classes()->sync($userClasses);
            }

            $user = $matriculation->user();


            //$this->changeState("CHANGE", $matriculation->user->id, null);



            // return  ArticleRequest::whereUserId($matriculation->user->id)->get();
            //return  ArticleRequest::whereUserId(4429)->get();


//
//            $articleRequets = [];
//            // Confirmação de matrícula (id: 8)
//            $r1 = createAutomaticArticleRequest($user->id, 8, null, null);
//            if (!$r1) {
//                throw new Exception('Could not create automatic [Confirmação de matrícula (id: 8)] article request payment for student (id: ' . $user->id . ') matriculation');
//            }
//            $articleRequets[$r1]['updatable'] = false;
//
//            // Emissão de Cartão de Estudante (id: 31)
//            $r2 = createAutomaticArticleRequest($user->id, 31, null, null);
//            if (!$r2) {
//                throw new Exception('Could not create automatic [Emissão de Cartão de Estudante (id: 31)] article request payment for student (id: ' . $user->id . ') matriculation');
//            }
//            $articleRequets[$r2]['updatable'] = false;
//
//            // Pagamento de Propina
//            $courseID = $user->courses()->first()->id;
//            $articlePropina = Article::whereHas('monthly_charges', function ($q) use ($courseID) {
//                $q->where('course_id', $courseID);
//            })->first();
//            $r3 = createAutomaticArticleRequest($user->id, $articlePropina->id, Carbon::now()->format('Y'), 3);
//            if (!$r3) {
//                throw new Exception('Could not create automatic [Pagamento de Propina (id: ' . $articlePropina->id . ')] article request payment for student (id: ' . $user->id . ') matriculation');
//            }
//            $articleRequets[$r3]['updatable'] = false;
//
            // Cadeiras em atraso
            foreach ($allDisciplinesOffCurricularYear as $offDiscipline => $examOnly) {
                if ($examOnly) {
                    // Inscrição Por Exame Cadeira Em Atraso (id: 41)
                    //$r4 = createAutomaticArticleRequest($user->id, 41, null, null);

                    //Query para retornar o id do request de um usuario caso tenha cadeira para exame
                    /*$artc_exame = DB::table('article_requests')
                        ->leftJoin('users', 'users.id', '=', 'article_requests.user_id')
                        ->where('article_requests.user_id', $matriculation->user->id)
                        ->where('article_requests.article_id', 41)
                        ->select('article_requests.id')
                        ->get();*/
                    /*$artc_exame = ArticleRequest::whereUserId($matriculation->user->id)->where('article_id', 41)->get();
                    //return $artc_exame->pluck('id');
                        $plan = false;
                        foreach ($artc_exame as $value) {

                            if($offDiscipline === $value->discipline_id) {
                                $plan = true;
                                return $plan;
                            }
                            if($plan === false){

                                $group_exam = [
                                    'discipline_id' => $offDiscipline,
                                    'article_request_id' => $value->id,
                                    'user_id' => $matriculation->user->id
                                ];
                                DisciplineArticle::insert($group_exam);
                            }

                     }*/

                    /*return $q = DB::table('disciplines_articles')
                        ->leftJoin('users', 'users.id', '=', 'disciplines_articles.user_id')
                        ->where('disciplines_articles.user_id', $matriculation->user->id)
                        ->select('disciplines_articles.id')
                        //->groupBy('article_request_id')
                        //->groupBy('discipline_id')
                        ->get();*/

                            //return $group_exam;

                            //return $artc_exame->pluck('id');


                                /*$dd = new DisciplineArticle;
                                $dd->discipline_id = $offDiscipline;
                                $dd->article_request_id = $artc_exame->pluck('id');
                                $dd->user_id = $matriculation->user->id;


                                $dd->save();*/

                        //return $group_exam;


                         /*$artc_exame = ArticleRequest::whereUserId($matriculation->user->id)->where('article_id', 41)->get();
                        //return $artc_exame;
                            foreach ($artc_exame as $value) {

                        //Array que vai para a tabela 'disciplines_request' caso existirem disciplinas em atraso registados para exame

                            $group_exam = [
                                'discipline_id' => $offDiscipline,
                                'article_request_id' => $value->id,
                                'user_id' => $matriculation->user->id
                            ];

                        }*/
                        //DisciplineArticle::insert($group_exam);
                        //return $group_exam;



                    /*if (!$r4) {
                        throw new Exception('Could not create automatic [Inscrição Por Exame Cadeira Em Atraso (id: 41)] article request payment for student (id: ' . $user->id . ') matriculation');
                    }
                    $articleRequets[$r4]['updatable'] = true;*/
                } else {
                    // Inscrição Por Frequência Cadeira Em Atraso (id: 42)
                    //$r5 = createAutomaticArticleRequest($user->id, 42, null, null);

                     //Query para retornar o id do request de um usuario caso tenha cadeira para frequencia
                    /*$artc_freq = DB::table('article_requests')
                        ->leftJoin('users', 'users.id', '=', 'article_requests.user_id')
                        ->where('article_requests.user_id', $matriculation->user->id)
                        ->where('article_requests.article_id', 42)
                        ->select('article_requests.id')
                        ->get();

                    //Array que vai para a tabela 'disciplines_request' caso existirem disciplinas em atraso registados para frequencia
                     $group_freq = [
                            'discipline_id' => $offDiscipline,
                            'article_request_id' => $artc_freq,
                            'user_id' => $matriculation->user->id
                        ];
                    return $group_freq;*/

                    //DisciplineArticle::insert($group_freq);

                    /*if (!$r5) {
                        throw new Exception('Could not create automatic [Inscrição Por Frequência Cadeira Em Atraso (id: 42)] article request payment for student (id: ' . $user->id . ') matriculation');
                    }
                    $articleRequets[$r5]['updatable'] = true;*/
                }
            }

//            $matriculation->articleRequests()->sync($articleRequets);

            DB::commit();

            // Success message
            Toastr::success(__('Users::matriculations.update_success_message'), __('toastr.success'));
            return redirect()->route('matriculations.show', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Users::matriculations.not_found_message'), __('toastr.error'));
            logError($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }








    public function destroy($id)
    {
        try {


            
            DB::transaction(function () use ($id) {

            $consulta=DB::table('matriculations')->whereId($id) ->get();
            if(!$consulta->isEmpty()){
                DB::table('deleted_matriculation')->updateOrInsert([
                    'id_mat' => $consulta[0]->id,
                    'id_user_mat' => $consulta[0]->user_id,
                    'code_mat' => $consulta[0]->code,
                ],
                [
                    'course_year_mat' => $consulta[0]->course_year,
                    'created_by_mat' => $consulta[0]->created_by,
                    'updated_by_mat' => $consulta[0]->updated_by,
                    'created_at_mat' => $consulta[0]->created_at,
                    'updated_at_mat' => $consulta[0]->updated_at,
                    'Deleted_by' => Auth::user()->id 

                ]
            
            
            );

                //Pegar os article request (Emolumento)
             $article=DB::table('article_requests')
                ->where('user_id',$consulta[0]->user_id)
                ->where('created_at',$consulta[0]->created_at)
                ->get()
                ->map(function($item){
                    return $item->id;
                }) ;
                if(!$article->isEmpty()){
                    //Pegar os article request Transation(Emolumento junto com a transição)
                 $ID_Transation=DB::table('transaction_article_requests')
                    ->whereIn('article_request_id',$article)
                    ->get() 
                    ->map(function($item){
                        return $item->transaction_id;
                    }) ;

                    //Eliminar artigo e transação
                    $Transacao_request_delete = DB::table('transaction_article_requests')
                    ->whereIn('transaction_id',$ID_Transation)
                    ->delete();

                    //Eliminar transação_informacao
                    $Transacao_inf = DB::table('transaction_info')
                    ->whereIn('transaction_id',$ID_Transation)
                    ->delete();

                    //Eliminar transação_recibo
                    $Transacao_recibo = DB::table('transaction_receipts')
                    ->whereIn('transaction_id',$ID_Transation)
                    ->delete();

                    //emolumento da matricula
                    $deleted_article = DB::table('matriculation_article_requests')
                    ->where('matriculation_id',$consulta[0]->id)
                    ->delete();

                    //Eliminar transação
                    $Transacao = DB::table('transactions')
                    ->whereIn('id',$ID_Transation)
                    ->delete();
               

                    //Eliminar emolumento
                    $article_requeste = DB::table('article_requests')
                    ->whereIn('id',$article)
                    ->delete();


                    //Todas as tabelas relacionadas a matriculas
                    //matriculation_article_requests
                    //matriculation_classes
                    //matriculation_disciplines


                    $deleted_classe = DB::table('matriculation_classes')
                    ->where('matriculation_id',$consulta[0]->id)
                    ->delete();
    
                    $deleted_Disciplina = DB::table('matriculation_disciplines')
                    ->where('matriculation_id',$consulta[0]->id)
                    ->delete();

                    //Apagar a matricula ---geral
                    $deleted_Matricula = DB::table('matriculations')
                    ->where('id',$consulta[0]->id)
                    ->delete();
                }

                
            }
            
        });
            
         
            // Success message
            Toastr::success(__('Users::matriculations.destroy_success_message'), __('toastr.success'));
            return redirect()->route('matriculations.index');
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Users::matriculations.not_found_message'), __('toastr.error'));
           return $e;
           logError($e);
           return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            
            return $e;
           
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    // HELPERS //////


























    

    protected function studentsWithCourseWithoutMatriculationSelectList()
    {
        $users = User::whereHas('roles', function ($q) {
            $q->whereIn('id', [6, 15]);
        })
            ->whereHas('courses')
            // ->doesntHave('matriculation')
            ->with(['parameters' => function ($q) {
                $q->whereIn('code', ['nome', 'n_mecanografico']);
            }])
            ->get()
            ->map(function ($user) {
                $displayName = $this->formatUserName($user);
                
                return ['id' => $user->id, 'display_name' => $displayName];
            });

        return $users->sortBy(function ($item) {
            return strtr(
                utf8_decode($item['display_name']),
                utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'),
                'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY'
            );
        });
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








    public static function pre_matricula_confirma_emolumento($lectiveYearSelected){
     
        $confirm=EmolumentCodevLective("confirm",$lectiveYearSelected)->first();
        $Prematricula=EmolumentCodevLective("p_matricula",$lectiveYearSelected)->first() ;   
        $emolumentos=[];

        if($confirm!=null){
            $emolumentos[]=$confirm->id_emolumento;
        }
        if($Prematricula!=null){
            $emolumentos[]=$Prematricula->id_emolumento;
        }
        return $emolumentos;


    }

    // AJAX //////




    public function ajax()
    {                     
        try {
            
            $tranf_type='payment';
            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
                ->first();
             
             $emolumento_confirma_prematricula= self::pre_matricula_confirma_emolumento( $lectiveYearSelected->id);
            
                $model = Matriculation::join('users as u0', 'u0.id', '=', 'matriculations.user_id')
                ->where('matriculations.lective_year', $lectiveYearSelected->id)
                    ->join('users as u1', 'u1.id', '=', 'matriculations.created_by')
                    ->leftJoin('users as u2', 'u2.id', '=', 'matriculations.updated_by')
                    ->leftJoin('users as u3', 'u3.id', '=', 'matriculations.deleted_by')
                    ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'u0.id')     
                    ->join('courses_translations as ct', function ($join) {
                        $join->on('ct.courses_id', '=', 'uc.courses_id');
                        $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('ct.active', '=', DB::raw(true));
                    })

                    ->leftJoin('matriculation_classes as mc', 'mc.matriculation_id', '=', 'matriculations.id')
                    ->join('classes as cl', function ($join)  {
                        $join->on('cl.id', '=', 'mc.class_id');
                        $join->on('mc.matriculation_id', '=', 'matriculations.id');
                        $join->on('matriculations.course_year', '=', 'cl.year');
                    })                             
                                        

                    ->leftJoin('user_parameters as u_p', function ($join) {
                         $join->on('u0.id', '=', 'u_p.users_id')
                         ->where('u_p.parameters_id', 1);
                    })

                    ->leftJoin('user_parameters as up_meca', function ($join) {
                         $join->on('u0.id','=','up_meca.users_id')
                         ->where('up_meca.parameters_id', 19);
                    })
                    ->leftJoin('user_parameters as up_bi', function ($join) {
                        $join->on('u0.id','=','up_bi.users_id')
                        ->where('up_bi.parameters_id', 14);
                   })

                    //emarq

                ->leftJoin('user_parameters as up_sexo', function ($join) {
                    $join->on('u0.id','=','up_sexo.users_id')
                    ->where('up_sexo.parameters_id', 2);
                })
                ->leftJoin('parameter_options as po_sexo', function ($join) {
                    $join->on('po_sexo.id','=','up_sexo.value');
                    
                })

                ->leftJoin('user_parameters as up_contact', function ($join) {
                    $join->on('u0.id','=','up_contact.users_id')
                    ->where('up_contact.parameters_id', 36);
                })

                ->leftJoin('user_parameters as up_escola', function ($join) {
                    $join->on('u0.id','=','up_escola.users_id')
                    ->where('up_escola.parameters_id', 313);
                })

                ->leftJoin('user_parameters as up_data_nascimento', function ($join) {
                    $join->on('u0.id','=','up_data_nascimento.users_id')
                    ->where('up_data_nascimento.parameters_id', 5);
                })

                ->leftJoin('scholarship_holder as hold', function ($join) {
                    $join->on('hold.user_id', '=', 'u0.id')
                    ->where('hold.are_scholarship_holder', 1);
                })
                ->leftJoin('scholarship_entity as ent', function ($join) {
                    $join->on('ent.id', '=', 'hold.scholarship_entity_id');
                })

                ->leftJoin('regime_especial as re', 're.user_id', '=', 'u0.id')
                ->leftJoin('rotacao_regime_especial as rre', 'rre.id', '=', 're.rotation_id')

                //emarq

                  ->leftJoin('article_requests as art_requests',function ($join) use($emolumento_confirma_prematricula)
                    {
                        $join->on('art_requests.user_id','=','u0.id')
                        ->whereIn('art_requests.article_id', $emolumento_confirma_prematricula)
                         ->where('art_requests.deleted_by', null) 
                    ->where('art_requests.deleted_at', null);
                    })
                 
                 
                    ->select([
                        'matriculations.*',
                        'u0.id as id_usuario',
                        'matriculations.code as code_matricula',
                        'up_meca.value as matricula',
                        'art_requests.status as state' ,
                        'up_bi.value as n_bi',
                        //emarq
                        'rre.nome as regime',
                        'po_sexo.code as sexo',  
                        'up_contact.value as contacto',  
                        'up_escola.value as escola',  
                        'up_data_nascimento.value as data_nascimento',  
                        'ent.company as entidade',
                        'ent.type as categoria',
                        //emarq

                        'cl.display_name as classe',
                       
                        'u_p.value as student',
                        'u0.email as email',
                        'u1.name as criado_por',
                        'u2.name as actualizado_por',
                        'u3.name as deletador_por',
                        'ct.display_name as course',
                        //sedrac
                        'uc.courses_id as id_course'
                    ])
                    ->groupBy('u_p.value')
                    ->distinct('id');
                    
                     

                    return Datatables::of($model)
                        ->addColumn('actions', function ($item) {
                            return view('Users::matriculations.datatables.actions')->with('item', $item);
                        })
                    ->addColumn('states', function($state){
                        return view('Users::matriculations.datatables.states')->with('state',$state);
                   })
                    ->rawColumns(['actions', 'states'])
                    // ->rawColumns(['actions'])
                    ->addIndexColumn()
                    ->toJson();
        } catch (Exception | Throwable $e) {
            logError($e);
            return response()->json($e->getMessage(), 500);
        }
    }









    public function ajaxUserData($id)
    {
        try {
            $user = User::where('id', $id)
                ->with([
                    'courses' => function ($q) {
                        $q->with([
                            'currentTranslation',
                            'studyPlans' => function ($q) {
                                $q->with([
                                    'study_plans_has_disciplines' => function ($q) {
                                        $q->with([
                                            'discipline' => function ($q) {
                                                $q->with('currentTranslation');
                                            }
                                        ]);
                                    }
                                ]);
                            },
                            'classes'
                        ]);
                    },
                    'classes',
                    'roles'
                ])
                ->first();

                if($user->courses == Null && $user->classes == NULL) {
                    return "Sem Disciplinas";
                }
                else {
                    return response()->json($user, 200);   
                }
                
        } catch (Exception | Throwable $e) {
            logError($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function ajaxUserPdf($id)
    {
        return "/storage/matriculations/M+R.pdf";
    }
    public function getWhatsapp($whatsapp){
        
        try{
            $isApiRequest = request()->header('X-From-API') === 'flask';
            $tokenRecebido = request()->bearerToken();
            if($isApiRequest){
                if($tokenRecebido!== env('FLASK_API_TOKEN')){
                    return response()->json(['error' => 'Unauthorized'], 401);
                }
            }
            $matriculationId = DB::table('users')
                ->join('matriculations as m', 'm.user_id', '=', 'users.id')
                ->where('users.user_whatsapp', $whatsapp)
                ->select('m.id')
                ->first();
            $id = $matriculationId->id;
            if (is_null($id))  {
                    return response()->json([
                        'error' => 'Matricula não encontrado para este número de WhatsApp.'
                    ], 404);
                }

            return $this->openReport($id);
        }
        catch (Exception | Throwable $e) {
            logError($e);
            return response()->json($e->getMessage(), 500);
        }   
        

    }



    public static function openReport($id, $api=null)
    {
        
        $matriculation = Matriculation::where('id', $id)
            ->with([
                'disciplines' => function ($q) {
                    $q->with([
                        'currentTranslation',
                        'study_plans_has_disciplines' => function ($q) {
                            $q->with([
                                'discipline_period' => function ($q) {
                                    $q->with('currentTranslation');
                                }
                            ]);
                        }
                    ]);
                },
                'classes' => function ($q) {
                    $q->with([
                        'room' => function ($q) {
                            $q->with('currentTranslation');
                        }
                    ]);
                },
                'user' => function ($q) {
                    $q->with([
                        'roles' => function ($q) {
                            $q->with([
                                'currentTranslation'
                            ]);
                        },
                        'parameters' => function ($q) {
                            // $q->where('code', 'n_mecanografico');
                            $q->with([
                                'currentTranslation',
                                'groups'
                            ]);
                        },
                        'courses' => function ($q) {
                            $q->with('currentTranslation');
                        }
                    ]);
                }])
            ->firstOrFail();
        
        
        $parameterPhoto = $matriculation->user->parameters->where('code', 'fotografia')->first();
        $photo = $parameterPhoto ? $parameterPhoto->pivot->value : '';

        $parameterNome = $matriculation->user->parameters->where('code', 'nome')->first();
        $personalName = $parameterNome ? $parameterNome->pivot->value : '';

        $parameterBI = $matriculation->user->parameters->where('code', 'n_bilhete_de_identidade')->first();
        $personalBI = $parameterBI ? $parameterBI->pivot->value : '';

        $parameterMobilePhone = $matriculation->user->parameters->where('code', 'telemovel_principal')->first();
        $personalMobilePhone = $parameterMobilePhone ? $parameterMobilePhone->pivot->value : '';

        $parameterMobilePhoneAlt = $matriculation->user->parameters->where('code', 'telemovel_alternativo')->first();
        $personalMobilePhoneAlt = $parameterMobilePhoneAlt ? $parameterMobilePhoneAlt->pivot->value : '';

        $parameterPhone = $matriculation->user->parameters->where('code', 'telefone_fixo')->first();
        $personalPhone = $parameterPhone ? $parameterPhone->pivot->value : '';

        $parameterEmail = $matriculation->user->parameters->where('code', 'e-mail_2')->first();
        $personalEmail = $parameterEmail ? $parameterEmail->pivot->value : '';

        $parameterMecanografico = $matriculation->user->parameters->where('code', 'n_mecanografico')->first();
        $personalMecanografico = $parameterMecanografico ? $parameterMecanografico->pivot->value : '';

        // TODO: ver como ir buscar e formatar a morada
        // $parameterAddress = $matriculation->user->parameters->where('code', 'morada_principal')->first();
        // $personalAddress = $parameterAddress ? $parameterAddress->pivot->value : '';

        $courseModel = $matriculation->user->courses->first();
        $curricularCourse = $courseModel ? $courseModel->currentTranslation->display_name : null;

        $curricularYear = $matriculation->course_year;

        $classesByYear = [];
        foreach ($matriculation->classes as $class) {
            $classesByYear[$class->year] = $class->code;
        }

        $disciplinesExam = [];
        $disciplinesLate = [];
        $disciplines = [];
        foreach ($matriculation->disciplines as $discipline) {
            $studyPlan = $discipline->study_plans_has_disciplines->first();
            $disciplineYear = $studyPlan ? $studyPlan->years : null;

            $d = [
                'name' => $discipline->currentTranslation->display_name,
                'regime' => $studyPlan ? $studyPlan->discipline_period->currentTranslation->display_name : 'N/A',
                'year' => $disciplineYear ?: 'N/A',
                'class' => $classesByYear[$disciplineYear] ?? 'N/A',
                'code' => $discipline->code
            ];

            if ($discipline->pivot->exam_only) {
                $disciplinesExam[] = $d;
            } elseif ($disciplineYear !== $curricularYear) {
                $disciplinesLate[] = $d;
            } else {
                $disciplines[] = $d;
            }
        }

        $declaration = new DeclarationController();
        $disciplines = $declaration->ordena_plano($disciplines);
        $disciplinesLate = $declaration->ordena_plano($disciplinesLate);
        $disciplinesExam = $declaration->ordena_plano($disciplinesExam);



        $institution = Institution::latest()->first();
        $titulo_documento = "Boletim de Matrícula";
        $documentoGerado_documento = "Documento gerado a";
        $documentoCode_documento = 6;

        $lective_year =  LectiveYear::with('currentTranslation')->find($matriculation->lective_year);

        $data = [
            'photo' => $photo,
            'matriculation_generated_date' => $matriculation->created_at,
            'matriculation_numb' => $matriculation->code,
            'personal' => [
                'name' => $personalName,
                'bi' => $personalBI,
                'mobile_phone' => $personalMobilePhone,
                'mobile_phone_alt' => $personalMobilePhoneAlt,
                'phone' => $personalPhone,
                'email' => $matriculation->user->email,
                'email_2' => $personalEmail,
                'n_mecanografico' => $personalMecanografico
            ],
            'curricular' => [
                'course' => $curricularCourse,
                'year' => $curricularYear,
            ],
            'disciplines_exam' => $disciplinesExam,
            'disciplines_late' => $disciplinesLate,
            'disciplines' => $disciplines,
            'created_by' => isset(auth()->user()->name)?auth()->user()->name:null,
            
            'institution' => $institution,
            'titulo_documento' => $titulo_documento,
            'documentoGerado_documento' => $documentoGerado_documento,
            'documentoCode_documento' => $documentoCode_documento,

            'matriculation_lective_year' => $lective_year,
            'matriculation_course_year' => $matriculation->course_year,
            "matriculation_obj" => $matriculation
        ];

        // return view('Users::matriculations.report', $data);

        // $footer_html = view()->make('Users::matriculations.partials.pdf_footer', compact('institution'))->render();
        $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
        
        $pdf = PDF::loadView('Users::matriculations.report', $data)
            ->setOption('margin-top', '10')
            ->setOption('margin-top', '2mm')
            ->setOption('margin-left', '2mm')
            ->setOption('margin-bottom', '2cm')
            ->setOption('margin-right', '2mm')
            ->setOption('header-html', '<header></header>')
            ->setOption('footer-html', $footer_html)
            ->setPaper('a4');

        // return $pdf->download('matriculation.pdf');
        if($api != null){
            $filename = 'Matricula_'.$matriculation->code.'.pdf';
            return response($pdf->output(), 200)->header('Content-Type', 'application/pdf')->header('Content-Disposition', 'inline; filename="'.$filename.'"');
        }
        return $pdf->stream($matriculation->code . '.pdf');
    }






    public function changeState($type, $userId, $maxSelectedYear, $courseID)
    {
        if ($type == "STORE") {
            //Avaliar apenas se ele esta a ser matriculado no ultimo ano do curso - se estiver a ser matriculado, mudar o estado para finalista
            //$yearsMatriculated = Matriculation::whereUserId($userId)->get();
            $maxCourseYear = Course::whereId($courseID)->firstOrFail();

                //se o ano em que ele esta matriculado for igual ao maior do curso. (ex: se estiver no 5 ano e o curso tem 5 anos.)
                    if ($maxCourseYear->duration_value == $maxSelectedYear) {
                        UserState::updateOrCreate(
                            ['user_id' => $userId],
                            ['state_id' => 19, 'courses_id' => null] //19 => Finalista
                        );
                        UserStateHistoric::create([
                                    'user_id' => $userId,
                                    'state_id' => 19
                                ]);

                    }



            /*$discipline_ids = [181,551,237,637,299,527,129,395,468,619,430,229,72,565,315];
            if (count($discipline) == 1) {
                foreach ($discipline_ids as $id) {
                    $user = User::whereId($userId)->with([
                        'matriculation'
                    ])->first();
                    $id = DB::table('matriculation_disciplines')
                            ->where('matriculation_disciplines.matriculation_id', $user->matriculation->id)
                            ->where('matriculation_disciplines.discipline_id', $id)
                            ->get();

                    if (!$id->isEmpty()) {
                        UserState::updateOrCreate(
                            ['user_id' => $userId],
                            ['state_id' => 15] //Finalista
                        );
                        UserStateHistoric::create([
                            'user_id' => $userId,
                            'state_id' => 15
                        ]);
                    }
                }
            }*/
        } elseif ($type == "CHANGE") {
            $user_state = UserState::whereUserId($userId)->first();

            if (!$user_state == null && $user_state->state_id == 9) {
                UserState::updateOrCreate(
                    ['user_id' => $userId],
                    ['state_id' => 7]
                );
                UserStateHistoric::create([
                        'user_id' => $userId,
                        'state_id' => 7
                    ]);
            }
        }
    }






    

    public function getMatriculationBy($lectiveYear)
    {
        try {
            
            //$currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
            ->where('id', $lectiveYear)
            //->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
            ->first();
            
            $emolumento_confirma_prematricula= self::pre_matricula_confirma_emolumento( $lectiveYearSelected->id);
            
            $model = Matriculation::join('users as u0', 'u0.id', '=', 'matriculations.user_id')
                ->join('users as u1', 'u1.id', '=', 'matriculations.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'matriculations.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'matriculations.deleted_by')
                ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'u0.id')
                ->join('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'uc.courses_id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })

                ->leftJoin('matriculation_classes as mc', 'mc.matriculation_id', '=', 'matriculations.id')
                ->join('classes as cl', function ($join)  {
                    $join->on('cl.id', '=', 'mc.class_id');
                    $join->on('mc.matriculation_id', '=', 'matriculations.id');                    
                    $join->on('matriculations.course_year', '=', 'cl.year');
                })

                ->leftJoin('user_parameters as u_p', function ($join) {
                  $join->on('u0.id', '=', 'u_p.users_id')
                  ->where('u_p.parameters_id', 1);
                })
                ->leftJoin('user_parameters as up_meca', function ($join) {
                    $join->on('u0.id', '=', 'up_meca.users_id')
                            ->where('up_meca.parameters_id', 19);
                })
                ->leftJoin('user_parameters as up_bi', function ($join) {
                    $join->on('u0.id','=','up_bi.users_id')
                    ->where('up_bi.parameters_id', 14);
               })

                //emarq

                ->leftJoin('user_parameters as up_sexo', function ($join) {
                    $join->on('u0.id','=','up_sexo.users_id')
                    ->where('up_sexo.parameters_id', 2);
                })
                ->leftJoin('parameter_options as po_sexo', function ($join) {
                    $join->on('po_sexo.id','=','up_sexo.value');
                    
                })

                ->leftJoin('user_parameters as up_contact', function ($join) {
                    $join->on('u0.id','=','up_contact.users_id')
                    ->where('up_contact.parameters_id', 36);
                })

                ->leftJoin('user_parameters as up_escola', function ($join) {
                    $join->on('u0.id','=','up_escola.users_id')
                    ->where('up_escola.parameters_id', 313);
                })

                ->leftJoin('user_parameters as up_data_nascimento', function ($join) {
                    $join->on('u0.id','=','up_data_nascimento.users_id')
                    ->where('up_data_nascimento.parameters_id', 5);
                })

                ->leftJoin('scholarship_holder as hold', function ($join) {
                    $join->on('hold.user_id', '=', 'u0.id')
                    ->where('hold.are_scholarship_holder', 1);
                })
                ->leftJoin('scholarship_entity as ent', function ($join) {
                    $join->on('ent.id', '=', 'hold.scholarship_entity_id');
                })

                //emarq

                ->leftJoin('article_requests as art_requests',function ($join) use ($emolumento_confirma_prematricula)
                {
                    $join->on('art_requests.user_id','=','u0.id')
                    ->whereIn('art_requests.article_id', $emolumento_confirma_prematricula);
                })


                // ->leftJoin('transaction_article_requests as trant_art_requests',function ($join)
                // {
                //     $join->on('trant_art_requests.article_request_id', '=', 'art_requests.id');
                //     // ->whereIn('art_requests.article_id', [117, 79]);
                // })

                // ->leftJoin('transaction_receipts as trant_receipts',function ($join)
                // {
                //     $join->on('trant_receipts.transaction_id', '=', 'trant_art_requests.transaction_id');
                //     // ->whereIn('art_requests.article_id', [117, 79]);
                // })
                
                ->select([
                    'matriculations.*',
                    'matriculations.code as code_matricula',
                    'up_meca.value as matricula',
                    'u0.id as id_usuario',
                    'art_requests.status as state', 
                    'up_bi.value as n_bi', 
                     //emarq
                    'po_sexo.code as sexo',  
                    'up_contact.value as contacto',  
                    'up_escola.value as escola',  
                    'up_data_nascimento.value as data_nascimento',  
                    'ent.company as entidade',
                    'ent.type as categoria',
                    //emarq
                    // 'trant_receipts.code as recibo', 
                    'cl.display_name as classe',
                    'u_p.value as student',
                    'u0.email as email',
                    'u1.name as criado_por',
                    'u2.name as actualizado_por',
                    'u3.name as deletador_por',
                    'ct.display_name as course',
                    //sedrac
                    'uc.courses_id as id_course'
                ])
                ->where('art_requests.deleted_by', null) 
                ->where('art_requests.deleted_at', null)
                
                ->groupBy('u_p.value')
                ->distinct('id')
                // ->whereBetween('matriculations.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date]);
                ->where('matriculations.lective_year', $lectiveYearSelected->id);
                return Datatables::Eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('Users::matriculations.datatables.actions')->with('item', $item);
                })
                ->addColumn('states', function($state){
                    return view('Users::matriculations.datatables.states')->with('state',$state);
                })
                ->rawColumns(['actions', 'states'])
                // ->rawColumns(['actions'])
                ->addIndexColumn()
                ->toJson();
        }
         catch (Exception | Throwable $e) {
            logError($e);
            return response()->json($e->getMessage(), 500);
        }
    }
    public function matricula_anolectivo($id_matricula,$idanolectivo){
        $action="show";
        try {
            $lectiveYearSelected = DB::table('lective_years')
            ->where('id', $idanolectivo)
            //->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
            ->first();
             $matriculation = $matriculation = Matriculation::where('matriculations.user_id', $id_matricula)
                          ->where("matriculations.lective_year",$lectiveYearSelected->id)
            // ->whereBetween('matriculations.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                ->with([
                    'disciplines',
                    'classes',
                    'user' => function ($q) {
                        $q->with([
                            'courses' => function ($q) {
                                $q->with([
                                    'currentTranslation',
                                    'studyPlans' => function ($q) {
                                        $q->with([
                                            'study_plans_has_disciplines' => function ($q) {
                                                $q->with([
                                                    'discipline' => function ($q) {
                                                        $q->with('currentTranslation');
                                                    }
                                                ]);
                                            }
                                        ]);
                                    },
                                    'classes'
                                ]);
                            }
                        ]);
                    }])
                ->first();

            if ($matriculation) {
                $stored = [];
                foreach ($matriculation->classes as $class) {
                    $stored['years'][] = $class->year;
                    $stored['classes'][$class->year] = $class->id;
                }

                foreach ($matriculation->disciplines as $discipline) {
                    $stored['disciplines'][] = $discipline->id;
                    if ($discipline->pivot->exam_only) {
                        $stored['disciplines_exam_only'][] = $discipline->id;
                    }
                }

                $data = [
                    'action' => $action,
                    'userName' => $this->formatUserName($matriculation->user),
                    'matriculation' => $matriculation,
                    'stored' => $stored
                ];
            } else {
                $data = ['action' => $action];
            }

            return view('Users::matriculations.matriculation')->with($data);
        } catch (ModelNotFoundException $e) {
            return $e;
            Toastr::error(__('Users::matriculations.not_found_message'), __('toastr.error'));
            logError($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return abort(500);
        }
    }
    
    public function generateMatriculationGep($ano_lectivo){
        try{
           
           return Excel::download(new MatriculationExport($ano_lectivo), 'matriculados-gep.xlsx');
          
        } catch (Exception | Throwable $e) {
           
            Log::error($e);
            Toastr::error($e->getMessage(), __('toastr.error'));
            return redirect()->back();
        }
    }
}


















































































