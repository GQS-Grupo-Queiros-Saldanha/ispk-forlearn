<?php

namespace App\Modules\Users\Controllers;

use App\Helpers\LanguageHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\Classes;
use App\Modules\GA\Models\Course;
use App\Modules\GA\Models\StudyPlan;
use App\Modules\Payments\Models\Article;
use App\Modules\Payments\Models\Transaction;
use App\Modules\Payments\Models\TransactionInfo;
use App\Modules\Payments\Models\ArticleRequest;
use App\Modules\Payments\Requests\ArticleRequestRequest;
use App\Modules\Payments\Models\DisciplineArticle;
use App\Modules\Users\Models\Matriculation;
use App\Modules\Users\Models\UserCandidate;
use App\Modules\Users\Models\User;
use App\Modules\Users\Models\UserState;
use App\Modules\Users\Models\UserStateHistoric;
use App\Modules\Users\Requests\MatriculationRequest;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;
use App\Modules\GA\Models\LectiveYear;
use Yajra\DataTables\Facades\DataTables as YajraDataTables;
use DataTables;
use LynX39\LaraPdfMerger\Facades\PdfMerger;
use PDF;
use App\Model\Institution;
use App\Modules\Users\util\MatriculationFinalistUtil;
use Illuminate\Support\Facades\Auth;

class MatriculationConfirmationFinalitController extends Controller
{
    //  metodo create for Mr. Gelson Matias

