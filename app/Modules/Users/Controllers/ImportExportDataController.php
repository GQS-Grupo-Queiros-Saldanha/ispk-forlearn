<?php

namespace App\Modules\Users\Controllers;

use App\Helpers\LanguageHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\Classes;
use App\Modules\GA\Models\Course;
use App\Modules\GA\Models\StudyPlan;
use App\Modules\Payments\Models\Article;
use App\Modules\Payments\Models\DisciplineArticle;
use App\Modules\Users\Models\Matriculation;
use App\Modules\Users\Models\UserCandidate;
use App\Modules\Users\Models\User;
use App\Modules\Users\Models\Role;
use App\Modules\Users\Models\UserState;
use App\Modules\Users\Models\UserStateHistoric;
use App\Modules\Users\Requests\MatriculationRequest;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Throwable;
use App\Modules\GA\Models\LectiveYear;
use DB;
use App\Model\Institution;
use App\Modules\Users\util\EnumVariable;
use App\Modules\Users\Enum\ParameterGroupEnum;
use App\Modules\Users\Enum\ParameterEnum;
use App\Modules\Users\Enum\RoleEnum;
use App\Modules\Users\util\UserUtil;
use Auth;

class ImportExportDataController extends Controller
{

    public function index(){
        try {
            //Pegar ano lectivo corrente.
            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();

            $currentData = Carbon::now();

            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();

            $data = [
                'action' => 'create',
                'languages' => Language::whereActive(true)->get(),
                'lectiveYears' => $lectiveYears,
                'lectiveYearSelected' => $lectiveYearSelected->id

            ];


            return view('Users::equivalence.index')->with($data);
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }




    public function transferenceRequest()
    {
        try {
            //Pegar ano lectivo corrente.
            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();

            $currentData = Carbon::now();

            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();

            $institution = Institution::latest()->first();
            $data = [
                'action' => 'create',
                'languages' => Language::whereActive(true)->get(),
                'lectiveYears' => $lectiveYears,
                'lectiveYearSelected' => $lectiveYearSelected->id,
                'institution' => $institution->nome
            ];


            return view('Users::equivalence.transferenceRequest')->with($data);
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }







    public function getStudentsWhereHasCourse($course_id, $type_transference)
    {
        $studentsQuery = User::query()
            ->whereHas('roles', function ($q) {
                $q->where('id', '=', 6);
            })
            ->leftJoin('users as u1', 'u1.id', '=', 'users.created_by')
            ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'users.id')
            ->join('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'uc.courses_id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })
            ->leftJoin('users as u2', 'u2.id', '=', 'users.updated_by')
            ->leftJoin('users as u3', 'u3.id', '=', 'users.deleted_by')
            ->leftJoin('user_parameters as full_name', function ($join) {
                $join->on('users.id', '=', 'full_name.users_id')
                    ->where('full_name.parameters_id', 1);
            })
            ->leftJoin('user_parameters as up_meca', function ($join) {
                $join->on('users.id', '=', 'up_meca.users_id')
                    ->where('up_meca.parameters_id', 19);
            })
            ->leftJoin('user_parameters as up_bi', function ($join) {
                $join->on('users.id', '=', 'up_bi.users_id')
                    ->where('up_bi.parameters_id', 14);
            })
            ->where('uc.courses_id', $course_id)
            ->select([
                'users.*',
                'full_name.value as nome_student',
                'up_meca.value as matricula',
                'u1.name as created_by',
                'u2.name as updated_by',
                'u3.name as deleted_by',
                'up_bi.value as n_bi',
                'ct.display_name as curso',
            ]);

        if ($type_transference == 1) {
            $studentsQuery->whereDoesntHave('matriculation');
        } else {
            $studentsQuery->whereHas('matriculation');
        }

        $students = $studentsQuery->get();

        return response()->json($students);
    }







    public function ListImportDataAjax()
    {


     

        $model = DB::table('Import_data_forlearn as importUSer')
            ->join('users as u0', 'u0.id', '=', 'importUSer.id_user')
            ->join('users as u1', 'u1.id', '=', 'importUSer.created_by')
            ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'u0.id')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'uc.courses_id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })

