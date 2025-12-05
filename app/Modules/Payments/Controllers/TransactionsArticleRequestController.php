<?php

namespace App\Modules\Payments\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\LectiveYear;
use App\Modules\Payments\Models\ArticleRequest;
use App\Modules\Payments\Models\Bank;
use App\Modules\Payments\Models\Transaction;
use App\Modules\Payments\Models\TransactionInfo;
use App\Modules\Payments\Models\TransactionReceipt;
use App\Modules\Payments\Requests\TransactionRequest;
use App\Modules\Payments\Models\TransactionArticleRequest;
use App\Modules\Payments\Util\TransactionsArticleRequestUtil;

use App\Modules\Users\Models\State;
use App\Modules\Users\Models\User;
use App\Modules\Users\Models\UserState;
use App\Modules\Users\Models\UserStateHistoric;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use App\Helpers\LanguageHelper;
use LynX39\LaraPdfMerger\Facades\PdfMerger;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Modules\Payments\Models\Article;
use App\Modules\Users\util\AnulateUtil;
use App\Modules\Users\Enum\CodevEnum;
use App\Modules\Payments\Util\ArticlesUtil;
use App\Model\Institution;
use Storage;
use Throwable;
use Auth;

class TransactionsArticleRequestController extends Controller
{
    private $transactionsArticleRequestUtil;
    private $articleRequests = null;
    private $anulateUtil;
    private $articlesUtil;