    public function newConfirmation_forFinalist($lective_year)
    {   
        try {
            $vetorStudent = [];
            $lectiveYears = DB::table('lective_years')->join('lective_year_translations', function ($q) {
                $q->on('lective_year_translations.lective_years_id', '=', 'lective_years.id')->where('lective_year_translations.language_id', 1)->where('lective_year_translations.active', 1);
            })
                ->select([
                    'lective_years.id as id',
                    'lective_year_translations.display_name as display_name'
                ])
                ->where('lective_years.id', $lective_year)->first();


            $getStudent = DB::table('users as use')
                ->join('matriculations as matriculat', 'matriculat.user_id', '=', 'use.id')
                ->join('matriculation_classes as matriculat_classe', 'matriculat_classe.matriculation_id', '=', 'matriculat.id')
                ->join('classes as classe', 'classe.id', '=', 'matriculat_classe.class_id')
                ->join('courses as curso', function ($q) {
                    $q->on('curso.id', '=', 'classe.courses_id');
                })
                ->leftJoin('user_parameters as name_full', function ($q) {
                    $q->on('name_full.users_id', '=', 'use.id')
                        ->where('name_full.parameters_id', 1);
                })
                ->leftJoin('user_parameters as matricula', function ($q) {
                    $q->on('matricula.users_id', '=', 'use.id')
                        ->where('matricula.parameters_id', 19);
                })
                ->select([
                    'use.email',
                    'matricula.value as matricula',
                    'name_full.value as name_full',
                    'use.name as nome',
                    'matriculat.lective_year',
                    'matriculat.id as id_matricula',
                    'matriculat.course_year',
                    'curso.duration_value',
                    'curso.id as id_curso'
                ])
                ->orderBy('matriculat.id', 'DESC')
                ->whereNull('matriculat.deleted_by')
                ->whereNull('matriculat.deleted_at')
                ->get()->map(function ($q) {
                    $q->{'full_nameEmail'} = $q->name_full != null ? $q->name_full . ' #' . $q->matricula . ' (' . $q->email . ')' :   $q->nome . ' #' . $q->matricula . ' (' . $q->email . ')';
                    return $q;
                });



            foreach ($getStudent as $key => $item) {
                if ($item->course_year != $item->duration_value) {
                    unset($getStudent[$key]);
                } else {
                    if (in_array($item->matricula, $vetorStudent)) {
                        unset($getStudent[$key]);
                    } else {
                        $vetorStudent[] = $item->matricula;
                    }
                }
            }



            $data = [
                'lectiveYears' => $lectiveYears,
                'getStudent' => $getStudent
            ];


            return view('Users::matriculations_finalist.index')->with($data);
        } catch (Exception | Throwable $e) {
            // return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    public function createConfirmation_Matriculation(Request $request)
    {
        try {

            $id_matricula = $request->user;
            $Year_lectiv = $request->ano_lectivo;
            $getMatriculation = DB::table('matriculations as matriculat', 'matriculat.user_id', '=', 'use.id')
                ->leftJoin('user_parameters as name_full', function ($q) {
                    $q->on('name_full.users_id', '=', 'matriculat.user_id')->where('name_full.parameters_id', 1);
                })
                ->leftJoin('user_parameters as matricula', function ($q) {
                    $q->on('matricula.users_id', '=', 'matriculat.user_id')->where('matricula.parameters_id', 19);
                })
                ->select(['matricula.value as matricula', 'matriculat.user_id as user_id', 'matriculat.id as id_matricula', 'name_full.value as name_full', 'matriculat.lective_year as lective_year'])->where('matriculat.id', $id_matricula);



            $lectiveYears = DB::table('lective_years')->join('lective_year_translations', function ($q) {
                $q->on('lective_year_translations.lective_years_id', '=', 'lective_years.id')->where('lective_year_translations.language_id', 1)->where('lective_year_translations.active', 1);
            })
                ->select([
                    'lective_years.id as id',
                    'lective_year_translations.display_name as display_name'
                ])
                ->where('lective_years.id', $Year_lectiv)->first();

            $explodeData = explode('/', $lectiveYears->display_name);
            $dataStart = '20' . $explodeData[1];

            $matriculado_noLectivo = false;

            $getMatricula = $getMatriculation->first();
            $getMatriculas_student = $getMatriculation->get();
            
            $getstundet_finalistMatriculatio = DB::table('matriculation_finalist')
                ->whereNull('matriculation_finalist.deleted_by')
                ->whereNull('matriculation_finalist.deleted_at')
                ->where('matriculation_finalist.year_lectivo', $Year_lectiv)
                ->where('matriculation_finalist.user_id', $getMatricula->user_id)
                ->get();

            $getLastMatriculation = $this->getInformation_lastMatriculation($id_matricula);

            foreach ($getMatriculas_student as $key => $item) {
                if ($item->lective_year == $Year_lectiv) {
                    $matriculado_noLectivo = true;
                }
            }

            // estrura de codição que verifica se o estudante já foi matriclado neste ano
            if ($getstundet_finalistMatriculatio->isEmpty() && $matriculado_noLectivo == false) {

                $nextCodeNumber = $dataStart . '0001';
                $latestsMatriculation = DB::table('matriculation_finalist')->latest()
                    ->whereNull('matriculation_finalist.deleted_by')
                    ->whereNull('matriculation_finalist.deleted_at')
                    ->first();

                if (isset($latestsMatriculation->id)) {
                    $nextCodeNumber = ((int)ltrim($latestsMatriculation->num_confirmaMatricula, 'CF') + 1);
                }
                $nextCode = 'CF' . $nextCodeNumber;
                $getinformaStad_plan_Student = $this->analistStudentFinalist($id_matricula, $Year_lectiv);
                $utilizador = Auth::user()->name;
                //dd($getinformaStad_plan_Student);
                if ($getinformaStad_plan_Student == 'finalista') {
                    $createEmolumento = $this->createEmolument($id_matricula, $Year_lectiv, $lectiveYears);

                    if ($createEmolumento == true) {
                        DB::table('matriculation_finalist')->insert([
                            'user_id' => $getMatricula->user_id,
                            'num_confirmaMatricula' => $nextCode,
                            'id_curso' => $getLastMatriculation->id_curso,
                            'year_curso' => $getLastMatriculation->duration_value,
                            'year_lectivo' => $Year_lectiv,
                            'created_at' => Carbon::Now(),
                            'created_by' => Auth::user()->id,
                            'updated_at' => Carbon::Now(),
                            'updated_by' => Auth::user()->id,
                        ]);
                        Toastr::success(__('Confirmação da matrícula para finalista, foi criada com sucesso.'), __('toastr.success'));
                        return redirect()->back();
                    } else {
                        return redirect()->back();
                    }
                } else if ($getinformaStad_plan_Student == "finalista_matriculado") {
                    Toastr::warning(__('Caro utilizador ' . $utilizador . ' o/a estudante <b>' . $getMatricula->name_full . '</b>, já finalizou o curso.'), __('toastr.warning'));
                    return redirect()->back();
                } else {
                    Toastr::warning(__('Caro utilizador ' . $utilizador . ' o/a estudante <b>' . $getMatricula->name_full . '</b>, não tem as disciplinas com as notas final regularizada.! verifique as suas notas.'), __('toastr.warning'));
                    return redirect()->back();
                }
            } else {
                $utilizador = Auth::user()->name;
                Toastr::warning(__('Caro utilizador ' . $utilizador . ' o/a estudante <b>' . $getMatricula->name_full . '</b>, já se encontra matrículado neste ano lectivo.'), __('toastr.warning'));
                return redirect()->back();
            }
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }



    private function createEmolument($id_matricula, $Year_lectiv, $lectiveYears)
    {


        $getLastMatriculation = $this->getInformation_lastMatriculation($id_matricula);
        $explode = explode('/', $lectiveYears->display_name);

        $dataStart = '20' . $explode[0];
        $dataEnd = '20' . $explode[1];

        $getArticle_lastMatriculation = DB::table('articles as article')
            //->join('article_monthly_charges as article_monthly','article_monthly.article_id','=','article.id')
            ->join('code_developer as code_dev', 'code_dev.id', '=', 'article.id_code_dev')
            ->whereNull('article.deleted_by')
            ->whereNull('article.deleted_at')
            ->where('article.anoLectivo', $Year_lectiv)
            //->where('article_monthly.course_id',$getLastMatriculation->id_curso)
            ->whereIn('code_dev.code', ['propina_finalista'])
            ->select([
                'article.id as id_article',
                'article.base_value as base_value',
                // 'article_monthly.start_month as start_month',
                // 'article_monthly.end_month as end_month',
            ])
            ->first();

        $getEmolumentConfirmacao = DB::table('articles as article')
            ->join('code_developer as code_dev', 'code_dev.id', '=', 'article.id_code_dev')
            ->whereNull('article.deleted_by')
            ->whereNull('article.deleted_at')
            ->where('article.anoLectivo', $Year_lectiv)
            ->whereIn('code_dev.code', ['confirm'])
            ->select([
                'article.id as id_article',
                'article.base_value as base_value'
            ])
            ->first();

        if (isset($getArticle_lastMatriculation->id_article) && isset($getEmolumentConfirmacao->id_article)) {
            // $start_month=$getArticle_lastMatriculation->start_month;
            // $end_month=$getArticle_lastMatriculation->end_month;
            $start_month = 3;
            $end_month = 7;

            $month = 0;
            if ($dataStart > 2020) {

                if ($start_month >= $end_month) {
                    while ($start_month <= 12) {
                        if ($start_month == $end_month) {
                            $month = $start_month;
                            $this->create_emolument_StudentFinalista($getLastMatriculation->user_id, $getArticle_lastMatriculation->base_value, $getArticle_lastMatriculation->id_article, $month, $dataStart);
                            break;
                        } else {
                            $month = $start_month;
                            $this->create_emolument_StudentFinalista($getLastMatriculation->user_id, $getArticle_lastMatriculation->base_value, $getArticle_lastMatriculation->id_article, $month, $dataStart);
                            $start_month++;
                        }
                    }
                    while ($end_month >= 1) {
                        $month = $end_month;
                        $this->create_emolument_StudentFinalista($getLastMatriculation->user_id, $getArticle_lastMatriculation->base_value, $getArticle_lastMatriculation->id_article, $month, $dataEnd);
                        $end_month -= 1;
                    }
                } else {

                    while ($start_month <= $end_month) {
                        // echo($start_month);
                        $month = $start_month;
                        $this->create_emolument_StudentFinalista($getLastMatriculation->user_id, $getArticle_lastMatriculation->base_value, $getArticle_lastMatriculation->id_article, $month, $dataEnd);
                        $start_month++;
                    }
                }
            } else {
                $start_month = 10;
                $end_month = 7;
                $month = 0;
                while ($start_month <= 12) {
                    if ($start_month == $end_month) {
                        $month = $start_month;
                        $this->create_emolument_StudentFinalista($getLastMatriculation->user_id, $getArticle_lastMatriculation->base_value, $getArticle_lastMatriculation->id_article, $month, $dataStart);
                        break;
                    } else {
                        $month = $start_month;
                        $this->create_emolument_StudentFinalista($getLastMatriculation->user_id, $getArticle_lastMatriculation->base_value, $getArticle_lastMatriculation->id_article, $month, $dataStart);
                        $start_month++;
                    }
                }
                while ($end_month >= 1) {
                    $month = $end_month;
                    $this->create_emolument_StudentFinalista($getLastMatriculation->user_id, $getArticle_lastMatriculation->base_value, $getArticle_lastMatriculation->id_article, $month, $dataEnd);
                    $end_month -= 1;
                }
            }
            $this->create_emolumentConfirmacao_StudentFinalista($getLastMatriculation->user_id, $getEmolumentConfirmacao->base_value, $getEmolumentConfirmacao->id_article);
            return true;
        } else {
            $utilizador = Auth::user()->name;
            return Toastr::warning(__('Caro utilizador ' . $utilizador . '.<br><br><br>  A forLEARN detectou que Ainda não foi criado emolumento (propina, para efeito de confirmação de matrícula para finalista) para o curso  <b>' . $getLastMatriculation->display_name . '</b>. <br><br> Para o ano lectivo ' . $getLastMatriculation->anoLectivo), __('toastr.warning'));
        }
    }

    private function filterCode($list, $abbr){
        $array = [];
        foreach($list as $item){
            if(str_contains($item->code, $abbr)){
                $array[] = $item;
            }
        }
        return $array;
    }

    private function analistStudentFinalist($id_matricula, $Year_lectiv)
    {
        $DisplionaPercuso = [];
        $qtdDisciplina = 0;
        $id_trabalho = null;

        $lectiveYears = DB::table('lective_years')->join('lective_year_translations', function ($q) {
            $q->on('lective_year_translations.lective_years_id', '=', 'lective_years.id')->where('lective_year_translations.language_id', 1)->where('lective_year_translations.active', 1);
        })->where('lective_years.id', $Year_lectiv)->first();

        $getMatriculation = DB::table('matriculations as matriculat', 'matriculat.user_id', '=', 'use.id')
            ->join('matriculation_classes as matriculat_classe', 'matriculat_classe.matriculation_id', '=', 'matriculat.id')
            ->join('classes as classe', 'classe.id', '=', 'matriculat_classe.class_id')
            ->join('courses as curso', function ($q) {
                $q->on('curso.id', '=', 'classe.courses_id');
            })
            ->select([
                'matriculat.id as id_matricula',
                'matriculat.user_id as user_id',
                'curso.id as id_curso',
                'curso.duration_value as duration_value',
                'matriculat.lective_year as lective_year'
            ])
            ->where('matriculat.id', $id_matricula)
            ->first();

        $getStudy_plan = DB::table('study_plans as study_plan')
            ->join('study_plan_editions as study_plan_edition', 'study_plan_edition.study_plans_id', '=', 'study_plan.id')
            ->join('study_plan_edition_disciplines as study_plan_edition_discipline', 'study_plan_edition_discipline.study_plan_edition_id', '=', 'study_plan_edition.id')
            ->join('disciplines as d','d.id','discipline_id')
            ->where('study_plan.courses_id', $getMatriculation->id_curso)
            ->where('study_plan_edition.lective_years_id', $getMatriculation->lective_year)
            ->where('study_plan_edition.course_year', $getMatriculation->duration_value)
            ->select([
                'study_plan_edition_discipline.discipline_id as  discipline_id', 'd.code'
            ])
            ->distinct('study_plan_edition_discipline.discipline_id')
            ->orderBy('study_plan_edition_discipline.discipline_id', 'ASC')
            ->get();

        $especialidades = [];
        foreach ($getStudy_plan as $key => $item) {
            $DisplionaPercuso[] = $item->discipline_id;
        }
        
       $getNotasPercusso = DB::table('new_old_grades')
            ->join('disciplines as d','d.id','discipline_id')
            ->where('new_old_grades.user_id', $getMatriculation->user_id)
            ->whereIn('new_old_grades.discipline_id', $DisplionaPercuso)
            ->where('new_old_grades.grade', '>', 9)
            ->select([
                'new_old_grades.discipline_id as  discipline_id',
                'new_old_grades.grade as  grade',
                'd.code'
            ])
            ->orderBy('new_old_grades.discipline_id', 'ASC')
            ->get();
            
        foreach ($getNotasPercusso as $key => $item) {
            $abbr = substr($item->code, 0, 3);
            $keys = array_keys($especialidades);
            $especialidades[$abbr] = !in_array($abbr, $keys) ? 0 : $especialidades[$abbr] + 1;
        }
            
        if(sizeof($especialidades) >= 1){
            $maior = 0;
            $abbr = "";
            foreach($especialidades as $key => $value)
                if($value > $maior){ $maior = $value; $abbr = $key;}
            $getStudy_plan = $this->filterCode($getStudy_plan, $abbr);
            $getNotasPercusso = $this->filterCode($getNotasPercusso, $abbr);
        }
      
        $boole =  count($getNotasPercusso) == count($getStudy_plan)  ?  'finalista' : 'n_finalista';

        $matriculationFinalist = null;

        if(isset($getMatriculation->user_id) && isset($getMatriculation->id_curso)){
            $matriculationFinalist = DB::table('matriculation_finalist')->where([
                'user_id' => $getMatriculation->user_id,
                'id_curso' => $getMatriculation->id_curso
            ])->first();
        }

        if ($boole == 'finalista') {
            if(isset($matriculationFinalist->id))
              $boole = 'finalista_matriculado';
        } else {
            $boole = count($getNotasPercusso) == count($getStudy_plan) - 1 ? 'finalista' : 'n_finalista';
        }

        return $boole;
    }

    private function create_emolument_StudentFinalista($user_id, $base_value, $id_article, $month, $year)
    {
        try {
            DB::beginTransaction();
            $articleRequestMonth = new ArticleRequest([
                'user_id' => $user_id,
                'article_id' => $id_article,
                'year' => $year,
                'month' => $month,
                'base_value' => $base_value,
                'meta' => '',
                'discipline_id' => ''
            ]);
            $articleRequestMonth->save();
            $transaction = Transaction::create([
                'type' => 'debit',
                'value' => $base_value,
                'notes' => 'Débito inicial do valor base'
            ]);

            $transaction->article_request()->attach($articleRequestMonth->id, ['value' => $articleRequestMonth->base_value]);
            DB::commit();
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    private function create_emolumentConfirmacao_StudentFinalista($user_id, $base_value, $id_article)
    {
        try {

            DB::beginTransaction();
            $articleRequestMonth = new ArticleRequest([
                'user_id' => $user_id,
                'article_id' => $id_article,
                'base_value' => $base_value,
                'meta' => '',
                'discipline_id' => ''
            ]);
            $articleRequestMonth->save();
            $transaction = Transaction::create([
                'type' => 'debit',
                'value' => $base_value,
                'notes' => 'Débito inicial do valor base'
            ]);

            $transaction->article_request()->attach($articleRequestMonth->id, ['value' => $articleRequestMonth->base_value]);
            DB::commit();
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    private function getInformation_lastMatriculation($id_matricula)
    {
        return $getLastMatriculation = DB::table('matriculations as matriculat', 'matriculat.user_id', '=', 'use.id')
            ->join('matriculation_classes as matriculat_classe', 'matriculat_classe.matriculation_id', '=', 'matriculat.id')
            ->join('classes as classe', 'classe.id', '=', 'matriculat_classe.class_id')
            ->join('courses as curso', 'curso.id', '=', 'classe.courses_id')
            ->join('courses_translations as corse_translation', function ($q) {
                $q->on('corse_translation.courses_id', '=', 'curso.id')
                    ->where('corse_translation.language_id', 1)
                    ->where('corse_translation.active', 1);
            })
            ->leftjoin('lective_year_translations as lective_year_translation', function ($q) {
                $q->on('lective_year_translation.lective_years_id', '=', 'matriculat.lective_year')
                    ->where('lective_year_translation.language_id', 1)
                    ->where('lective_year_translation.active', 1);
            })
            ->select([
                'matriculat.id as id_matricula',
                'matriculat.user_id as user_id',
                'curso.id as id_curso',
                'curso.duration_value as duration_value',
                'matriculat.lective_year as lective_year',
                'corse_translation.display_name as display_name',
                'lective_year_translation.display_name as anoLectivo',
                'lective_year_translation.lective_years_id as lective_years_id'
            ])
            ->where('matriculat.id', $id_matricula)
            ->first();
    }





    public function listaFinalista()
    {
        $lectiveYears = LectiveYear::with(['currentTranslation'])
            ->get();

        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
            ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
            ->first();
        $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
        $data = [
            'lectiveYears' => $lectiveYears,
            'lectiveYearSelected' => $lectiveYearSelected
        ];

     
        return view('Users::matriculations_finalist.listafinalista')->with($data);
    }


    public function ajaxListaFinalista(Request $request, $id_anoLective)
    {
        $vetorStudent = [];
        $emolumento_confirma_prematricula = $this->pre_matricula_confirma_emolumento($id_anoLective);
        $matriculaFinalistUtil = new MatriculationFinalistUtil($id_anoLective, $emolumento_confirma_prematricula);
        $getstundets_finalist = $matriculaFinalistUtil->requestGetStundetsFinalist($request);
        // ->get();

        $getStudent = DB::table('matriculations as matriculat')
            ->join('matriculation_classes as matriculat_classe', 'matriculat_classe.matriculation_id', '=', 'matriculat.id')
            ->join('classes as classe', 'classe.id', '=', 'matriculat_classe.class_id')
            ->join('courses as curso', function ($q) {
                $q->on('curso.id', '=', 'classe.courses_id');
            })
            ->leftJoin('user_parameters as matricula', function ($q) {
                $q->on('matricula.users_id', '=', 'matriculat.user_id')
                    ->where('matricula.parameters_id', 19);
            })
            ->select([
                'matricula.value as matricula',
                'matriculat.lective_year',
                'matriculat.id as id_matricula',
                'matriculat.user_id as user_id',
                'matriculat.course_year',
                'curso.duration_value',
                'curso.id as id_curso'
            ])
            ->whereNull('matriculat.deleted_by')
            ->whereNull('matriculat.deleted_at')
            ->get();


        foreach ($getStudent as $key => $item) {
            if ($item->course_year != $item->duration_value) {
                unset($getStudent[$key]);
            } else {
                if (in_array($item->matricula, $vetorStudent)) {
                    unset($getStudent[$key]);
                } else {
                    $vetorStudent[] = $item->matricula;
                }
            }
        }



        return Datatables::of($getstundets_finalist)
            ->addColumn('actions', function ($item) use ($getStudent) {
                return view('Users::matriculations_finalist.datatables.actions', compact('item', 'getStudent'));
            })
            ->addColumn('status', function ($state) {
                return view('Users::matriculations.datatables.states')->with('state', $state);
            })
            ->rawColumns(['actions', 'status'])
            ->addIndexColumn()
            ->toJson();
    }

    public function ajaxListaFinalista_forYear(Request $request, $idyear_lectivo)
    {
        $vetorStudent = [];
        $emolumento_confirma_prematricula = $this->pre_matricula_confirma_emolumento($idyear_lectivo);

        $matriculaFinalistUtil = new MatriculationFinalistUtil($idyear_lectivo, $emolumento_confirma_prematricula);
        $getstundets_finalist = $matriculaFinalistUtil->requestGetStundetsFinalist($request);
        // ->get();

        $getStudent = DB::table('matriculations as matriculat')
            ->join('matriculation_classes as matriculat_classe', 'matriculat_classe.matriculation_id', '=', 'matriculat.id')
            ->join('classes as classe', 'classe.id', '=', 'matriculat_classe.class_id')
            ->join('courses as curso', function ($q) {
                $q->on('curso.id', '=', 'classe.courses_id');
            })
            ->leftJoin('user_parameters as matricula', function ($q) {
                $q->on('matricula.users_id', '=', 'matriculat.user_id')
                    ->where('matricula.parameters_id', 19);
            })
            ->select([
                'matricula.value as matricula',
                'matriculat.lective_year',
                'matriculat.id as id_matricula',
                'matriculat.user_id as user_id',
                'matriculat.course_year',
                'curso.duration_value',
                'curso.id as id_curso'
            ])
            ->whereNull('matriculat.deleted_by')
            ->whereNull('matriculat.deleted_at')
            ->get();


        foreach ($getStudent as $key => $item) {
            if ($item->course_year != $item->duration_value) {
                unset($getStudent[$key]);
            } else {
                if (in_array($item->matricula, $vetorStudent)) {
                    unset($getStudent[$key]);
                } else {
                    $vetorStudent[] = $item->matricula;
                }
            }
        }

        return Datatables::of($getstundets_finalist)
            ->addColumn('actions', function ($item) use ($getStudent) {
                return view('Users::matriculations_finalist.datatables.actions', compact('item', 'getStudent'));
            })
            ->addColumn('status', function ($state) {
                return view('Users::matriculations.datatables.states')->with('state', $state);
            })
            ->rawColumns(['actions', 'status'])
            ->addIndexColumn()
            ->toJson();
    }


    private function pre_matricula_confirma_emolumento($lectiveYearSelected)
    {

        $confirm = EmolumentCodevLective("confirm", $lectiveYearSelected)->first();
        $Prematricula = EmolumentCodevLective("prematricula", $lectiveYearSelected)->first();
        $emolumentos = [];

        if ($confirm != null) {
            $emolumentos[] = $confirm->id_emolumento;
        }
        if ($Prematricula != null) {
            $emolumentos[] = $Prematricula->id_emolumento;
        }
        return $emolumentos;
    }

    public function deleteMatriculation_finalista($id_matriculation_finalist)
    {

        try {
            $update = DB::table('matriculation_finalist')
                ->where('id', $id_matriculation_finalist)
                ->update([
                    'deleted_by' => Auth::user()->id,
                    'deleted_at' => Carbon::Now()
                ]);

            Toastr::success(__('Confirmação da matrícula finalista, foi eliminada com sucesso.'), __('toastr.success'));
            return redirect()->back();
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function boletin_finalista($id)
    {
        try {
            $getstundets_finalist = DB::table('matriculation_finalist as matricula_finalist')
                ->join('users as use', 'use.id', '=', 'matricula_finalist.user_id')
                ->join('courses_translations as corse_translation', function ($q) {
                    $q->on('corse_translation.courses_id', '=', 'matricula_finalist.id_curso')
                        ->where('corse_translation.language_id', 1)
                        ->where('corse_translation.active', 1);
                })
                ->join('courses as curso', 'curso.id', '=', 'matricula_finalist.id_curso')
                ->leftJoin('user_parameters as name_full', function ($q) {
                    $q->on('name_full.users_id', '=', 'use.id')
                        ->where('name_full.parameters_id', 1);
                })
                ->leftJoin('user_parameters as fotografia', function ($q) {
                    $q->on('fotografia.users_id', '=', 'matricula_finalist.user_id')
                        ->where('fotografia.parameters_id', 25);
                })
                ->leftJoin('user_parameters as telemovel_principal', function ($q) {
                    $q->on('telemovel_principal.users_id', '=', 'matricula_finalist.user_id')
                        ->where('telemovel_principal.parameters_id', 36);
                })
                ->leftJoin('user_parameters as telemovel_alternativo', function ($q) {
                    $q->on('telemovel_alternativo.users_id', '=', 'matricula_finalist.user_id')
                        ->where('telemovel_alternativo.parameters_id', 37);
                })
                ->leftJoin('user_parameters as telefone_fixo', function ($q) {
                    $q->on('telefone_fixo.users_id', '=', 'matricula_finalist.user_id')
                        ->where('telefone_fixo.parameters_id', 38);
                })
                ->leftJoin('user_parameters as e_mail_alternativo', function ($q) {
                    $q->on('e_mail_alternativo.users_id', '=', 'matricula_finalist.user_id')
                        ->where('e_mail_alternativo.parameters_id', 38);
                })
                ->leftJoin('user_parameters as matricula', function ($q) {
                    $q->on('matricula.users_id', '=', 'use.id')
                        ->where('matricula.parameters_id', 19);
                })
                ->leftJoin('user_parameters as num_bi', function ($q) {
                    $q->on('num_bi.users_id', '=', 'use.id')
                        ->where('num_bi.parameters_id', 14);
                })
                ->select([
                    'matricula_finalist.id as id_matriculation_finalist',
                    'matricula_finalist.user_id as user_id',
                    'matricula_finalist.num_confirmaMatricula as num_confirmaMatricula',
                    'matricula_finalist.year_curso as year_curso',
                    'matricula_finalist.created_at as created_at',
                    'matricula_finalist.updated_at as updated_at',
                    'matricula.value as matricula',
                    'name_full.value as name_full',
                    'num_bi.value as num_bi',
                    'use.email as email',
                    'corse_translation.display_name as display_name',
                    'curso.duration_value as duration_value',
                    'fotografia.value as fotografia',
                    'telemovel_principal.value as telemovel_principal',
                    'telefone_fixo.value as telefone_fixo',
                    'telemovel_alternativo.value as telemovel_alternativo',
                    'e_mail_alternativo.value as e_mail_alternativo',
                ])
                ->where('matricula_finalist.id', $id)
                ->whereNull('matricula_finalist.deleted_by')
                ->whereNull('matricula_finalist.deleted_at');
            // ->get();

            $stundet_finalist = $getstundets_finalist->first();
            $institution = Institution::latest()->first();
            $titulo_documento = "Boletim de matrícula finalista";
            $documentoGerado_documento = "Documento gerado a";
            $documentoCode_documento = 6;
            // $footer_html = view()->make('Users::matriculations.partials.pdf_footer', compact('institution'))->render();
            $data = [
                'institution' => $institution,
                'titulo_documento' => $titulo_documento,
                'documentoGerado_documento' => $documentoGerado_documento,
                'stundet_finalist' => $stundet_finalist,
            ];
            $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();

            $pdf = PDF::loadView('Users::matriculations_finalist.partials.pdf_finalista', $data)

                ->setOption('header-html', '<header></header>');
            $pdf->setOption('margin-top', '5mm');
            $pdf->setOption('margin-left', '4mm');
            $pdf->setOption('margin-bottom', '1.5cm');
            $pdf->setOption('margin-right', '4mm');
            $pdf->setOption('enable-javascript', true);
            $pdf->setOption('debug-javascript', true);
            $pdf->setOption('javascript-delay', 1000);
            $pdf->setOption('enable-smart-shrinking', true);
            $pdf->setOption('no-stop-slow-scripts', true);
            $pdf->setOption('footer-html', $footer_html)
                ->setPaper('a4');

            // return $pdf->download('matriculation.pdf');
            return $pdf->stream('boletin-finalista' . '.pdf');
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
}