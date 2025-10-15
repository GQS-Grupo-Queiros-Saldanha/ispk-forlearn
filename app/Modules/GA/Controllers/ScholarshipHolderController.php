<?php

namespace App\Modules\GA\Controllers;

use App\Helpers\LanguageHelper;
use App\Http\Controllers\Controller;
use App\Modules\Payments\Models\Transaction;
use App\Modules\Payments\Models\TransactionInfo;
use App\Modules\Users\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;

use LynX39\LaraPdfMerger\Facades\PdfMerger;
use App\Modules\Payments\Util\ArticlesUtil;
use App\Modules\GA\Models\LectiveYear;
use App\Model\Institution;
use Error;
use PDF;

class ScholarshipHolderController extends Controller
{
    private $articlesUtil;

    function __construct()
    {
        $this->articlesUtil = new ArticlesUtil();
    }

    public function index()
    {
        return view('GA::scholarship-holder.scholarship-holder');
    }

    public function createScholarship()
    {
        return view('GA::scholarship-holder.form', [
            'action' => 'create',
        ]);
    }

    public function ajax()
    {
        try {
            $bolseiros = User::join('scholarship_holder', 'scholarship_holder.user_id', '=', 'users.id')
                ->leftJoin('scholarship_entity', 'scholarship_entity.id', '=', 'scholarship_holder.scholarship_entity_id')
                ->leftJoin('user_parameters as u_p', function ($join) {
                    $join->on('users.id', '=', 'u_p.users_id')->where('u_p.parameters_id', 19);
                })
                ->leftJoin('user_courses', 'user_courses.users_id', '=', 'users.id')
                ->leftJoin('courses as crs', 'crs.id', '=', 'user_courses.courses_id')
                ->leftJoin('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'crs.id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })
                ->where('scholarship_holder.are_scholarship_holder', 1);

            return DataTables::eloquent($bolseiros)->addIndexColumn()->toJson();
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function ajax_entity()
    {
        try {
            $entidades = DB::table('scholarship_entity');

            return DataTables::queryBuilder($entidades)

                ->addColumn('actions', function ($item) {
                    return view('GA::scholarship-holder.datatables.actions')->with('item', $item);
                })
                ->rawColumns(['actions'])
                ->addIndexColumn()
                ->toJson();
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    /*public function ajaxUsers()
    {
        try {
            $model = User::query()
             ->with(['roles' => function ($q) {
                 $q->with([
                    'currentTranslation'
                ]);
             }])
                ->join('users as u1', 'u1.id', '=', 'users.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'users.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'users.deleted_by')
                ->leftJoin('users_states as us', 'users.id', '=', 'us.user_id')
                ->leftJoin('states', 'us.state_id', '=', 'states.id')

                ->select([
                    'users.*',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    'states.name as state_name',
                    //'roles.name as roles'
                ]);

            return Datatables::eloquent($model)
                ->toJson();
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }

    }*/
    public function store(Request $request)
    {
        DB::table('scholarship_entity')->insert([
            'company' => $request->get('company'),
            'registered_office' => $request->get('registered_office'),
            'offices' => $request->get('office'),
            'NIF' => $request->get('nif'),
            'telf' => $request->get('tel'),
            'type' => $request->get('type'),
            'code' => $request->get('code'),
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ]);

        Toastr::success('Entidade registada com sucesso', __('toastr.success'));
        return redirect()->route('list.scholarship');
    }

    public function associateStudentEntity($id)
    {
        return view('GA::scholarship-holder.student-entity', ['user' => User::find($id), 'entitys' => DB::table('scholarship_entity')->get()]);
    }
    public function storeAssociateStudent(Request $request)
    {
        DB::table('scholarship_holder')
            ->where('user_id', $request->get('user_id'))
            ->update(['scholarship_entity_id' => $request->get('entity')]);

        Toastr::success('Entidade associada com sucesso', __('toastr.success'));
        return redirect()->route('scholarship.index');
    }

    public function removeAssociateStudent($id)
    {
        DB::table('scholarship_holder')
            ->where('user_id', $id)
            ->update(['scholarship_entity_id' => 0]);

        Toastr::success('Entidade removida com sucesso', __('toastr.success'));
        return redirect()->route('scholarship.index');
    }

    public function generateInvoice()
    {
        $id = 499;
        $user = User::whereId($id)
            ->with([
                'courses' => function ($q) {
                    $q->with(['currentTranslation']);
                },
            ])
            ->first();
        $entityInfo = DB::table('scholarship_holder')->join('scholarship_entity', 'scholarship_entity.id', '=', 'scholarship_holder.scholarship_entity_id')->where('scholarship_holder.user_id', $id)->first();

        $data = [
            'entityInfo' => $entityInfo,
            'user' => $user,
        ];

        return view('GA::scholarship-holder.reports.invoice')->with($data);
    }

    public function listScholarship()
    {
        return view('GA::scholarship-holder.index');
    }

    public function createRegraScholarshipNew(Request $request)
    {   
        $request->validate(['lective' => 'required', 'emolument' => 'required', 'scholarship' => 'required']);
        $id_anolectivo = $request->lective;
        
        foreach ($request->emolument as $emolument) {
            foreach ($request->scholarship as $scholarship) {
                $data = [
                    'scholarship_entity_id' => $scholarship,
                    'ano_lectivo' => $id_anolectivo,
                    'id_articles' => $emolument,
                    'deleted_by' => null,
                    'deleted_at' => null,
                ];

                $ruleArticle = DB::table('artcles_rules')->where($data)->first();
                $data['valor'] = $request->valorPercentual;

                $data['updated_by'] = auth()->user()->id;
                $data['updated_at'] = now();

                if (!isset($ruleArticle->id)) {
                    $data['created_by'] = auth()->user()->id;
                    $data['created_at'] = now();
                    DB::table('artcles_rules')->insert($data);
                } else {
                    DB::table('artcles_rules')
                        ->whereId($ruleArticle->id)
                        ->update($data);
                }
            }
        }
        Toastr::success(__('Regras foram implementadas com successo'), __('toastr.success'));
        return back();
    }

    public function getImplemtRulesAjax($id_anolectivo)
    {
        $model = $this->articlesUtil->getArticleRules($id_anolectivo, true);
        return Datatables::of($model)
            ->addColumn('actions', function ($item) {
                return view('Payments::articles.datatables.rules-article', compact('item'));
            })
            ->rawColumns(['actions'])
            ->addIndexColumn()
            ->toJson();
    }

    public function listRules()
    {
        $model = $this->articlesUtil->getArticleByLectiveYear();
        $currentLectiveYear = currentLectiveYear();
        $lectiveYears = LectiveYear::with(['currentTranslation'])
            ->whereNull(['deleted_at', 'deleted_by'])
            ->get();
        $scholarshipEntities = DB::table('scholarship_entity')
            ->whereNull(['deleted_at'])
            ->get();
        return view('GA::scholarship-holder.rulesArticle', compact('model', 'lectiveYears', 'currentLectiveYear', 'scholarshipEntities'));
    }

    public function showScholarship($id)
    {
        $action = 'show';
        $entity = DB::table('scholarship_entity')->where('id', $id)->first();
        return view('GA::scholarship-holder.form', compact('entity', 'action'));
    }
    public function editScholarship($id)
    {
        $action = 'edit';
        $entity = DB::table('scholarship_entity')->where('id', $id)->first();
        return view('GA::scholarship-holder.form', compact('entity', 'action'));
    }
    public function updateScholarship(Request $request, $id)
    {
        $entity = DB::table('scholarship_entity')
            ->where('id', $id)
            ->update([
                'company' => $request->get('company'),
                'registered_office' => $request->get('registered_office'),
                'offices' => $request->get('office'),
                'NIF' => $request->get('nif'),
                'telf' => $request->get('tel'),
                'type' => $request->get('type'),
                'code' => $request->get('code'),
            ]);

        Toastr::success('Entidade editado com sucesso', __('toastr.success'));
        return redirect()->route('list.scholarship');
    }
    public function deleteScholarship($id)
    {
        DB::table('scholarship_entity')->where('id', $id)->delete();
        Toastr::success('Entidade eliminada com sucesso', __('toastr.success'));
        return redirect()->route('list.scholarship');
    }

    public function generateReceipt()
    {
        /*$id = 1446;
        $user = User::whereId($id)->with([
            'courses' => function ($q) {
                $q->with(['currentTranslation']);
            }
            ])->first();


        $data = [
            'entityInfo' => $entityInfo,
            'user' => $user
        ];*/

        $transactionId = 27563;
        $receipt = 1;

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

            $entityInfo = DB::table('scholarship_holder')
                ->join('scholarship_entity', 'scholarship_entity.id', '=', 'scholarship_holder.scholarship_entity_id')
                ->where('scholarship_holder.user_id', $transaction->article_request->first()->user_id)
                ->first();

            $data = [
                'transaction' => $transaction,
                'receipt' => $receipt,
                'entityInfo' => $entityInfo,
                'transactionInfo' => $transactionInfo,
            ];

            return view('GA::scholarship-holder.reports.receipt')->with($data);

            // return view('Payments::transactions.pdf_recibo', $data);
            // Footer
            /*$footer_html = view()->make('Payments::transactions.partials.pdf_footer', ['user' => $transaction->createdBy])->render();

            $fileName = 'recibo-' . Carbon::now()->format('y') . '-' . $receipt->code . '.pdf';

            $pdf = SnappyPdf::loadView('Payments::transactions.pdf_recibo', $data)
                ->setOption('margin-top', '10')
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
            $receipt->save();*/

            return true;
        } catch (Exception $e) {
            //logError($e);
            Log::error($e);
            //return response()->json($e);
        }

        return false;

        return view('GA::scholarship-holder.reports.receipt')->with($data);
    }

    
    
        public function pdf_scholarship_holder()
    {
        try {
            // Recupera os dados de todos os bolsistas
            $scholarship_holders = User::join('scholarship_holder', 'scholarship_holder.user_id', '=', 'users.id')
                ->leftJoin('scholarship_entity', 'scholarship_entity.id', '=', 'scholarship_holder.scholarship_entity_id')
                ->leftJoin('user_parameters as u_p', function ($join) {
                    $join->on('users.id', '=', 'u_p.users_id')
                        ->where('u_p.parameters_id', 19);
                })
                ->leftJoin('user_parameters as full_name', function ($join) {
                    $join->on('users.id', '=', 'full_name.users_id')
                        ->where('full_name.parameters_id', 1);
                })
                ->leftJoin('user_courses', 'user_courses.users_id', '=', 'users.id')
                ->leftJoin('courses as crs', 'crs.id', '=', 'user_courses.courses_id')
                ->leftJoin('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'crs.id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })
                ->select([
                    'full_name.value as name',
                    'u_p.value as matricula',
                    'users.email as email',
                    'scholarship_entity.company as company',
                    'ct.display_name as display_name',
                    'scholarship_holder.desconto_scholarship_holder as desconto_scholarship_holder',
                ])
                ->where('scholarship_holder.are_scholarship_holder', 1)
                ->orderBy('name', 'asc')
                ->orderBy('scholarship_entity.company', 'asc')
                ->get();

            // Agrupar os bolsistas por empresa
            $groupedScholarshipHolders = $scholarship_holders->groupBy('company');

            // Obtendo a instituição
            $institution = Institution::latest()->first();

            // Gerar um nome de arquivo único
            $pdf_name = "Relatorio_Bolsista_" . date("Y_m_d") . ".pdf";
            $pdf_path = storage_path('pdfs/' . $pdf_name);

            // Verifica se o arquivo já existe e exclui
            if (file_exists($pdf_path)) {
                unlink($pdf_path);
            }

            // Gerar o PDF
            $pdf = PDF::loadView(
                'GA::scholarship-holder.reports.pdf_scholarship_holder',
                [
                    'groupedScholarshipHolders' => $groupedScholarshipHolders,
                    'institution' => $institution,
                    'titulo_documento' => "Relatório de Bolsista",
                    'documentoGerado_documento' => "Documento gerado em " . date("Y/m/d")
                ]
            );

            $pdf->setOption('margin-top', '1mm');
            $pdf->setOption('margin-left', '1mm');
            $pdf->setOption('margin-bottom', '4mm');
            $pdf->setOption('margin-right', '1mm');
            $pdf->setOption('enable-javascript', true);
            $pdf->setOption('debug-javascript', true);
            $pdf->setOption('javascript-delay', 1000);
            $pdf->setOption('enable-smart-shrinking', true);
            $pdf->setOption('no-stop-slow-scripts', true);
            $pdf->setPaper('a4', 'portrait');

            // Salvar o PDF em uma pasta no servidor
            $pdf->save($pdf_path);

            // Retornar uma mensagem de sucesso
            return $pdf->stream($pdf_name . '.pdf');

        } catch (Exception $e) {
            // Trate a exceção adequadamente
            Log::error($e->getMessage());
            return response()->json($e->getMessage(), 500);
        }
    }
    
    public function get_student($student)
    {
        $Bolseiro = DB::table('scholarship_holder')->where('user_id', $student)->where('are_scholarship_holder', 1)->first();

        if (isset($Bolseiro)) {
            if (isset($Bolseiro->desconto_scholarship_holder)) {
                return [1, $Bolseiro->desconto_scholarship_holder];
            } else {
                return [1, 0];
            }
        } else {
            return [0, ''];
        }
    }
    
        public function pdf_scholarship_entity()
    {
        try {

            // Recuperar a data atual
            $currentData = Carbon::now();

            // Obter o ano letivo atual
            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();

            // Consultar entidades bolseiras com informações adicionais
            $entidades = DB::table('scholarship_entity')
                ->leftJoin('users as u1', 'u1.id', '=', 'scholarship_entity.created_by')  // Left join para created_by
                ->leftJoin('users as u2', 'u2.id', '=', 'scholarship_entity.updated_by')  // Left join para updated_by
                ->leftJoin('users as u3', 'u3.id', '=', 'scholarship_entity.deleted_by')  // Left join para deleted_by
                ->select([
                    'scholarship_entity.*',
                    'u1.name as created_by',  // Alias para o nome do criador
                    'u2.name as updated_by',  // Alias para o nome do atualizador
                    'u3.name as deleted_by',
                    'scholarship_entity.code',
                    'scholarship_entity.company',
                    'scholarship_entity.company',
                    'scholarship_entity.registered_office',
                    'scholarship_entity.NIF',
                    'scholarship_entity.telf',
                    'scholarship_entity.phone_person'
                ])
                ->get();

            // Definir o título do documento
            $titulo_documento = "Entidades bolseiras / protocolo";

            $institution = Institution::latest()->first();

            // Obter os dados do ano letivo atual
            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->where('id', $lectiveYearSelected->id)
                ->select('*')
                ->get();

            // Carregar a view do PDF e passar as variáveis
            $pdf = PDF::loadView(
                'GA::scholarship-holder.pdf_scholarship_entity',  // Atualize o caminho da view para o correto
                compact('entidades', 'titulo_documento', 'institution', 'lectiveYears')
            );

            // Definir opções de margens e outras configurações do PDF
            $pdf->setOption('margin-top', '5mm');
            $pdf->setOption('margin-left', '5mm');
            $pdf->setOption('margin-bottom', '5mm');
            $pdf->setOption('margin-right', '5mm');
            $pdf->setOption('enable-javascript', true);
            $pdf->setOption('debug-javascript', true);
            $pdf->setOption('javascript-delay', 3000);
            $pdf->setOption('enable-smart-shrinking', true);
            $pdf->setOption('no-stop-slow-scripts', true);
            $pdf->setPaper('a4', 'portrait');

            // Definir o nome do arquivo PDF
            $pdf_name = "E_Bolseiras_Protocolo";

            // Retornar o PDF gerado como stream
            return $pdf->stream($pdf_name . '.pdf');
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