    function __construct()
    {
        $this->transactionsArticleRequestUtil = new TransactionsArticleRequestUtil();
        $this->anulateUtil = new AnulateUtil();
        $this->articlesUtil = new ArticlesUtil();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    private function anyParcial($articleRequests){
        foreach($articleRequests as $articleRequest){
            if($articleRequest->status == "partial")
                return true;
        }
        return false;
    }

    public function create(\Illuminate\Http\Request $request)
    {
        try {
            $anoLectivo = 0;
            if ($request->selectAnoLetivo == 0) {
                $currentData = Carbon::now();
                $lectiveYear = DB::table('lective_years')
                    ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                    ->first();
                $anoLectivo = $lectiveYear->id;
            } else {
                $anoLectivo = $request->selectAnoLetivo;
            }
            $regraImplementada = null;
            $userId = $request->user;
            $arrayMonth = [];
            $arrayMonth_getRegraImplementada = [];
            $arrayMonth_getRegraImplementEmolu = [];
            $articlesRequestedToPay = $request->checked_values;
            $getRegraImplementEmolu = null;
            $user = User::where('id', $userId)
                ->with([
                    'parameters' => function ($q) {
                        $q->whereIn('code', ['nome', 'n_mecanografico', 'fotografia']);
                    },
                ])
                ->firstOrFail();

            $fullNameParameter = $user->parameters->firstWhere('code', 'nome');
            $fullName = $fullNameParameter && $fullNameParameter->pivot->value ? $fullNameParameter->pivot->value : $user->name;

            $studentNumberParameter = $user->parameters->firstWhere('code', 'n_mecanografico');
            $studentNumber = $studentNumberParameter && $studentNumberParameter->pivot->value ? $studentNumberParameter->pivot->value : '000';

            $displayName = "$fullName #$studentNumber ($user->email)";

            $studentFoto = $user->parameters->firstWhere('code', 'fotografia');
            //   return  $studentFoto ;
            $user->{'display_name'} = $displayName;
            $user->{'fullname'} = $fullName;
            if ($studentFoto != '') {
                $foto = $studentFoto->pivot->value;
                $user->{'foto'} = $foto;
            } else {
                $user->{'foto'} = '';
            }

            $getBolseiro = DB::table('scholarship_holder')->where('user_id', $userId)->where('are_scholarship_holder', 1)->get();

            
            $banks = Bank::whereNull('banks.type_conta_entidade')->orderBy('display_name', 'asc')->get();
            $bankSem_referencia = DB::table('banks')->where('banks.type_conta_entidade', '!=', null)->get();
            $creditTypes = collect([['id' => 'payment', 'display_name' => __('Payments::requests.transactions.credit-payment')], ['id' => 'adjust', 'display_name' => __('Payments::requests.transactions.credit-adjust')]]);

            $lectiveYearSelected = LectiveYear::whereId($anoLectivo)->first();
            $disciplines = DB::table('articles as art')
                ->leftJoin('article_requests', 'article_requests.article_id', '=', 'art.id')
                // ArticleRequest::whereUserId($userId)
                ->leftJoin('disciplines', 'disciplines.id', '=', 'article_requests.discipline_id')
                ->leftJoin('disciplines_translations as dcp', function ($join) {
                    $join->on('dcp.discipline_id', '=', 'disciplines.id');
                    $join->on('dcp.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dcp.active', '=', DB::raw(true));
                })
                ->leftJoin('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'disciplines.courses_id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })
                ->select(['article_requests.id as article_req_id', 'dcp.display_name as discipline_name', 'article_requests.discipline_id as discipline_id', 'ct.display_name as course_name', 'dcp.abbreviation as abbreviation'])
                ->where('article_requests.user_id', $userId)
                ->whereBetween('art.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                ->get();

                $metrics = DB::table('articles as art')
                ->join('article_requests','article_requests.article_id','=','art.id')
                ->join('metricas', 'metricas.id', '=', 'article_requests.metric_id')
                ->select([
                    'article_requests.id as article_req_id',
                    'article_requests.metric_id as metric_id',
                    'metricas.nome as nome'
                ])
                ->whereNull('article_requests.deleted_at')
                ->whereNull('article_requests.deleted_by')
                ->whereNull('art.deleted_by')
                ->where('article_requests.user_id',$userId)
                ->whereBetween('art.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
            ->get();

            $getMonth = DB::table('article_requests as articles')
                ->whereIn('articles.id', $request->checked_values)
                ->select(['articles.month as mes'])
                ->get();
            foreach ($getMonth as $key => $value) {
                $arrayMonth[] = $value->mes;
            }

            
            $getRegraImplementada = $this->articlesUtil->getRegraImplementada($anoLectivo, $userId);
           

            if (count($getRegraImplementada) > 0) {
                foreach ($getRegraImplementada as $key => $value) {
                    $arrayMonth_getRegraImplementada[] = $value->mes;
                }
            } else {
                $getRegraImplementEmolu = $this->articlesUtil->getRegraImplementEmolu($anoLectivo, $userId);
                foreach ($getRegraImplementEmolu as $key => $item) {
                    $arrayMonth_getRegraImplementEmolu[] = $item->mes;
                }
            }
            
            $articleRequests = $this->getUnpaidArticleRequests($user->id, $articlesRequestedToPay);
            
            $rRegraValue = $this->articlesUtil->getRegraValue($articleRequests, $getRegraImplementada, $getRegraImplementEmolu);
            
            $getOrganizaArticl = $rRegraValue->request;
            
   
            $data = [
                'arrayMonth_getRegraImplementada' => $arrayMonth_getRegraImplementada,
                'arrayMonth_getRegraImplementEmolu' => $arrayMonth_getRegraImplementEmolu,
                'selectAnoLetivo' => $anoLectivo,
                'disciplines' => $disciplines,
                'metrics' => $metrics,
                'action' => 'create',
                'getRegraImplementada' => $getRegraImplementada,
                'getRegraImplementEmolu' => $getRegraImplementEmolu,
                'user' => $user,
                'article_requests' => $articleRequests,
                'banks' => $banks,
                'bankSem_referencia' => $bankSem_referencia,
                'credit_types' => $creditTypes,
                // 'years' => getYearList(),
                // 'months' => getLocalizedMonthsPropinas(),
                'getOrganizaArticl' => $getOrganizaArticl,
                // 'languages' => Language::whereActive(true)->get(),
                'getBolseiro' => $getBolseiro,
            ];

           
            return view('Payments::transactions.request')->with($data);
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    private function getUnpaidArticleRequests($userId, $articlesRequestedToPay)
    {   
       
        $trans_info = [];
        $articleRequests = ArticleRequest::where('user_id', $userId)
            ->where('status', '<>', 'total')
            ->whereIn('id', $articlesRequestedToPay)
            ->with([
                'article' => function ($q) {
                    $q->with(['currentTranslation', 'extra_fees']);
                },
                'transactions' => function ($q) {
                    $q->where('data_from', '=', '');
                    $q->with([
                        'transaction_info' => function ($q) {
                            $q->where('transaction_id', '<>', 206219);
                            $q->with('bank');
                        },
                    ]);
                },
            ])
            ->orderBy('base_value')
            ->get();

        if (empty($articleRequests)) {
            return redirect()->back();
        } else {
            foreach ($articleRequests as $ar) {
                $balance = 0;

                # for upload
                $value_transaction = 0;
                $value_initial = 0;
                $sub_parcial = 0;
                # for upload

                $trans_info = [];
                foreach ($ar->transactions as $t) {
                    $t->transaction_info == null ? ($trans_info[] = 'N-info') : ($trans_info[] = 'S_info');
                }
                $concat_value_transaction = '';
                $concat_value_initial = '';
                
                if (in_array('S_info', $trans_info)) {
                    $balance = 0;
                    $valor_base = $ar->transactions[0]->pivot->value;

                    

                    foreach ($ar->transactions as $t) {
                        if (isset($t->transaction_info)) {
                            $balance = -1 * $valor_base + $t->pivot->value;
                            $valor_base = -1 * $balance;

                            $value_transaction += $t->pivot->value;
                            $value_initial += $ar->transactions[0]->pivot->value;

                            if ($t->type == 'payment') {
                                $sub_parcial += $t->pivot->value;
                            }
                        }
                    }
                } else {
                    $balance += -1 * $ar->transactions[0]->pivot->value;
                }
                
                if ($ar->estado_extra_fees == 1) {
                    $balance = $balance - 1 * $balance;
                    $balance += -1 * $ar->extra_fees_value;
                
                    if ($ar->status == 'partial') {
                        $ar->{'balance'} = -1 * ($value_initial + $ar->extra_fees_value - $value_transaction);
                        
                    } else {
                        $ar->{'balance'} = $balance;
                    }
                } elseif ($ar->estado_extra_fees > 1 && $ar->estado_extra_fees <= 3) {
                    $balance = $balance - 1 * $balance;
                    $balance += -1 * $ar->extra_fees_value;
                    $ar->{'balance'} = $balance;
                } elseif ($ar->estado_extra_fees == 0) {
                    if ($ar->status == 'partial') {
                        $ar->{'balance'} = -1 * ($value_initial - $value_transaction);   
                    }else{
                        $ar->{'balance'} = $balance;
                    }
                }
            }

            return $articleRequests;
        }
    }
    private function organizaArticlEmoluRulo($articleRequestsOrganizado, $getRegraImplementada, $getRegraImplementEmolu)
    {
        if (count($getRegraImplementada) > 0) {
            foreach ($articleRequestsOrganizado as $key => $item) {
                foreach ($getRegraImplementada as $chave => $valor) {
                    if ($item->month == $valor->mes && $item->year != null && $item->discipline_id == '') {
                        $item->balance = -$valor->valor + 0;
                        $item->base_value = $valor->valor;
                    }
                }
            }
        } elseif (count($getRegraImplementEmolu) > 0 && count($getRegraImplementada) < 1) {
            foreach ($articleRequestsOrganizado as $key => $item) {
                foreach ($getRegraImplementEmolu as $chave => $valor) {
                    if ($item->month == $valor->mes && $item->year != null && $item->discipline_id == '') {
                        $item->balance = -$valor->valor + 0;
                        $item->base_value = $valor->valor;
                    }
                }
            }
        }
        return $articleRequestsOrganizado;
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $requesty
     * @param $request
     * @return void
     */

    public function store(\Illuminate\Http\Request $request, $userId)
    {
        try {
            $codeRecibo = null;
            $nextCode = null;
            $qtdRegisto = null;
            $saldoAtual = null;
            $caminhRecibo = null;
            $cretidoUser = isset($request->check_dado) ? true : false;
            $tesoreiro = Auth::user()->name;

            // Estrutura de condição que verifica a referência bancaria.
            if ($request->transaction_reference != null || $request->reference_1 != null || $request->reference_2 != null || $request->reference_3 != null || $request->reference_4 != null) {
                $ref1 = $request->reference_1;
                $ref2 = $request->reference_2;
                $ref3 = $request->reference_3;
                $ref4 = $request->reference_4;
                $referencias_usada = [];

                if (isset($request->transaction_reference)) {
                    array_push($referencias_usada, $request->transaction_reference);
                }
                if (isset($request->reference_1)) {
                    array_push($referencias_usada, $request->reference_1);
                }
                if (isset($request->reference_2)) {
                    array_push($referencias_usada, $request->reference_2);
                }
                if (isset($request->reference_3)) {
                    array_push($referencias_usada, $request->reference_3);
                }
                if (isset($request->reference_4)) {
                    array_push($referencias_usada, $request->reference_4);
                }

                $getReferencia_existe = DB::table('transaction_info')
                    ->leftJoin('transactions as trasn', function ($join) {
                        $join->on('trasn.id', '=', 'transaction_info.transaction_id');
                    })
                    ->where('trasn.data_from', '!=', 'Estorno')
                    ->whereIn('transaction_info.reference', $referencias_usada)
                    ->get();

                if (count($getReferencia_existe) > 0) {
                    Toastr::error(__('Caro tesoureiro, <b>' . $tesoreiro . '</b>.<br><br>A plataforma forLEARN detectou que a referência bancária submetida está duplicada (já existe).<br><br><br>Por favor verifique.'), __(''));
                    return redirect()->route('requests.index');
                }
            }

            // Verificar se o codigo de recibo temporario de cada transacção.
            $latestReceipt_temporary = DB::table('transaction_receipts_temporary')
                // ->latest()
                ->orderBy('transaction_receipts_temporary.id', 'DESC')
                ->get();
            if (sizeof($latestReceipt_temporary) == 0) {
                DB::table('transaction_receipts_temporary')->insert([
                    'code_temporary' => 1,
                    'created_at' => Carbon::Now(),
                ]);
                $nextCode = 2;
            } else {
                $nextCode = $latestReceipt_temporary[0]->code_temporary + 1;
            }

            $query = DB::table('transaction_receipts_temporary as tran_recept_temp')->where('tran_recept_temp.code_temporary', '=', $nextCode)->get();
            $qtdRegisto = count($query);
            if ($qtdRegisto == 0) {
                // return "estamos em processo de manutenção aguarde".$nextCode;
                DB::table('transaction_receipts_temporary')->insert([
                    'code_temporary' => $nextCode,
                    'created_at' => Carbon::Now(),
                ]);

                $user = User::findOrFail($userId);
                $getRegraImplementEmolu = null;
                $selectAnoLetivo = null;
                $cancelarMulta = 0;
                $arrayMonth = [];

                $getMonth = DB::table('article_requests as articles')
                    ->join('article_translations as at', function ($join) {
                        $join->on('at.article_id', '=', 'articles.article_id');
                        $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('at.active', '=', DB::raw(true));
                    })
                    ->whereIn('articles.id', $request->article_request_selected)
                    ->select(['articles.month as mes', 'at.display_name as display_name'])
                    ->get();
                foreach ($getMonth as $key => $value) {
                    $arrayMonth[] = $value->mes;
                }

                $getRegraImplementada = $this->articlesUtil->getRegraImplementada($request->selectAnoLetivo, $userId, null, $arrayMonth);

                if (count($getRegraImplementada) > 0) {
                } else {
                    $getRegraImplementEmolu = $this->articlesUtil->getRegraImplementEmolu($request->selectAnoLetivo, $userId, $request->article_request_selected);
                }

                // consultar se o estudante é bolseiro.

                $userBolseiro = $userId;
                if (isset($request->tipo_recibo)) {
                    $userBolseiro = $request->tipo_recibo != 'recibo_bolseiro' ? 0 : $userId;
                }

                $areAllowed = DB::table('scholarship_holder')
                ->where('are_scholarship_holder',1)
                ->where('user_id', $userBolseiro)->get();

                $articleRequests = $this->calculateCheckedArticleRequestsTaxAndValues($user, $request, $getRegraImplementEmolu, $getRegraImplementada, $cretidoUser);

                $this->organizacionEstado($user, $request);

                $lectiveYearSelected = LectiveYear::whereId($request->selectAnoLetivo)->first();
                $disciplines = DB::table('articles as art')
                    ->leftJoin('article_requests', 'article_requests.article_id', '=', 'art.id')
                    // ArticleRequest::whereUserId($userId)
                    ->leftJoin('disciplines', 'disciplines.id', '=', 'article_requests.discipline_id')
                    ->leftJoin('disciplines_translations as dcp', function ($join) {
                        $join->on('dcp.discipline_id', '=', 'disciplines.id');
                        $join->on('dcp.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('dcp.active', '=', DB::raw(true));
                    })
                    ->leftJoin('courses_translations as ct', function ($join) {
                        $join->on('ct.courses_id', '=', 'disciplines.courses_id');
                        $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('ct.active', '=', DB::raw(true));
                    })
                    ->select(['article_requests.id as article_req_id', 'dcp.display_name as discipline_name', 'disciplines.code as codigo_disciplina', 'disciplines.discipline_profiles_id as perfil_disciplina', 'article_requests.discipline_id as discipline_id', 'ct.display_name as course_name', 'dcp.abbreviation as abbreviation'])
                    ->where('article_requests.user_id', $userId)
                    ->where('article_requests.discipline_id', '!=', null)
                    ->whereIn('article_requests.id', $request->article_request_selected)
                    // ->whereBetween('art.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                    ->get();

                if (isset($request->checados)) {
                    $cancelarMulta = $request->checados;
                }

                # for upload

                $articleResume = $articleRequests['requests'];

                if (count($articleRequests['requests'])) {
                    DB::beginTransaction();

                    $totalValue = (float) $request->get('totalValue');

                    $transaction = Transaction::create([
                        'type' => $request->get('transaction_type'),
                        'value' => $totalValue,
                        'notes' => $request->get('transaction_notes'),
                    ]);

                    foreach ($articleRequests['requests'] as $articleRequestId => $articleRequestData) {
                        if ($articleRequestData['paid'] > 0) {
                            $consultaArt = DB::table('article_requests')->where('article_requests.id', '=', $articleRequestId)->first();

                            if ($consultaArt->extra_fees_value > 0) {
                                if ($consultaArt->extra_fees_value < $articleRequestData['tax']) {
                                    $ar = ArticleRequest::findOrFail($articleRequestId);
                                    $ar->extra_fees_value = (float) $articleRequestData['tax'] ? $articleRequestData['tax'] : $ar->extra_fees_value;
                                    $ar->status = $articleRequestData['state'];
                                    $ar->estado_extra_fees = 2;
                                    $ar->save();
                                } else {
                                    // return $articleRequests;
                                    $ar = ArticleRequest::findOrFail($articleRequestId);
                                    $ar->extra_fees_value = (float) $articleRequestData['tax'] ? $articleRequestData['tax'] : $ar->extra_fees_value;
                                    $ar->status = $articleRequestData['state'];
                                    $ar->estado_extra_fees = 3;
                                    $ar->save();
                                }
                            } else {
                                $ar = ArticleRequest::findOrFail($articleRequestId);
                                $ar->extra_fees_value = (float) $articleRequestData['tax'] ? $articleRequestData['tax'] : $ar->extra_fees_value;
                                $ar->status = $articleRequestData['state'];
                                $ar->estado_extra_fees = $articleRequestData['tax'] == 0 ? 0 : 1;
                                $ar->save();
                            }

                            //$this->changeState($ar->article_id, $userId);
                            /*
                                            Criar o historico do saldo em carteira
                                            *** verifcar ssaldo disponivel.
                                            *** verificar quanto foi retirado no saldo em carteira.
                                        */

                            $transaction->article_request()->attach($ar->id, ['value' => $articleRequestData['paid']]);
                           

                            if ($articleRequestData['cretid_saldo'] == false) {
                                $qtd_saldoPagamento = $user->credit_balance - $articleRequests['user'];
                                $historic_user_balance = DB::table('historic_user_balance')->insert([
                                    'id_user' => $userId,
                                    'id_transaction' => $transaction->id,
                                    'valor_credit' => $qtd_saldoPagamento,
                                    'data_from' => null,
                                ]);

                            }

                            if ($articleRequestData['applyTax'] && $articleRequestData['state'] != 'total') {
                                // create debit with article base value
                                $taxTransaction = Transaction::create([
                                    'type' => 'debit',
                                    'value' => $ar->extra_fees_value,
                                    'notes' => 'Débito do valor de taxa aplicado.',
                                    'data_from' => 'Multa',
                                ]);

                                $taxTransaction->article_request()->attach($ar->id, ['value' => $ar->extra_fees_value]);
                            }
                        }

                        $articleAnulacao = Article::where([
                            'id' => $ar->article_id,
                            'id_code_dev' => CodevEnum::ANULACAO_MATRICULA,
                        ])->first();

                        if (isset($articleAnulacao->anoLectivo) && $articleRequestData['state']) {
                            $this->anulateUtil->anulatePropinaArticleRequest($articleAnulacao->anoLectivo, $consultaArt->user_id);
                        }
                    }

                    $receipt = null;
                    // $banco=null;
                    $transaction_value = null;
                    
                    if ($transaction->type === 'payment') {
                        $fulfilledAt = Carbon::parse($request->get('transaction_fulfilled_at'))->startOfDay();

                        //IMPORTANTE: tratar depois com estrutura de repeticao esse codigo.
                        //codigo: executa 5 vezes o registo pagos em parecela (se nao for null)

                        if ($request->get('transaction_bank') == null) {
                            $banco = 16;
                            $transaction_value = 0;
                        } else {
                            $banco = $request->get('transaction_bank');
                            $transaction_value = (float) $request->get('transaction_value');
                        }
                          
                        TransactionInfo::create([
                            'transaction_id' => $transaction->id,
                            'bank_id' => $banco,
                            'fulfilled_at' => $fulfilledAt,
                            'reference' => $request->get('transaction_reference'),
                            'value' => $transaction_value,
                        ]);

                        if ($request->get('transaction_value_1') != null) {
                            TransactionInfo::create([
                                'transaction_id' => $transaction->id,
                                'bank_id' => $request->get('bank_1'),
                                'fulfilled_at' => Carbon::parse($request->get('transaction_fulfilled_at_1'))->startOfDay(),
                                'reference' => $request->get('reference_1'),
                                'value' => (float) $request->get('transaction_value_1'),
                            ]);
                        }
                        if ($request->get('transaction_value_2') != null) {
                            TransactionInfo::create([
                                'transaction_id' => $transaction->id,
                                'bank_id' => $request->get('bank_2'),
                                'fulfilled_at' => Carbon::parse($request->get('transaction_fulfilled_at_2'))->startOfDay(),
                                'reference' => $request->get('reference_2'),
                                'value' => (float) $request->get('transaction_value_2'),
                            ]);
                        }

                        if ($request->get('transaction_value_3') != null) {
                            TransactionInfo::create([
                                'transaction_id' => $transaction->id,
                                'bank_id' => $request->get('bank_3'),
                                'fulfilled_at' => Carbon::parse($request->get('transaction_fulfilled_at_3'))->startOfDay(),
                                'reference' => $request->get('reference_3'),
                                'value' => (float) $request->get('transaction_value_3'),
                            ]);
                        }

                        if ($request->get('transaction_value_4')) {
                            TransactionInfo::create([
                                'transaction_id' => $transaction->id,
                                'bank_id' => $request->get('bank_4'),
                                'fulfilled_at' => Carbon::parse($request->get('transaction_fulfilled_at_4'))->startOfDay(),
                                'reference' => $request->get('reference_4'),
                                'value' => (float) $request->get('transaction_value_4'),
                            ]);
                        }

                        $receipt = $this->reserveReceiptCode($transaction->id);
                    } elseif ($transaction->type === 'adjust') {
                        $transaction_value = (float) $request->get('transaction_value');
                        $fulfilledAt = Carbon::parse($request->get('transaction_fulfilled_at'))->startOfDay();
                        $banco = $request->get('entidade_ajuste');
                        TransactionInfo::create([
                            'transaction_id' => $transaction->id,
                            'bank_id' => $banco,
                            'fulfilled_at' => $fulfilledAt,
                            'reference' => $request->get('transaction_reference'),
                            'value' => $transaction_value,
                        ]);

                        $receipt = $this->reserveReceiptCode($transaction->id);
                    }

                    $getfinalisMatricula = DB::table('matriculation_finalist')
                        ->where('matriculation_finalist.user_id', $userId)
                        ->where('matriculation_finalist.year_lectivo', $request->selectAnoLetivo)
                        ->whereNull('matriculation_finalist.deleted_at')
                        ->whereNull('matriculation_finalist.deleted_by')
                        ->get();

                    $matricula_finalista = $getfinalisMatricula->isEmpty() ? false : true;

                    if ($areAllowed->isEmpty()) {
                        if ($receipt) {
                            //if(auth()->user()->id == 5) dd($articleRequests);
                            $receiptGenerated = $this->generateReceipt($transaction->id, $receipt, $articleRequests['user'], $cancelarMulta, $disciplines, $getRegraImplementEmolu, $getRegraImplementada, $cretidoUser, $matricula_finalista, $articleResume);

                            // return  $receiptGenerated;
                            $caminhRecibo = $receiptGenerated[1];

                            // $receiptGenerated;
                            if (!$receiptGenerated) {
                                throw new Exception('Receipt could not be generated.');
                            }
                        }
                    } else {
                        if ($receipt) {
                            $receiptGenerated = $this->generateReceiptForBolseiros($transaction->id, $receipt, $cancelarMulta, $disciplines);

                            if (!$receiptGenerated) {
                                throw new Exception('Receipt could not be generated.');
                            }
                        }
                    }

                    $saldoAtual = (float) $user->credit_balance;

                    if ($saldoAtual < $articleRequests['user']) {
                        DB::table('historic_user_credit_balance')->insert([
                            'user_id' => $userId,
                            'id_transaction' => $transaction->id,
                            'valor' => (float) $articleRequests['user'],
                            'created_at' => Carbon::Now(),
                        ]);
                    }

                    $user->credit_balance = (float) $articleRequests['user'];
                    $user->save();

                    DB::commit();

                    $year = Carbon::now()->year;
                    // Configurações das Notificações.
                    $body = '<p>Caro(a) estudante, ' . $user->name . ',  foi emitido o recibo nº <b>' . $year . '-' . $receipt->code . '</b> por favor verifique o anexo.</p>';
                    $icon = 'fas fa-receipt';
                    $subjet = '[Tesouraria]-Pagamento';
                    $destinetion[] = $user->id;
                    $file = $caminhRecibo;
                    notification($icon, $subjet, $body, $destinetion, $file, null);

                    // return $receiptGenerated;

                    // Success message
                    Toastr::success(__('Payments::requests.update_success_message'), __('toastr.success'));
                    return redirect()->route('requests.index');
                }
                return redirect()
                    ->back()
                    ->withErrors(['Nenhum Emolumento / Propina selecionado.'])
                    ->withInput();
            } else {
                Toastr::error(__('Caro tesoureiro, <b>' . $tesoreiro . '</b>.<br><br>Houve uma sobreposição momentânea na submissão de dados.<br><br>Por favor repita a operação.'), __(''));
                return redirect()->route('requests.index');
            }
        } catch (Exception | Throwable $e) {
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    private function reserveReceiptCode($transactionId)
    {
        $nextCode = '000001';
        $nome_path = null;
        $codigoRecibo = null;
        $ano = null;
        $latestReceipt = TransactionReceipt::latest()->first();
        if ($latestReceipt && Carbon::parse($latestReceipt->created_at)->year === Carbon::now()->year) {
            $nextCode = str_pad((int) $latestReceipt->code + 1, 6, '0', STR_PAD_LEFT);
            $ano = substr(Carbon::now()->year, -2);
            $codigoRecibo = '/storage/receipts/recibo-' . $ano . '-' . $nextCode . '.pdf';
        }
        $query = DB::table('transaction_receipts as recibo')
            ->where('recibo.path', $codigoRecibo)
            // ->latest()
            ->get();

        if (!$query->isEmpty()) {
            $nextCode = str_pad((int) $nextCode + 1, 6, '0', STR_PAD_LEFT);
            $receipt = new TransactionReceipt();
            $receipt->transaction_id = $transactionId;
            $receipt->code = $nextCode;
            $receipt->created_at = Carbon::now();
            $receipt->save();

            return $receipt;
            // return $nextCode."-1";
        } else {
            // // // create receipt
            $receipt = new TransactionReceipt();
            $receipt->transaction_id = $transactionId;
            $receipt->code = $nextCode;
            $receipt->created_at = Carbon::now();
            $receipt->save();

            return $receipt;

            // return $nextCode."-2";
        }
    }
    
    private function AnoArticle($article_id) {
        return DB::table('articles')
            ->where('id', $article_id)
            ->value('anoLectivo'); // devolve diretamente o valor
    }


    private function generateReceipt($transactionId, $receipt, $credit_balance, $cancelarMulta, $disciplines, $getRegraImplementEmolu, $getRegraImplementada, $cretidoUser, $matricula_finalista, $articleResume, $user = null){
        try {
            $transaction = Transaction::where('id', $transactionId)
                ->with([
                    'article_request' => function ($q) {
                        $q->with([
                            'user' => function ($q) {
                                $q->with([
                                    'courses' => function ($q) {
                                        $q->with('currentTranslation');
                                    },
                                    'classes' => function ($q) {
                                        $q->with([
                                            'room' => function ($q) {
                                                $q->with('currentTranslation');
                                            },
                                        ]);
                                    },
                                    'parameters' => function ($q) {
                                        $q->where('code', 'n_mecanografico');
                                    },
                                    'user_parameters' => function ($q) {
                                        $q->where('parameters_id', 1);
                                    },
                                    'matriculation' => function ($q) {
                                        $q->with([
                                            'classes' => function ($q) {
                                                $q->with([
                                                    'room' => function ($q) {
                                                        $q->with('currentTranslation');
                                                    },
                                                ]);
                                            },
                                        ]);
                                    },
                                ]);
                            },
                            'article',
                        ]);
                    },
                    'transaction_info' => function ($q) {
                        $q->with(['bank']);
                    },
                    'createdBy',
                ])
                ->firstOrFail();

            $transactionInfo = TransactionInfo::whereTransactionId($transactionId)
                ->with(['bank'])
                ->get();
            $institution = Institution::latest()->first();

            if (count($getRegraImplementada) > 0) {
                // return $getRegraImplementada;
                foreach ($transaction->article_request as $value) {
                    foreach ($getRegraImplementada as $key => $item) {
                        if ($value->discipline_id == '' && $value->year != null && $value->month == $item->mes) {
                            // $value->balance=-$item->valor+0;
                            $value->base_value = $item->valor;
                        }
                    }
                }
            }

            if (count($getRegraImplementEmolu) > 0 && count($getRegraImplementada) < 1) {
                // return $getRegraImplementEmolu;
                foreach ($transaction->article_request as $value) {
                    foreach ($getRegraImplementEmolu as $chave => $element) {
                        if ($value->discipline_id == '' && $value->year != null && $value->month == $element->mes) {
                            // $value->balance=-$element->valor+0;
                            $value->base_value = $element->valor;
                        }
                    }
                }
            }



            $data = [
                'transaction' => $transaction,
                'articleResume' => $articleResume,
                'receipt' => $receipt,
                'transactionInfo' => $transactionInfo,
                'cancelarMulta' => $cancelarMulta,
                'disciplines' => $disciplines,
                'institution' => $institution,
                'userValor_credit_balance' => $credit_balance,
                'cretidoUser' => $cretidoUser,
                'matricula_finalista' => $matricula_finalista,
            ];
            
            // Footer
            $footer_html = view()
                ->make('Payments::transactions.partials.pdf_footer', ['user' => $transaction->createdBy, 'institution' => $institution])
                ->render();

            $fileName = 'recibo-' . Carbon::now()->format('y') . '-' . $receipt->code . '.pdf';
            $pathTemp = storage_path('app/public/receipts-temp/' . $fileName);
            // Se já existir, elimina
            if (file_exists($pathTemp)) {
                unlink($pathTemp);
            }

            // Guardar o PDF
            $pdf = SnappyPdf::loadView('Payments::transactions.pdf_recibo', $data)
                ->setOption('margin-top', '2mm')
                ->setOption('margin-left', '2mm')
                ->setOption('margin-bottom', '35mm')
                ->setOption('margin-right', '2mm')
                ->setOption('header-html', '<header></header>')
                ->setOption('footer-html', $footer_html)
                ->setPaper('a5')
                ->save(storage_path('app/public/receipts-temp/' . $fileName));

            $merger = PDFMerger::init();
            // $merger = new PDFMerger();
            // $merger = new \LynX39\LaraPdfMerger\PdfManage;

            $file_path = storage_path('app/public/receipts-temp/' . $fileName);
            // return $file_path;

            $merger->addPDF($file_path, 'all');
            $merger->addPDF($file_path, 'all');
            $merger->merge();
            // return $merger;

            Storage::delete('receipts-temp/' . $fileName);

            $merger->save(storage_path('app/public/receipts/' . $fileName), 'file');
            $receipt->path = '/storage/receipts/' . $fileName;
            $receipt->save();

            $vetor = [($recibo = true), ($caminhoRecibo = $receipt->path), ($data = $data)];

            return $vetor;
        } catch (Exception $e) {
            // metodo merger em que estou a trabalhar
            dd($e);
            //logError($e);
            Log::error($e);
            //return response()->json($e);
        }

        return false;
    }

    public function generateReceiptForBolseiros($transactionId, $receipt, $cancelarMulta, $disciplines)
    {
        try {
            $transaction = Transaction::where('id', $transactionId)
                ->with([
                    'article_request' => function ($q) {
                        $q->with([
                            'user' => function ($q) {
                                $q->with([
                                    'courses' => function ($q) {
                                        $q->with('currentTranslation');
                                    },
                                    'classes' => function ($q) {
                                        $q->with([
                                            'room' => function ($q) {
                                                $q->with('currentTranslation');
                                            },
                                        ]);
                                    },
                                    'parameters' => function ($q) {
                                        $q->where('code', 'n_mecanografico');
                                    },
                                    'user_parameters' => function ($q) {
                                        $q->where('parameters_id', 1);
                                    },
                                    'matriculation' => function ($q) {
                                        $q->with([
                                            'classes' => function ($q) {
                                                $q->with([
                                                    'room' => function ($q) {
                                                        $q->with('currentTranslation');
                                                    },
                                                ]);
                                            },
                                        ]);
                                    },
                                ]);
                            },
                            'article',
                        ]);
                    },
                    'transaction_info' => function ($q) {
                        $q->with(['bank']);
                    },
                    'createdBy',
                ])
                ->firstOrFail();

            $transactionInfo = TransactionInfo::whereTransactionId($transactionId)
                ->with(['bank'])
                ->get();

            $institution = Institution::latest()->first();
            $entityInfo = DB::table('scholarship_holder')
                ->join('scholarship_entity', 'scholarship_entity.id', '=', 'scholarship_holder.scholarship_entity_id')
                ->where('scholarship_holder.user_id', $transaction->article_request->first()->user_id)
                ->first();

            $data = [
                'transaction' => $transaction,
                'receipt' => $receipt,
                'entityInfo' => $entityInfo,
                'transactionInfo' => $transactionInfo,
                'institution' => $institution,
                'cancelarMulta' => $cancelarMulta,
                'disciplines' => $disciplines,
            ];

            // return view('GA::scholarship-holder.reports.receipt')->with($data);

            // return view('Payments::transactions.pdf_recibo', $data);
            // Footer
            $footer_html = view()
                ->make('Payments::transactions.partials.pdf_footer', ['user' => $transaction->createdBy, 'institution' => $institution])
                ->render();

            $fileName = 'recibo-' . Carbon::now()->format('y') . '-' . $receipt->code . '.pdf';

            $pdf = SnappyPdf::loadView('GA::scholarship-holder.reports.receipt', $data)
                ->setOption('margin-top', '2mm')
                ->setOption('margin-left', '2mm')
                ->setOption('margin-bottom', '35mm')
                ->setOption('margin-right', '2mm')
                ->setOption('header-html', '<header></header>')
                ->setOption('footer-html', $footer_html)
                ->setPaper('a5')
                ->save(storage_path('app/public/receipts-temp/' . $fileName));

            $merger = PDFMerger::init();

            $merger->addPDF(storage_path('app/public/receipts-temp/' . $fileName));
            $merger->addPDF(storage_path('app/public/receipts-temp/' . $fileName));
            $merger->merge();

            Storage::delete('receipts-temp/' . $fileName);

            $merger->save(storage_path('app/public/receipts/' . $fileName), 'file');

            $receipt->path = '/storage/receipts/' . $fileName;
            $receipt->save();
            return $arrya = [true, $data];
        } catch (Exception $e) {
            //logError($e);
            Log::error($e);
            dd($e);
            //return response()->json($e);
        }

        return false;
    }

    private function calculateCheckedArticleRequestsTaxAndValues($user, $request, $getRegraImplementEmolu, $getRegraImplementada, $cretidoUser)
    {
        $modifiedData = [
            'requests' => [],
            'user' => 0,
            'qtDias' => 0,
        ];
        $dataArray = [$request->transaction_fulfilled_at, $request->transaction_fulfilled_at_1, $request->transaction_fulfilled_at_2, $request->transaction_fulfilled_at_3, $request->transaction_fulfilled_at_4];
        rsort($dataArray);
        $saldoCarteira = $cretidoUser == false ? $user->credit_balance : 0;

        $transactionValue = (float) $request->get('totalValue') + (float) $saldoCarteira;
        $checkedArticleRequests = $request->get('article_request_selected');

        if (is_array($checkedArticleRequests)) {
            $articleRequestsSelected = $this->getUnpaidArticleRequests($user->id, $request->article_request_selected)->whereIn('id', $checkedArticleRequests);
            
            $rRegraValue = $this->articlesUtil->getRegraValue($articleRequestsSelected, $getRegraImplementada, $getRegraImplementEmolu);

            if (count($getRegraImplementada) > 0) {
                
                foreach ($articleRequestsSelected as $value) {
                    foreach ($getRegraImplementada as $key => $item) {
                        if ($value->discipline_id == '' && $value->year != null && $value->month == $item->mes) {
                            if(!$rRegraValue->status) $value->balance = -$item->valor + 0;
                            $value->base_value = $item->valor;
                            $ativaRules = $this->getAtivaRules($item->id_ruleArtc);
                        }
                    }
                }
            }
            if (count($getRegraImplementEmolu) > 0 && count($getRegraImplementada) < 1) {
                foreach ($articleRequestsSelected as $value) {
                    foreach ($getRegraImplementEmolu as $chave => $element) {
                        if ($value->discipline_id == '' && $value->year != null && $value->month == $element->mes) {
                            if(!$rRegraValue->status) $value->balance = -$element->valor + 0;
                            $value->base_value = $element->valor;
                            $ativaRules = $this->getAtivaRules($element->id_ruleArtc);
                        }
                    }
                }
            }
            $articleRequestsSelected->each(function ($item, $key) use ($request, &$modifiedData, &$transactionValue, $user, $dataArray, &$getRegraImplementada, &$getRegraImplementEmolu, &$cretidoUser) {
                // TAX

                $taxValue = 0;
                //    return $key;
                $modifiedData['requests'][$item->id]['applyTax'] = false;
                // $canPayBaseValue = $transactionValue >= $item->base_value;

                // return "123";
                $fees = $item->article->extra_fees;
                // (int)$item->extra_fees_value === 0 && // forma de verificar se pode conbrar as multa.

                if ($fees->count()) {
                    // $transactionDay = $this->getArticleLatestTransactionDate($item, $request->get('transaction_fulfilled_at'))
                    $anoEmolu = $item->year;
                    $mesEmolu = $item->month;
                    $diaEmolu = 0;

                    /*
                            **
                            Saber quanto tempo velou para pagar está propina.
                        */
                    $data_inicial = $anoEmolu . '-' . $mesEmolu . '-' . $diaEmolu;
                    $data_final = $dataArray[0];
                    $diferenca = strtotime($data_final) - strtotime($data_inicial);
                    $dias = floor($diferenca / (60 * 60 * 24));

                    $fees = $fees->sortBy('fee_percent')->values();
                    // $zeroTax = $fees->firstWhere('fee_percent', 0);
                    // $noTaxDays = $zeroTax ? $zeroTax->max_delay_days : 0;

                    // $articleRequestCreateDay = $item->created_at->startOfDay();

                    $max_days = null;
                    $fee_percent = null;
                    $chave = null;
                    $index = null;
                    $limiteDias = null;

                    // verificar se o intervalo da data, pertence a que taxa
                    // basta pegar o dia e compara com os maximo dia de intermavo
                    $qtd_tax = count($fees) - 1;
                    if (isset($request->checados)) {
                        if (in_array($item->id, $request->checados)) {
                            $modifiedData['requests'][$item->id]['applyTax'] = false;
                            $modifiedData['requests'][$item->id]['tax'] = 0;
                        } else {
                            if ($dias > 0) {
                                foreach ($fees as $key => $value) {
                                    if ($dias > $value->max_delay_days) {
                                        $fee_percent = $value->fee_percent;
                                        $max_days = $value->max_delay_days;
                                        $chave = $key == $qtd_tax ? $fee_percent : $fees[$key + 1]->fee_percent;
                                    } else {
                                        $index = $value->max_delay_days;
                                    }
                                }
                                if ($fee_percent > 0) {
                                   
                                    if ($index > $max_days && $dias > $max_days && $max_days != null) {
                                        $limiteDias = $max_days;
                                        if ($limiteDias > $dias) {
                                            $taxValue = ($item->base_value * 15) / 100;
                                        } else {
                                            $taxValue = $dias == 45 ? ($item->base_value * 15) / 100 : ($item->base_value * $chave) / 100;
                                            // $taxValue =  ($item->base_value * $chave) / 100;
                                        }
                                    } else {
                                        $taxValue = ($item->base_value * $fee_percent) / 100;
                                    }
                                } else {
                                    if ($fee_percent == 0 && $dias > $max_days && $max_days != null) {
                                        $taxValue = ($item->base_value * $chave) / 100;
                                    }
                                }
                                if ($taxValue !== 0) {
                                    $modifiedData['requests'][$item->id]['applyTax'] = true;
                                    $modifiedData['requests'][$item->id]['tax'] = $taxValue;
                                    // $modifiedData['requests']['qtDias'] =$dias;
                                } else {
                                    $modifiedData['requests'][$item->id]['applyTax'] = false;
                                    $modifiedData['requests'][$item->id]['tax'] = 0;
                                }
                                $modifiedData['requests'][$item->id]['qtDias'] = $dias;
                            } else {
                                $modifiedData['requests'][$item->id]['applyTax'] = false;
                                $modifiedData['requests'][$item->id]['tax'] = 0;
                            }
                        }
                    } else {
                        if ($dias > 0) {
                            foreach ($fees as $key => $value) {
                                if ($dias > $value->max_delay_days) {
                                    $fee_percent = $value->fee_percent;
                                    $max_days = $value->max_delay_days;
                                    $chave = $key == $qtd_tax ? $fee_percent : $fees[$key + 1]->fee_percent;
                                } else {
                                    $index = $value->max_delay_days;
                                }
                            }
                            if ($fee_percent > 0) {
                               

                                if ($index > $max_days && $dias > $max_days && $max_days != null) {
                                    $limiteDias = $max_days;
                                   
                                    if ($limiteDias > $dias) {
                                        $taxValue = ($item->base_value * 15) / 100;
                                    } else {
                                        $taxValue = $dias == 45 ? ($item->base_value * 15) / 100 : ($item->base_value * $chave) / 100;
                                        // $taxValue =  ($item->base_value * $chave) / 100;
                                    }
                                    
                                } else {
                                    $taxValue = ($item->base_value * $fee_percent) / 100;
                                }
                            } else {
                                if ($fee_percent == 0 && $dias > $max_days && $max_days != null) {
                                    $taxValue = ($item->base_value * $chave) / 100;
                                }
                            }
                            if ($taxValue !== 0) {
                                $modifiedData['requests'][$item->id]['applyTax'] = true;
                                $modifiedData['requests'][$item->id]['tax'] = $taxValue;
                                // $modifiedData['requests']['qtDias'] =$dias;
                            } else {
                                $modifiedData['requests'][$item->id]['applyTax'] = false;
                                $modifiedData['requests'][$item->id]['tax'] = $taxValue;
                            }
                            $modifiedData['requests'][$item->id]['qtDias'] = $dias;
                        } else {
                            $modifiedData['requests'][$item->id]['applyTax'] = false;
                            $modifiedData['requests'][$item->id]['tax'] = 0;
                        }
                    }
                } else {
                    $modifiedData['requests'][$item->id]['tax'] = 0;
                }

                if ($taxValue <= $item->extra_fees_value && !isset($request->checados)) {
                    // $modifiedData['requests']['qtDias'] =0;
                    $modifiedData['requests'][$item->id]['applyTax'] = false;
                    $modifiedData['requests'][$item->id]['tax'] = 0;
                    $taxValue = 0;
                }

                // VALUE
                $articleRequestState = 'pending';
                // $articleRequestPaid = $transactionValue <= -($item->balance - ( $taxValue > $item->extra_fees_value ? $taxValue - $item->extra_fess_value:0)) ? $transactionValue : -($item->balance - ($taxValue > $item->extra_fees_value ? $taxValue - $item->extra_fess_value:0));
                // $articleRequestBalance = $item->balance - ($taxValue > $item->extra_fees_value ? $taxValue - $item->extra_fess_value:0) + $articleRequestPaid;
                $articleRequestPaid = $transactionValue <= -($item->balance - $taxValue) ? $transactionValue : -($item->balance - $taxValue);
                $articleRequestBalance = $item->balance - $taxValue + $articleRequestPaid;

                if ((int) $articleRequestBalance === 0) {
                    $articleRequestState = 'total';
                } elseif (-1 * ($item->base_value + ($taxValue > $item->extra_fees_value ? $taxValue - $item->extra_fess_value : 0)) < $articleRequestBalance && $articleRequestBalance < 0) {
                    $articleRequestState = 'partial';
                    $modifiedData['requests'][$item->id]['pending'] = $articleRequestBalance;
                } elseif (-1 * ($item->base_value + ($taxValue > $item->extra_fees_value ? $taxValue - $item->extra_fess_value : 0)) === $articleRequestBalance) {
                    $articleRequestState = 'pending';
                }

                $modifiedData['requests'][$item->id]['state'] = $articleRequestState;
                $modifiedData['requests'][$item->id]['paid'] = $articleRequestPaid;

                $transactionValue -= $articleRequestPaid;

                // if($modifiedData['requests'][$item->id]['state']=="total"){
                //     return 2;
                // }

                $saldoCarteira = $cretidoUser == false ? $transactionValue : $transactionValue + $user->credit_balance;
                if ($transactionValue >= $user->credit_balance) {
                    $modifiedData['requests'][$item->id]['cretid_saldo'] = true;
                } else {
                    $modifiedData['requests'][$item->id]['cretid_saldo'] = false;
                }

                # Código para recalcular as múltas

                // if(($modifiedData['requests'][$item->id]['state']=="partial") && ($modifiedData['requests'][$item->id]['applyTax']==true)){
                //     $modifiedData['requests'][$item->id]['tax'] = ($item->base_value+$modifiedData['requests'][$item->id]['tax'])-$modifiedData['requests'][$item->id]['paid'];
                // }
            });
        }
        // return $modifiedData['requests']['qtDias'];

        $saldoCarteira = $cretidoUser == false ? $transactionValue : $transactionValue + $user->credit_balance;
        $modifiedData['user'] = (float) $saldoCarteira;
        return $modifiedData;
    }

    private function getArticleLatestTransactionDate($articleRequest, $currentTransactionDate)
    {
        $paymentTransactions = $articleRequest->transactions->where('type', 'payment');
        $dates = $paymentTransactions->count() ? $paymentTransactions->pluck('transaction_info.fulfilled_at') : collect([]);
        $sortedDates = $dates->merge($currentTransactionDate)->sort(static function ($a, $b) {
            return strtotime($a) - strtotime($b);
        });

        return Carbon::parse($sortedDates->first());
    }

    private function getAtivaRules($id_rules)
    {
        $consulteAtive = DB::table('artcles_rules')->Where('artcles_rules.estado', '=', 1)->WhereNull('artcles_rules.deleted_by')->get();
        if (count($consulteAtive) > 0) {
        } else {
            $updatEstado = DB::table('artcles_rules')
                ->where('artcles_rules.id', $id_rules)
                ->update(['artcles_rules.estado' => 1]);
        }
    }

    private function organizacionEstado($user, $request)
    {
        // return $user;
        $getEmolument = DB::table('article_requests as article_request')
            ->join('articles as article', 'article.id', '=', 'article_request.article_id')
            ->leftJoin('article_translations as article_translation', function ($q) {
                $q->on('article_translation.article_id', '=', 'article.id')->where('article_translation.language_id', 1)->where('article_translation.active', 1);
            })
            ->join('code_developer as code_develope', function ($q) {
                $q->on('code_develope.id', '=', 'article.id_code_dev')->whereIn('code_develope.code', ['anul_matric', 'confirm', 'mudanca_curso', 'trabalho_fim_curso', 'exame', 'pedido_t_entrada', 'pedido_t_saida']);
            })
            ->select(['article_translation.display_name as display_name', 'article.code as code_article', 'code_develope.code as code_dev', 'code_develope.id as id_code_dev', 'article.id as id_article'])
            ->where('article.deleted_at')
            ->where('article.deleted_at')
            ->whereIn('article_request.id', $request->article_request_selected)
            ->get();

        if (!$getEmolument->isEmpty()) {
            Anular_matricula($getEmolument, $user->id);
            validar_mudanca_curso($getEmolument, $user->id);
            getstatesPayment($getEmolument, $user);
        }
    }

    public function fetch($id, $action)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    public function changeState($articleId, $userId)
    {
        if ($articleId == 19) {
            //caso o artigo for anulacao de matricula
            UserState::updateOrCreate(
                ['user_id' => $userId],
                ['state_id' => 16, 'courses_id' => null], //16 => anulacao da matricula
            );
            UserStateHistoric::create([
                'user_id' => $userId,
                'state_id' => 16,
            ]);
        }
        //TODO: QUANDO ELE PAGAR ESSES EMOLUMENTOS DENTRO DO ARRAY, PASSAR PARA A FREQUENTAR (AVALIAR SE NAO DEVE OUTRO EMOLUMENTO)
        elseif (in_array($articleId, [23, 24, 27, 21, 22, 25, 26, 6, 41, 42, 36, 8])) {
            //caso o artigo for aguardar pagamento por - declaracao, inscricao e confirmacao de matricula
        }
        /*//localmente colocar 65
        if ($articleId == 64) {
            //Pedido de transferencia (entrar)
            UserState::updateOrCreate(
                ['user_id' => $userId],
                ['state_id' => 14, 'courses_id' => null] //14 => Pedido de transferencia entrar
            );
            UserStateHistoric::create([
                'user_id' => $userId,
                'state_id' => 14
            ]);
        } else {
            $user = User::findOrFail($userId);
            $course_id = $user->courses()->first()->id;

            //Avaliar sempre o estado anterior
            //$state = UserState::whereUserId($userId)->firstOrFail();

            if ($articleId == 19) {
                UserState::updateOrCreate(
                    ['user_id' => $userId],
                    ['state_id' => 10, 'courses_id' => $course_id] // 10 => Suspensão da matrícula
                );
                UserStateHistoric::create([
                'user_id' => $userId,
                'state_id' => 10
            ]);
            } elseif ($articleId == 8) {
                //avaliar se o ultimo estado é finalista, caso for não alterar
                $actualState = UserState::whereUserId($userId)->first();
                //!$actualState == null &&

                if (!$actualState == null &&  $actualState->state_id == 15) {

                }else{
                    UserState::updateOrCreate(
                            ['user_id' => $userId],
                            ['state_id' => 7, 'courses_id' => $course_id] //7 => Frequentar
                        );
                        UserStateHistoric::create([
                        'user_id' => $userId,
                        'state_id' => 7
                    ]);
                }
            } elseif ($articleId == 38) {
                UserState::updateOrCreate(
                    ['user_id' => $userId],
                    ['state_id' => 9, 'courses_id' => $course_id] //9 => Mudanca de Curso,
                );
                UserStateHistoric::create([
                'user_id' => $userId,
                'state_id' => 9
            ]);
            } elseif ($articleId == 40) {
                //Pedido de transferencia (sair)
                UserState::updateOrCreate(
                    ['user_id' => $userId],
                    ['state_id' => 12, 'courses_id' => $course_id] //12 => Pedido de transferencia sair
                );
                UserStateHistoric::create([
                'user_id' => $userId,
                'state_id' => 12
            ]);
            }

             //TODO: avaliar se existe emolumento de emolumento de equivalencia por disciplina
            $pagos = ArticleRequest::whereUserId($userId)
                             ->where('status', 'total')
                             ->count();

            $porPagar = ArticleRequest::whereUserId($userId)
                     ->where('status', 'pending')
                     ->count();
            $actualState = UserState::whereUserId($userId)->first();
            if ($pagos == $porPagar) {
                //mesmo que pagar tudo antes de pagar pelas equivalencias nao alterar o estado
                //so alterar o estado se for diferente de "em transferencia"
                //se o estado dele ja for concluido ou finalista, nao alterar nada o estado.
                if(!$actualState == null){
                    if (!$actualState->state_id == 14 || !$actualState->state_id == 6 || !$actualState->state_id == 15) {
                        UserState::updateOrCreate(
                            ['user_id' => $userId],
                            ['state_id' => 7, 'courses_id' => $course_id] //7 => Frequentar
                        );
                        UserStateHistoric::create([
                            'user_id' => $userId,
                            'state_id' => 7
                        ]);
                    }
                }
            }

            //fim todo
        }*/
    }
}
