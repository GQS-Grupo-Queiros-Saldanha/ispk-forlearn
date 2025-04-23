<?php

namespace App\Modules\Avaliations\Controllers;

use App\Helpers\LanguageHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Avaliations\Models\Avaliacao;
use App\Modules\Avaliations\Models\AvaliacaoAluno;
use App\Modules\Avaliations\Models\AvaliacaoAlunoHistorico;
use App\Modules\Users\Models\User;
use App\Modules\Avaliations\Models\Metrica;
use App\Modules\Avaliations\Models\PlanoEstudoAvaliacao;
use App\Modules\Avaliations\Models\TipoAvaliacao;
use App\Modules\Avaliations\Models\TipoMetrica;
use App\Modules\GA\Models\Course;
use App\Modules\GA\Models\Discipline;
use App\Modules\GA\Models\StudyPlan;
use App\Modules\GA\Models\StudyPlanEdition;
use App\NotaEstudante;
use Brian2694\Toastr\Toastr as ToastrToastr;
use Toastr;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;
use Yajra\DataTables\Facades\DataTables;
use App\Modules\Users\Models\Matriculation;


use App\Modules\Cms\Controllers\mainController;
use Log;

class AvaliacaoAlunoHistoricoController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        try {
            DB::transaction(function () use ($request) {
                $edples = $request->get('course_id');
                $discipline_id = $request->get('discipline_id');
                $class_id = $request->get('classes');
                $avaliacao_id = $request->get('avaliacao_id');

                $spea = PlanoEstudoAvaliacao::join('avaliacaos', 'avaliacaos.id', '=', 'plano_estudo_avaliacaos.avaliacaos_id')
                        ->select('plano_estudo_avaliacaos.id')
                        ->where('plano_estudo_avaliacaos.study_plan_editions_id', $edples)
                        ->where('plano_estudo_avaliacaos.avaliacaos_id', $avaliacao_id)
                        ->where('plano_estudo_avaliacaos.disciplines_id', $discipline_id)
                        ->first();

                $data = [
                    'notas'=> $request->get('nota_final'),
                    'estudantes' => $request->get('user_id'),
                    'empty_grades' => $request->get('empty_grade')
                ];


                for ($i=0; $i < count($data['estudantes']); $i++) {
                    $avaliacaoHistorico = AvaliacaoAlunoHistorico::updateOrCreate(
                        [
                      'plano_estudo_avaliacaos_id' => $spea->id,
                      'avaliacaos_id' => $avaliacao_id,
                      'user_id' => $data['estudantes'][$i],
                      'class_id' => $class_id
                    ],
                        [
                      'nota_final' => $data['notas'][$i],
                      'created_by' => Auth::user()->id,
                    ]
                    );

                    $avaliacao = Avaliacao::whereId($avaliacao_id)->first();

                    //fazer o arredondamento das notas
                    //anchor

                    //Verificar se a disciplina tem o exame obrigatorio

                    //senao tiver,

                    //Se for MAC e tiver menos nota < 6 nao achar percentagem

                    /*IMPORTANTE ADICIONAR REGRA DE QUANDO FOR RECURSO NAO MULTIPLICAR COM A PERCENTAGEM */
                    /* 1 - avaliar se a discipliina tem exame obrigatorio
                        2 - avaliar se é recurso para nao achar percentagem */

                    $disciplineHasMandatoryExam = Discipline::join('discipline_has_exam', 'discipline_has_exam.discipline_id', '=', 'disciplines.id')
                                                ->select('discipline_has_exam.has_mandatory_exam as exam')
                                                ->where('disciplines.id', $discipline_id)
                                                ->firstOrFail();

                    if ($disciplineHasMandatoryExam->exam == 1) {
                        if ($avaliacao_id == 21 && $data['notas'][$i] < 6.5) {
                            DB::table('percentage_avaliation')
                                        ->updateOrInsert(
                                            ['user_id' => $data['estudantes'][$i], 'avaliation_id' => $avaliacao_id, 'discipline_id' => $discipline_id, 'class_id' => $class_id, 'plano_estudo_avaliacaos_id' => $spea->id],
                                            ['percentage_mac' => $data['notas'][$i], 'state' => 1]
                                        );
                        } elseif ($avaliacao_id == 21 && $data['notas'][$i] >= 6.5) {
                            DB::table('percentage_avaliation')
                                        ->updateOrInsert(
                                            ['user_id' => $data['estudantes'][$i], 'avaliation_id' => $avaliacao_id, 'discipline_id' => $discipline_id, 'class_id' => $class_id],
                                            ['percentage_mac' => (($avaliacao->percentage / 100) * $data['notas'][$i]), 'state' => 1, 'plano_estudo_avaliacaos_id' => $spea->id]
                                        );
                        } elseif ($avaliacao_id == 23) {
                            DB::table('percentage_avaliation')
                                        ->updateOrInsert(
                                            ['user_id' => $data['estudantes'][$i], 'avaliation_id' => 21, 'discipline_id' => $discipline_id, 'class_id' => $class_id],
                                            ['percentage_neen' => (($avaliacao->percentage / 100) * $data['notas'][$i]), 'state' => 1, 'plano_estudo_avaliacaos_id' => $spea->id]
                                        );
                        }
                    } else {
                        if ($avaliacao_id == 21) {
                            if ($data['notas'][$i] >= 6.5 && $data['notas'][$i] <= 13) {
                                DB::table('percentage_avaliation')
                                        ->updateOrInsert(
                                            ['user_id' => $data['estudantes'][$i], 'avaliation_id' => $avaliacao_id, 'discipline_id' => $discipline_id, 'class_id' => $class_id],
                                            ['percentage_mac' => (($avaliacao->percentage / 100) * $data['notas'][$i]), 'state' => 1, 'plano_estudo_avaliacaos_id' => $spea->id]
                                        );
                            } elseif ($data['notas'][$i] >= 13.5 && $data['notas'][$i] <= 20) {
                                DB::table('percentage_avaliation')
                                        ->updateOrInsert(
                                            ['user_id' => $data['estudantes'][$i], 'avaliation_id' => $avaliacao_id, 'discipline_id' => $discipline_id, 'class_id' => $class_id],
                                            ['percentage_mac' => $data['notas'][$i], 'state' => 1, 'plano_estudo_avaliacaos_id' => $spea->id]
                                        );
                            } elseif ($data['notas'][$i] >= 0 && $data['notas'][$i] <= 6) {
                                DB::table('percentage_avaliation')
                                        ->updateOrInsert(
                                            ['user_id' => $data['estudantes'][$i], 'avaliation_id' => $avaliacao_id, 'discipline_id' => $discipline_id, 'class_id' => $class_id],
                                            ['percentage_mac' => $data['notas'][$i], 'state' => 1, 'plano_estudo_avaliacaos_id' => $spea->id]
                                        );
                            }
                        } elseif($avaliacao_id == 23){
                            DB::table('percentage_avaliation')
                                        ->updateOrInsert(
                                            ['user_id' => $data['estudantes'][$i], 'avaliation_id' => 21, 'discipline_id' => $discipline_id, 'class_id' => $class_id],
                                            ['percentage_neen' => (($avaliacao->percentage / 100) * $data['notas'][$i]), 'state' => 1, 'plano_estudo_avaliacaos_id' => $spea->id]
                                        );
                        }
                    }




                    /*AvaliacaoAluno::create([
                        'plano_estudo_avaliacaos_id' => 2,
                        'metricas_id' => $metrica_id,
                        'users_id' => $data['estudantes'][$i],
                        'nota' => $data['notas'][$i],
                        'created_by' => Auth::user()->id
                    ]);*/
                  // echo $data['notas'][$i] . " - " . $data['estudantes'][$i] . "<br>";
                }
            });