            ->leftJoin('user_parameters as u_p', function ($join) {
                $join->on('u0.id', '=', 'u_p.users_id')
                ->where('u_p.parameters_id', 1);
            })

            ->leftJoin('user_parameters as up_meca', function ($join) {
                $join->on('u0.id', '=', 'up_meca.users_id')
                ->where('up_meca.parameters_id',19 );
             })
            ->leftJoin('user_parameters as up_bi', function ($join) {
                $join->on('u0.id', '=', 'up_bi.users_id')
                ->where('up_bi.parameters_id',14 );
             })
             ->leftJoin('roles as r', function ($join) {
                $join->on('importUSer.role_user', '=', 'r.id')
                     ->whereNotNull('importUSer.role_user');
            })
            
            ->select([
                'importUSer.id as id_importacao',
                'importUSer.type_user as tipo_usuario',
                'importUSer.ano_curricular as curricular',
                'u0.id as id_usuario',
                'u_p.value as nome_usuario',
                'up_meca.value as matricula',
                'up_bi.value as bilhete',
                'u0.email as email',
                'importUSer.role_user as cargo',
                'ct.display_name as curso',
                'importUSer.codigo_old as matricula_antiga',
                'u1.name as criado_por',
                'importUSer.created_at as criado_a',
                'importUSer.update_at as actualizado_a',
                'r.name as nome_cargo' 
              
            ])
            ->whereNull('u0.deleted_by')
            ->distinct('u0.id');
            return Datatables::of($model)
                 ->addColumn('actions', function ($item) {
                
               return view('Users::Import-export-users.datatables.action')->with('item', $item);
            })
            // ->addColumn('in_out', function ($in_out) {
                //     return $in_out->type_transference == 1 ? "Entrada" : "Saída";
                // })
                ->addColumn('roles', function ($role) {
               
                 return view('Users::Import-export-users.datatables.role')->with('item', $role);
            })
            ->rawColumns(['actions', 'roles'])
            ->addIndexColumn()
            ->toJson();
    }








    public function edit($id)
    {

        try {
            //Pegar ano lectivo corrente.
            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();

            $consulta = DB::table('tb_transference_studant as transf')
                ->join('users as u0', 'u0.id', '=', 'transf.user_id')
                ->join('users as u1', 'u1.id', '=', 'transf.created_by')

                ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'u0.id')
                ->join('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'uc.courses_id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })
                ->leftJoin('user_parameters as u_p', function ($join) {
                    $join->on('u0.id', '=', 'u_p.users_id')
                        ->where('u_p.parameters_id', 1);
                })
                ->leftJoin('user_parameters as up_meca', function ($join) {
                    $join->on('u0.id', '=', 'up_meca.users_id')
                        ->where('up_meca.parameters_id', 19);
                })
                ->where('transf.id', $id)
                ->where('transf.type_transference', 1)

                ->select([
                    'transf.*',
                    'u0.id as id_usuario',
                    'u_p.value as student',
                    'u0.email as email',
                    'u1.name as criado_por',
                    'ct.display_name as course',
                    'uc.courses_id as course_id',
                ])


                ->groupBy('u_p.value')
                ->distinct('id')
                ->first();




            if (!$consulta) {
                Toastr::warning(__('A forLEARN não detectou um argumento invalido , por favor tente novamente'), __('toastr.warning'));
                return redirect()->back();
            }


            //Pegar equivalência já marcadas
            $Disci_Eq = DB::table('tb_equivalence_studant_discipline')
                ->where('id_transference_user', $consulta->id)->whereNull('deleted_by')->get();
            $currentData = Carbon::now();

            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();

            $data = [
                'action' => 'create',
                'languages' => Language::whereActive(true)->get(),
                'dados_geral' => $consulta,
                'dados_discipline' => $Disci_Eq,
                'lectiveYears' => $lectiveYears,
                'lectiveYearSelected' => $lectiveYearSelected->id

            ];


            return view('Users::equivalence.discipline_student_equivalence')->with($data);
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }





    public function anulate_equivalence(Request $request)
    {

        $currentData = Carbon::now();
        $consulta = DB::table('tb_transference_studant')
            ->where('id', $request->id_equivalencia)
            ->whereNull('deleted_by')
            ->whereNull('deleted_at')
            ->get();


        if (!$consulta->isEmpty()) {

            //reaproveitar para criar emolumento
            // $r4 =createAutomaticArticleRequestExame($id_user, $emolumento_equivalence->id_emolumento, null, null,$item);

            DB::table('tb_transference_studant')
                ->where('id', $request->id_equivalencia)
                ->update(['deleted_by' => Auth::user()->id, 'deleted_at' => $currentData]);

            Toastr::warning(__('A anulação do Pedido de transferência por equivalência foi efectuada com sucesso!.'), __('toastr.success'));
            return redirect()->back();
        }

        Toastr::warning(__('Erro ao tentar eliminar um pedido de equivalência!.'), __('toastr.danger'));
        return redirect()->back();
    }




    public function equivalence_student_store(Request $request)
    {

        try {



            $users_transf_id = $request->users_transf_id;
            $id_user = $request->user_data;
            $disciplines = $request->equivalence_disciplina;
            // Emolumento no ano LEctivo

            // $emolumento_equivalence = EmolumentCodevLective(['equivalencia_disciplina'],$request->anoLective)->first();
            // if(!$emolumento_equivalence){
            //     Toastr::warning(__('A forLEARN não encontrou um emolumento de Pedido de Equivalência Por Disciplina [ configurado no ano lectivo selecionado].'), __('toastr.warning'));
            //     return redirect()->back();
            // }
            $currentData = Carbon::now();
            foreach ($disciplines as $item) {
                //Emolumento
                $consulta = DB::table('tb_equivalence_studant_discipline')
                    ->where('id_transference_user', $users_transf_id)
                    ->where('id_discipline_equivalence', $item)
                    ->get();

                if ($consulta->isEmpty()) {

                    //reaproveitar para criar emolumento
                    // $r4 =createAutomaticArticleRequestExame($id_user, $emolumento_equivalence->id_emolumento, null, null,$item);

                    $insert = DB::table('tb_equivalence_studant_discipline')->insert([
                        'id_transference_user' => $users_transf_id,
                        'id_discipline_equivalence' => $item,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                        'created_at' => $currentData,
                    ]);
                } else {


                    $affected = DB::table('tb_equivalence_studant_discipline')
                        ->where('id_transference_user', $users_transf_id)
                        ->where('id_discipline_equivalence', $item)
                        ->update(['updated_by' => Auth::user()->id, 'updated_at' => $currentData]);
                }
            }

            //Estado de há disciplina adicionada ou não

            $affected_status = DB::table('tb_transference_studant')
                ->where('id', $users_transf_id)
                ->where('user_id', $id_user)
                ->update(['status_disc' => 1]);

            //Faz a deleção de todos que já fizeram parte parte e nessa nova edição já não !
            $affected_delete = DB::table('tb_equivalence_studant_discipline')
                ->where('id_transference_user', $users_transf_id)
                ->whereNotIn('id_discipline_equivalence', $disciplines)
                ->delete();



            Toastr::success(__('Equivalência de disciplina foi efectuada com sucesso.'), __('toastr.success'));
            return back();
        } catch (Exception | Throwable $e) {
            return $e;
            Toastr::error($e->getMessage(), __('toastr.error'));
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

















    public function transference_studant_store(Request $request)
    {


        try {
            //codev dos emolumentos
            $request->get('tipe_transference') == 1 ? $article_id = "pedido_t_entrada" : $article_id = "pedido_t_saida";

            //Emolumento com base no ano lectivo
            $emolumento_transferencia  = EmolumentCodevLective($article_id, $request->anoLectivo);
            if ($emolumento_transferencia->isEmpty()) {
                Toastr::warning(__('A forLEARN não encontrou um emolumento de pedido de transferência configurado[ configurado no ano lectivo selecionado].'), __('toastr.warning'));
                return redirect()->back();
            }
            $articleRequest = $emolumento_transferencia[0]->id_emolumento;

            //Emolumento
            $consulta = DB::table('tb_transference_studant')
                ->where('user_id', $request->students)
                ->whereNull('deleted_at')
                ->get();

            if (!$consulta->isEmpty()) {
                Toastr::warning(__('A forLEARN detectou que já existe uma marcação de pedido de transferência para este estudante, por favor verifique a existência do emolumento na tesouraria para validar a mesma , caso contrário contacte o apoio a forLEARN'), __('toastr.warning'));
                return redirect()->back();
            }

            $article = createAutomaticArticleRequest($request->students, $articleRequest, null, null);

            if (!$article) {
                Toastr::error(__(' Não foi possivel criar o emolumento pedido de tranferência, por favor tente novamente'), __('toastr.error'));
                return redirect()->back();
            }


            DB::table('tb_transference_studant')->insert([
                'user_id' => $request->students,
                'lectiveYear' => $request->anoLectivo,
                'school_name' => $request->school_name,
                'type_transference' => $request->tipe_transference,
                'created_by' => Auth::user()->id,
            ]);

            //

            Toastr::success(__('O pedido de transferência foi efectuado com sucesso.'), __('toastr.success'));
            return redirect()->back();
        } catch (Exception | Throwable $e) {

            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }












    public function create(LectiveYear $lective_year)
    {


        try {

            // $data = [
            //          'action' => 'create',
            //          'languages' => Language::whereActive(true)->get(),
            //          'lective_year'=>$lective_year,
            //          'users' => $this->studentsWithCourseAndMatriculationSelectList()
            //       ];


            // return view('Users::confirmations-matriculations.confirmation')->with($data);

        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    public function courses_change_store(Request $request)
    {

        try {

            $c_first = $request->course;
            $c_second = $request->courseNew;
            $Descricao = $request->Descricao;
            $Ano = $request->anoLective;
            $estado = $request->estado ?? 0;


            DB::transaction(function () use ($c_first, $c_second, $Ano, $Descricao, $estado) {
                DB::table('tb_courses_change')
                    ->updateOrInsert(
                        [
                            'course_id_primary' =>  $c_first,
                            'course_id_secundary' => $c_second,
                            'lective_year_id' => $Ano,
                        ],

                        [
                            'description' => $Descricao,
                            'status' => $estado,
                            'created_by' => Auth::user()->id,
                            'updated_by' => Auth::user()->id,

                        ]
                    );
            });

            Toastr::success(__('Associação foi criada com sucesso.'), __('toastr.success'));
            return redirect()->back();
        } catch (Exception | Throwable $e) {

            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }




    public function listSetting()

    {

        try {

            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();

            $courses = Course::with([
                'currentTranslation'
            ])->get();

            $roles = Role::with([
                'currentTranslation'
            ])
                ->where("id", "!=", 6)
                ->where("id", "!=", 15)
                ->where("id", "!=", 2)
                ->get();

            $currentData = Carbon::now();

            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();

            $data = [
                'action' => 'create',
                'courses' => $courses,
                'roles' => $roles,
                'lectiveYears' => $lectiveYears,
                'lectiveYearSelected' => $lectiveYearSelected->id
            ];


            return view('Users::Import-export-users.index')->with($data);
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }










    public function changeCourseAjax($LectiveYear)
    {


        try {


            // $lectiveYears = LectiveYear::with(['currentTranslation'])
            // ->get();

            $dados = DB::table('tb_courses_change as change')
                ->join('courses as c1', 'c1.id', '=', 'change.course_id_primary')
                ->join('courses as c2', 'c2.id', '=', 'change.course_id_secundary')
                ->join('users as u1', 'u1.id', '=', 'change.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'change.updated_by')

                ->join('courses_translations as ct1', function ($join) {
                    $join->on('ct1.courses_id', '=', 'c1.id');
                    $join->on('ct1.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct1.active', '=', DB::raw(true));
                })
                ->join('courses_translations as ct2', function ($join) {
                    $join->on('ct2.courses_id', '=', 'c2.id');
                    $join->on('ct2.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct2.active', '=', DB::raw(true));
                })
                ->where('change.lective_year_id', $LectiveYear)

                ->select(
                    'change.id',
                    'ct1.display_name as curso_de',
                    'ct2.display_name as curso_para',
                    'change.description as descricao',
                    'change.status as estado',
                    'u1.name as criado_por',
                    'change.created_at as criado_a',
                    'u2.name as actualizado_por',
                    'change.updated_at as actualizado_a'
                );


            return Datatables::of($dados)
                ->addColumn('actions', function ($item) {
                    return view('Users::equivalence.datatables.action')->with('item', $item);
                })

                ->rawColumns(['actions'])

                ->addIndexColumn()
                ->toJson();
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }













    public function ajaxUserData($studentId)
    {
        try {

            // $view = view("Users::confirmations-matriculations.disciplines")->with($data)->render();
            return response()->json(array('html' => $view));
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }







    public function store(Request $request)
    {
     
        try {

        
            $type_user = $request->selectedType;
            $exists = [];
            $success = [];
            $error = [];
            foreach ($request->users as $user) {
                $userData = collect($user);
                if (isset($userData['NomeCompleto'])) {
                    $nomeSemEspaco = str_replace(' ', ',', $userData['NomeCompleto']);
                    $preUser =  $this->convertToEmail($nomeSemEspaco);

                    $exist = $this->StoreUserImport($userData, $preUser, $request->course, $type_user, $request->role,$request->curricular_year);
                    $exist ?  $success[] = $userData : $exists[] = $userData;
                } else {
                    $error[] = $userData;
                }
            }


            return response()->json(["Error" => $error, "Sucess" => $success, "exists" => $exists, "ImportType" => $type_user == 1 ? "Estudante" : "Staffs"]);
        } catch (Exception | Throwable $e) {
            logError($e);
            return $e;
            
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    private function StoreUserImport($student, $preUser, $course_id, $type_user, $role,$curricular_year)
    {
        try{
        DB::beginTransaction();
        //Criar um e-mail da institucional
        $primeiroUltimoNome = $preUser['name'];
        $newEMail = $preUser['email'];

        $user = $this->validateUserExist($student);

        if (!$user) {
            // Create
            $user = User::create([
                'name' => $primeiroUltimoNome,
                'email' => $newEMail,
                'password' => bcrypt($student['BilhetedeIdentidade']),
                'created_by' => auth()->user()->id
            ]);

            $user->save();
            // Delete all relations
            $user->parameters()->sync([]);

            // full_name
            $user_parameters[] = [
                'parameters_id' => ParameterEnum::NOME,
                'created_by' => auth()->user()->id ?? 0,
                'parameter_group_id' => ParameterGroupEnum::DADOS_PESSOAIS,
                'value' => $student['NomeCompleto']
            ];

            //id_number
            $user_parameters[] = [
                'parameters_id' => ParameterEnum::BILHETE_DE_IDENTIDADE,
                'created_by' => auth()->user()->id ?? 0,
                'parameter_group_id' => ParameterGroupEnum::DOCUMENTOS_PESSOAIS,
                'value' => $student['BilhetedeIdentidade']
            ];
            //Nif
            $user_parameters[] = [
                'parameters_id' => ParameterEnum::NUMERO_DE_IDENTIFICACAO_FISCAL,
                'created_by' => auth()->user()->id ?? 0,
                'parameter_group_id' => ParameterGroupEnum::DOCUMENTOS_PESSOAIS,
                'value' => $student['BilhetedeIdentidade']
            ];
            // sexo
            $user_parameters[] = [
                'parameters_id' => ParameterEnum::SEXO,
                'created_by' => auth()->user()->id ?? 0,
                'parameter_group_id' => ParameterGroupEnum::DADOS_PESSOAIS,
                'value' => $student['Sexo'] == "M" ? "Masculino" : "Feminino"
            ];
            // NomePai
            $user_parameters[] = [
                'parameters_id' => ParameterEnum::NOME_DO_PAI,
                'created_by' => auth()->user()->id ?? 0,
                'parameter_group_id' => ParameterGroupEnum::DADOS_PESSOAIS,
                'value' => $student['NomedopaiCompleto'] ??""
            ];
            // NomeMae
            $user_parameters[] = [
                'parameters_id' => ParameterEnum::NOME_DA_MAE,
                'created_by' => auth()->user()->id ?? 0,
                'parameter_group_id' =>  ParameterGroupEnum::DADOS_PESSOAIS,
                'value' => $student['NomedameCompleto'] ?? ""
            ];
            // Data de Nascimento
            $user_parameters[] = [
                'parameters_id' => ParameterEnum::DATA_DE_NASCIMENTO,
                'created_by' => auth()->user()->id ?? 0,
                'parameter_group_id' => ParameterGroupEnum::DADOS_PESSOAIS,
                'value' => $student['Datadenascimento'] ?? ""
            ];
            // Nacionalidade
            $user_parameters[] = [
                'parameters_id' => ParameterEnum::NACIONALIDADE,
                'created_by' => auth()->user()->id ?? 0,
                'parameter_group_id' => ParameterGroupEnum::DADOS_PESSOAIS,
                'value' => $student['Nacionalidade']??""
            ];
            // Telefone
            $user_parameters[] = [
                'parameters_id' => ParameterEnum::TELEMOVEL_PRINCIPAL,
                'created_by' => auth()->user()->id ?? 0,
                'parameter_group_id' => ParameterGroupEnum::CONTACTOS,
                'value' => $student['Telemvel'] ?? ""
            ];
            // whatsapp
            $user_parameters[] = [
                'parameters_id' => ParameterEnum::WHATSAPP,
                'created_by' => auth()->user()->id ?? 0,
                'parameter_group_id' => ParameterGroupEnum::CONTACTOS,
                'value' => $student['Whatsapp'] ?? ""
            ];
            // E-mail pessoal
            $user_parameters[] = [
                'parameters_id' => ParameterEnum::E_MAIL_2,
                'created_by' => auth()->user()->id ?? 0,
                'parameter_group_id' => ParameterGroupEnum::CONTACTOS,
                'value' => $student['email'] ?? ""
            ];

          
            // estado civil
            $user_parameters[] = [
                'parameters_id' => ParameterEnum::ESTADO_CIVIL,
                'created_by' => auth()->user()->id ?? 0,
                'parameter_group_id' => ParameterGroupEnum::DADOS_PESSOAIS,
                'value' => $student['EstadoCivil'] ?? ""
            ];
          
            if($type_user==1){

                // matriculation_number  - Voltar para validar a ordem
                $user_parameters[] = [
                    'parameters_id' => ParameterEnum::N_MECANOGRAFICO,
                    'created_by' => auth()->user()->id ?? 0,
                    'parameter_group_id' => ParameterGroupEnum::IDENTIFICACAO_ISPM,
                    'value' => $student['NdeMatrcula']
                ];
                 // Bolseiro
                 // $user_parameters[] = [
                    //     'parameters_id' => ParameterEnum::Bol,
                    //     'created_by' => auth()->user()->id ?? 0,
                    //     'parameter_group_id' => 3,
                    //     'value' => $request->get('id_number')
                    // ];

                    $courses[] = $course_id;
                    $user->courses()->sync($courses);
                    $user->syncRoles(RoleEnum::STUDENT);
                    $old_matriculation = isset($student['CdigoId']) ?$student['CdigoId'] : null;
                    $this->ImportDataSave($user,$type_user,$old_matriculation,null,$curricular_year);
                }else{
                    $user->syncRoles([$role]);
                    $this->ImportDataSave($user,$type_user,null,$role,null);
                }
            
                $user->parameters()->sync($user_parameters);
        }


        DB::commit();



        return  $user;
        }
        catch(Exception | Throwablw $e){
             logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }





    public function update($id)
    {
    }



    public function convertToEmail($name)
    {

        $pieces = explode(",", $name);
        //count the quantity of name to use in email
        $lenght = strlen($pieces[0]);

        //return first and last name
        $nameLenght = count($pieces);
        $firstAndLastName = $pieces[0] . " " .  $pieces[$nameLenght - 1];

        //checar caracteres com acentuacao
        //IMPORTANTE
        $specialCharacters = [
            "á" => "a", "à" => "a", "â" => "a", "ã" => "a", "Á" => "A", "À" => "A", "Â" => "A", "Ã" => "A",
            "È" => "E", "É" => "E", "è" => "e", "é" => "e", "Ê" => "E", "ê" => "e",
            "Ì" => "I", "Í" => "I", "ì" => "i", "í" => "i", "Î" => "I", "î" => "i",
            "Ç" => "c", "ç" => "c",
            "ó" => "o", "ò" => "o", "Ó" => "O", "Ò" => "O", "Ô" => "O", "Õ" => "O", "õ" => "o", "ô" => "o",
            "Ù" => "U", "Ú" => "U", "ù" => "u", "ú" => "u", "û" => "u", "Û" => "U"
        ];

        //Ultimo email  da cadeia de palavras
        $lastEmail = strtolower(strtr($pieces[0], $specialCharacters) . "." . strtr($pieces[$nameLenght - 1], $specialCharacters) . EnumVariable::$CONVERT_TO_EMAIL);


        for ($i = 0; $i <= $lenght; $i++) {
            $letter = strtr($pieces[0], $specialCharacters);
            $lastNameWithoutSpecialCharacters = strtr($pieces[$nameLenght - 1], $specialCharacters);
            $email = strtolower(substr($letter, 0, $i + 1) . "." . $lastNameWithoutSpecialCharacters . EnumVariable::$CONVERT_TO_EMAIL);

            $checkEmail = User::where('users.email', '=', $email)->get();

            if ($checkEmail->isEmpty()) {
                $email = $email;
                break;
            } else if ($lastEmail == $email) {
                //se o email for o último e já existe.
                $checkEmail_Point = User::where('users.email', '=', $email)->get();
                $rand = rand(10, 1000);
                $novoEmail = strtolower(strtr($pieces[0], $specialCharacters) . "." . strtr($pieces[$nameLenght - 1], $specialCharacters) . $rand . EnumVariable::$CONVERT_TO_EMAIL);
                if (!$checkEmail_Point->isEmpty()) {
                    $email = $novoEmail;
                }
            }
        }
        $data = ['name' => $firstAndLastName, 'email' => $email];
        return  $data;
    }


    private function validateUserExist($user)
    {
        try{
        $model = User::query()
            ->join('user_parameters as up_meca', function ($join) {
                $join->on('users.id', '=', 'up_meca.users_id')
                  
                    ->where('up_meca.parameters_id', ParameterEnum::BILHETE_DE_IDENTIDADE);
                    
            })
            ->select([
                'users.*',
            ])
            ->where('up_meca.value', $user['BilhetedeIdentidade'])
            ->exists();

        return $model;
        }
        catch(Exception | Throwablw $e){
             logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    private function ImportDataSave($user,$type_user,$old_matriculation,$role,$curricular_year){


        DB::transaction(function () Use($user,$type_user,$old_matriculation,$role,$curricular_year){

            DB::table('Import_data_forlearn')
            ->updateOrInsert(
                [
                    'id_user' =>  $user->id
                ],
                [
                    'type_user'  => $type_user,
                    'codigo_old' => $old_matriculation,
                    'role_user'  => $role,
                    'ano_curricular'=> $curricular_year,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now()
                ]
            );
                        
            
        });
    }
}