            // Success message
            Toastr::success(__('Registo inserido com sucesso'), __('toastr.success'));
            return back();
        } catch (Exception | Throwable $e) {
            Toastr::error($e->getMessage(), __('toastr.error'));
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
   
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

      // Metodo de Percursos Academicos
    public function show($id)  
    {
        
        try {
            
            return view('Avaliations::curricular-path.index')->with('id_aluno',$id);
        
        } catch (Exception | Throwable $e) {
            Log::error($e);
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

    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return $id;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function gerarClassificacao()
    {
        //Precisa-se do ID da Disciplina

        $data = DB::table('percentage_avaliation')
        ->leftJoin('users', 'percentage_avaliation.user_id', '=', 'users.id')
                ->selectRaw(DB::raw('sum(nota) as nota, user_id, users.name'))
                //where para pegar por disciplina
                ->groupBy('user_id')
                //->where('discipline_id', 150)
                 ->get();
        return view("Avaliations::avaliacao-aluno.gerar-classificacao", compact('data'));
    }

    public function storeClassificacao(Request $request)
    {
        $data = DB::table('percentage_avaliation')
                ->groupBy('user_id');

        return $data;
    }

    public function studyPlanEditionClosedAjax()
    {
        $pea = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
            ->leftJoin('study_plan_edition_translations as spet', function ($join) {
                $join->on('spet.study_plan_editions_id', '=', 'stpeid.id');
                $join->on('spet.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('spet.active', '=', DB::raw(true));
            })
            ->leftJoin('avaliacao_aluno_historicos', 'avaliacao_aluno_historicos.plano_estudo_avaliacaos_id', '=', 'plano_estudo_avaliacaos.id')

                    ->select([
                        'stpeid.id as spea_id',
                        'spet.display_name as spea_nome'
                    ])
                    ->whereNotExists(function ($q) {
                        $q->select('plano_estudo_avaliacaos.id')
                       // $q->select('plano_estudo_avaliacaos.avaliacaos_id')
                          ->from('plano_estudo_avaliacaos')
                          ->whereRaw('avaliacao_aluno_historicos.plano_estudo_avaliacaos_id = plano_estudo_avaliacaos.id');
                        // ->whereRaw('avaliacao_aluno_historico.avaliacaos_id = plano_estudo_avaliacaos.avaliacaos_id');
                    })
                    ->distinct()
                    ->get();

        return response()->json($pea);

    }

    public function curricularPath()
    {   

        if(Auth::check() && Auth::user()->hasRole('student')) {
                    
            $payment_state = mainController::get_payments();
            
            if(isset($payment_state['dividas']["pending"]) && ($payment_state['dividas']["pending"]>0)){
                Toastr::warning(__('Para visualizar as notas lançadas, dirija-se a Tesouraria para regularizar 
                os seus pagamentos!'), __('toastr.warning'));
                return back();
            }
            
        } 
        
        
        if ($this->getRoles(Auth::user()->id,6)>0){
            $student = User::where('users.id', Auth::user()->id)
            ->leftJoin('user_parameters as u_p0', function ($join) {
                $join->on('users.id', '=', 'u_p0.users_id')
                    ->where('u_p0.parameters_id', 19);
            })
            ->leftJoin('user_parameters as u_p1', function ($join) {
                $join->on('users.id', '=', 'u_p1.users_id')
                    ->where('u_p1.parameters_id', 1);
            })
            ->join('user_courses', 'user_courses.users_id', '=', 'users.id')
            ->join('courses', 'courses.id', '=', 'user_courses.courses_id')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'courses.id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })
            ->select([
                'users.id',
                'users.email',
                'u_p0.value as number',
                'u_p1.value as name',
                'ct.courses_id', 
                'ct.display_name as course',
                'courses.id as course_id'

            ])
            ->get();

          
        return view('Avaliations::curricular-path.student', compact("student"));
        }else{  
            $id_aluno = 0;

        return view('Avaliations::curricular-path.index', compact("id_aluno"));
        }
      
    }

    public static function getRoles($model_id,$role_id)
    {




        $cargo = DB::table('users as usuario')
            ->join('model_has_roles as usuario_cargo', 'usuario.id', '=', 'usuario_cargo.model_id')

            ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')
            ->join('role_translations as roles_user', 'roles_user.role_id', '=', 'cargo.id')
            ->join('user_parameters as up', 'up.users_id', '=', 'usuario.id')
            ->where('up.parameters_id', 1)
            ->where('roles_user.active', 1)
            ->where('usuario_cargo.model_id', "=", $model_id)
            ->where('usuario_cargo.role_id', "=", $role_id)
            ->whereNull('usuario.deleted_at')
            ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
            ->select(['roles_user.display_name as role_user'])
            ->count();

            return $cargo;
    }

    public function curricularPathGetCourses($id_aluno)
    {
        $courses = Course::with('currentTranslation')->get();
        
         $getcoursoEstudante=DB::table('users as us')
        ->join('user_courses as uc',function($join)
        {
            $join->on('us.id','=','uc.users_id');
        })
        ->join('courses_translations as ct', function ($join) {
            $join->on('ct.courses_id', '=', 'uc.courses_id');
            $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
            $join->on('ct.active', '=', DB::raw(true));
        })
        ->where('us.id','=',$id_aluno)
        ->get();

        return response()->json(['course'=>$courses,'aluno'=>$getcoursoEstudante]);
    }

    public function curricularPathGetStudents($courseId)
    {
        return $students =  Matriculation::join('users as u0', 'u0.id', '=', 'matriculations.user_id')
                ->join('users as u1', 'u1.id', '=', 'matriculations.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'matriculations.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'matriculations.deleted_by')
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
                ->where('uc.courses_id', $courseId)
                ->select([
                    //'u0.name as student',
                    'u0.id as id',
                    'u_p.value as name',
                    'u0.email as email',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    'ct.display_name as course',
                    'up_meca.value as mecanografico'
                ])
                ->orderBy('name')
                ->get();
        return response()->json($students);
    }
    
    
    /*ZACARIAS LOCALIZAR PERCURSO*/

    public function curricularPathGrade()
    {
        return view('Avaliations::curricular-path.curricular_path_grade')->with('id_aluno',0);
    }

    public function curricularPathGetPauta()
    {
        $courses = Course::with('currentTranslation')->get();
        
        /*
        $getcoursoEstudante=DB::table('users as us')
        ->join('user_courses as uc',function($join)
        {
            $join->on('us.id','=','uc.users_id');
        })
        ->join('courses_translations as ct', function ($join) {
            $join->on('ct.courses_id', '=', 'uc.courses_id');
            $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
            $join->on('ct.active', '=', DB::raw(true));
        })
        ->where('us.id','=',$id_aluno)
        ->get();
        */


        /*
        $pauta_tfc = DB::table('publicar_pauta as pautas')
        ->join('disciplines as discipline')
        ->where('pauta.tipo', 50)
        ->get();
        */

        return response()->json(['course'=>$courses]);
    }


    public function curricularPathGetPautaStudents($courseId)
    {
        
        $pauta_tfc = DB::table('publicar_pauta as pautas')        
            ->join('disciplines as discipline', 'discipline.id', '=', 'pautas.id_disciplina')
            ->leftJoin('disciplines_translations as dcp', function ($join) {
                $join->on('dcp.discipline_id', '=', 'pautas.id_disciplina');
                $join->on('dcp.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dcp.active', '=', DB::raw(true));
            })
            
            //->join('new_old_grades as student_grades', 'student_grades.discipline_id', '=', 'pautas.id_disciplina')
            
            
            ->join('new_old_grades as student_grades', function ($join) {
                $join->on('student_grades.discipline_id', '=', 'pautas.id_disciplina');
                        //->where('student_grades.created_at', '=', 'pautas.created_at');
                //$join->on('student_grades.created_at', '=', 'pautas.created_at');
            })
            

            ->join('users as u0', 'u0.id', '=', 'student_grades.user_id')
            
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


            ->select([
                //'u0.name as student',
                'u0.id as id',
                'u_p.value as name',
                'u0.email as email',
                'ct.display_name as course',
                'up_meca.value as mecanografico',

                'student_grades.id as student_grades_id',
                'student_grades.lective_year as lective_year',
                'student_grades.grade as grade',
                'discipline.id as discipline_id',
                'discipline.code as code',
                'dcp.display_name as discipline_name',
                
                'student_grades.created_at as grades_created_at',
                'pautas.created_at as pautas_created_at'
            ])
            
            ->orderBy('name')
            ->groupBy('student_grades_id')
            ->distinct('id')
            
            ->where('pautas.tipo', 50)
            //->where('u0.id', $studentId)
            ->where('discipline.courses_id', $courseId)
            ->where('uc.courses_id', $courseId)
            //->where('student_grades.created_at')
        ->get();

        
        /**/
        $collection = collect($pauta_tfc);
        $pauta_tfc = $collection->groupBy('id', function($item){
                return ($item);
        });
        /**/
        

        $date = [
                'pauta_tfc' => $pauta_tfc                              
        ];
                    
               
               
        return response()->json(array('data'=>$date));








        return $pauta_tfc = DB::table('publicar_pauta as pautas')        
            ->join('disciplines as discipline', 'discipline.id', '=', 'pautas.id_disciplina')
            //->join('new_old_grades as student_grades', 'student_grades.discipline_id', '=', 'pautas.id_disciplina')
            
            ->join('new_old_grades as student_grades', function ($join) {
                $join->on('student_grades.discipline_id', '=', 'pautas.id_disciplina');
                        //->where('student_grades.created_at', '=', 'pautas.created_at');
            })

            ->join('users as u0', 'u0.id', '=', 'student_grades.user_id')
                //->join('users as u1', 'u1.id', '=', 'matriculations.created_by')
                //->leftJoin('users as u2', 'u2.id', '=', 'matriculations.updated_by')
                //->leftJoin('users as u3', 'u3.id', '=', 'matriculations.deleted_by')
            ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'u0.id')
            
            /*
            ->join('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'uc.courses_id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })
            */
            ->leftJoin('user_parameters as u_p', function ($join) {
                $join->on('u0.id', '=', 'u_p.users_id')
                    ->where('u_p.parameters_id', 1);
            })
            ->leftJoin('user_parameters as up_meca', function ($join) {
                $join->on('u0.id', '=', 'up_meca.users_id')
                        ->where('up_meca.parameters_id', 19);
            })           


            ->select([
                //'u0.name as student',
                'u0.id as id',
                'u_p.value as name',
                'u0.email as email',
                //'u1.name as created_by',
                //'u2.name as updated_by',
                //'u3.name as deleted_by',
                //'ct.display_name as course',
                'up_meca.value as mecanografico'
            ])
            ->orderBy('name')
            
            ->where('pautas.tipo', 50)
            ->where('discipline.courses_id', $courseId)
            ->where('uc.courses_id', $courseId)
            //->where('student_grades.created_at')
        ->get();

        return response()->json($pauta_tfc);
    }


    public function getStudentPercursoAcademicNotas($studentId)
    {
        
        try{


            $pauta_tfc = DB::table('publicar_pauta as pautas')        
            ->join('disciplines as discipline', 'discipline.id', '=', 'pautas.id_disciplina')
            //->join('new_old_grades as student_grades', 'student_grades.discipline_id', '=', 'pautas.id_disciplina')
            ->leftJoin('disciplines_translations as dcp', function ($join) {
                $join->on('dcp.discipline_id', '=', 'pautas.id_disciplina');
                $join->on('dcp.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dcp.active', '=', DB::raw(true));
            })
            
            ->join('new_old_grades as student_grades', function ($join) {
                $join->on('student_grades.discipline_id', '=', 'pautas.id_disciplina');
                        //->where('student_grades.created_at', '=', 'pautas.created_at');
            })

            ->join('users as u0', 'u0.id', '=', 'student_grades.user_id')
                //->join('users as u1', 'u1.id', '=', 'matriculations.created_by')
                //->leftJoin('users as u2', 'u2.id', '=', 'matriculations.updated_by')
                //->leftJoin('users as u3', 'u3.id', '=', 'matriculations.deleted_by')
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


            ->select([
                //'u0.name as student',
                'u0.id as id',
                'u_p.value as name',
                'u0.email as email',
                //'u1.name as created_by',
                //'u2.name as updated_by',
                //'u3.name as deleted_by',
                'ct.display_name as course',
                'up_meca.value as mecanografico',

                'student_grades.id as student_grades_id',
                'student_grades.lective_year as lective_year',
                'student_grades.grade as grade',
                'discipline.id as id',
                'discipline.code as code',
                'dcp.display_name as discipline_name',
            ])
            
            ->orderBy('name')
            
            ->where('pautas.tipo', 50)
            ->where('u0.id', $studentId)
            //->where('discipline.courses_id', $courseId)
            //->where('uc.courses_id', $courseId)
            //->where('student_grades.created_at')
        ->get();

            $date = [
                  'pauta_tfc' => $pauta_tfc                              
            ];
                    
               
               
            return response()->json(array('data'=>$date)); 
            
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

}
