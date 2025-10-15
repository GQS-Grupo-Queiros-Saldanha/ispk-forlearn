<?php

namespace App\Modules\Avaliations\Controllers;

use App\Helpers\LanguageHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Avaliations\Models\Avaliacao;
use App\Modules\Avaliations\Models\AvaliacaoConfig;
use App\Modules\Avaliations\Models\AvalicaoAlunoHistorico;
use App\Modules\Avaliations\Models\Metrica;
use App\Modules\Avaliations\Models\PlanoEstudoAvaliacao;
use App\Modules\Avaliations\Models\TipoAvaliacao;
use App\Modules\Avaliations\Models\TipoMetrica;
use App\Modules\Avaliations\Models\PautaAvaliationStudentShow;
use Toastr;
use App\Modules\GA\Models\LectiveYear;
use App\Modules\GA\Models\Course;
use App\Modules\Users\Models\Matriculation;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use App\Modules\Avaliations\Models\Avaliations;
use App\Modules\Users\Models\User;

class RecoveryMacController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
             //Pegar o ano lectivo na select
                $lectiveYears = LectiveYear::with(['currentTranslation'])
                    ->get();
            
                $currentData = Carbon::now();
                $lectiveYearSelected = DB::table('lective_years')
                        ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
                        ->first();
                $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
                    //-----------------------------------------------------------------------

                    $semestre= DB::table('discipline_periods as period')
                    ->leftJoin('discipline_period_translations as dt', function ($join) {
                    $join->on('dt.discipline_periods_id', '=', 'period.id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                    })
                    ->select(['dt.display_name','period.id'])
                    ->orderBy('dt.display_name', 'asc')
                    ->get();

                    $courses = Course::with(['currentTranslation'])->get();

                    $Pauta=DB::table('tb_estatistic_avaliation')
                    ->select(['pautaType as PautaCode','descrition_type_p as NamePauta'])
                    ->distinct()
                    ->get();

                $data = [
                          'courses' => $courses,
                          'semestre' => $semestre,
                          'lectiveYearSelected'=>$lectiveYearSelected,
                          'lectiveYears'=>$lectiveYears,
                          'config'=>$this->avaliacaoConfig($lectiveYearSelected),
                          'Pautas'=>$Pauta
                        ];

            return view("Avaliations::recovery_mac_avaliation.index")->with($data);

        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    private function avaliacaoConfig($id_anoLectivo){
        return AvaliacaoConfig::where(['lective_year' => $id_anoLectivo])->first();
     }
 
    public function StudantMacDispenseOnDisciplina(Request $request){
            $dados= Matriculation::join('users as u0', 'u0.id', '=', 'matriculations.user_id')
            ->join('users as u1', 'u1.id', '=', 'matriculations.created_by')
            ->leftJoin('users as u2', 'u2.id', '=', 'matriculations.updated_by')
            ->leftJoin('users as u3', 'u3.id', '=', 'matriculations.deleted_by')
            ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'u0.id')     
            ->join('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'uc.courses_id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })

            ->join('publicar_pauta as pauta', 'pauta.id_ano_lectivo', '=', 'matriculations.lective_year')

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
      
            ->leftJoin('new_old_grades as percurso', function ($join) {
                 $join->on('percurso.discipline_id','=','pauta.id_disciplina');
                 $join->on('percurso.user_id','=','u0.id');
                //  ->where('percurso.user_id',);
            })
      
            ->select([  
                      
                        'matriculations.lective_year',
                        'matriculations.course_year as curricular_year',
                        'u0.id as id_usuario',
                        'matriculations.code as code_matricula',
                        'up_meca.value as matricula',
                        'cl.display_name as classe',
                        'u0.email as email',    
                        'ct.display_name as course',
                        'pauta.id_turma as id_turma',
                        'pauta.pauta_tipo as pauta',
                        'pauta.id_disciplina as pauta_id_disciplina',
                        'percurso.grade as nota_percurso',
                        'uc.courses_id as id_course',
                                                            
                   ])
          
            ->where('uc.courses_id',$request->course_ids)
            ->where('pauta.id_disciplina',$request->disciplina_ids)
            ->where('pauta.estado',1)
            ->where('pauta.tipo',40)
            ->whereNull('matriculations.deleted_by')
            ->groupBy('u_p.value')
            ->distinct('id')
            ->where('matriculations.lective_year', $request->anoLectivo);
            //->where('matriculations.lective_year', 7)
            // ->get();
            $ids=$dados->pluck('u0.id_usuario');
           

             
              $MetricasCOde_dev = ['PF1', 'PF2', 'OA'];

              $utilizadores = DB::table('avaliacao_alunos as avl')
                ->join('metricas as mt', 'mt.id', 'avl.metricas_id')
                ->join('avaliacaos as av', 'av.id', 'mt.avaliacaos_id')
             
                ->join('plano_estudo_avaliacaos as plano', function ($join) {
                    $join->on('plano.avaliacaos_id', '=', 'av.id');
                    $join->on('plano.id', '=', 'avl.plano_estudo_avaliacaos_id');
                })
        
                ->join('users as student', 'student.id', 'avl.users_id')
                ->leftJoin('user_parameters as u_p9', function ($q) {
                    $q->on('student.id', '=', 'u_p9.users_id')
                        ->where('u_p9.parameters_id', 1);
                })
                ->select([
                    'mt.nome as metricas',
                    'u_p9.value as Estudante',
                    'avl.nota',
                    'av.anoLectivo as Lectivo',
                    'student.id',
                    'plano.disciplines_id as id_disciplina'
                ])
                ->whereIn('mt.code_dev', $MetricasCOde_dev)
                ->whereIn('student.id', $ids)
                ->where('plano.disciplines_id', $request->disciplina_ids)
                ->where('av.anoLectivo', $request->anoLectivo)
              
                ->distinct('avl.metricas_id')
                ->get();

                $SudentWithGrade = collect($utilizadores) ->groupBy('Estudante');
                
                $medias = [];
                $novo = [];

                foreach ($SudentWithGrade->all() as $nota) {
                    // Obter as notas de cada métrica
                    $pf1 = $nota->where('metricas', 'PF1')->first()->nota ?? 0;
                    $pf2 = $nota->where('metricas', 'PF2')->first()->nota ?? 0;
                    $oa = $nota->where('metricas', 'OA')->first()->nota ?? 0;
                    $estudante = $nota->where('Estudante',"!=",null)->first()->Estudante;
                    $estudanteId = $nota->where('Estudante',"!=",null)->first()->id;
                    $id_disciplina = $nota->where('Estudante',"!=",null)->first()->id_disciplina;
                    if(round(($pf1 * 0.4) + ($pf2 * 0.4) + ($oa * 0.2))>=14){

                        $medias[$estudante] =[
                            "pf1" => $pf1,
                            "pf2" => $pf2,
                            "oa" => $oa,
                            "media" => round(($pf1 * 0.4) + ($pf2 * 0.4) + ($oa * 0.2)),
                            "id_estudante" => $estudanteId,
                            "id_disciplina" => $id_disciplina,
                            "Resultado" => round(($pf1 * 0.4) + ($pf2 * 0.4) + ($oa * 0.2)) >=14 ? "Aprovado":"Reprovado",
                         ];
                 }

            }
            
            
               // return $medias;
            
             $MetricasCOde_devs = ['PF1', 'PF2', 'OA','Neen','Oral'];
    
            //id turma - 16
            //id disciplina - 1183
               //pegar os utilizadores que lançaram as notas 
             return  $utilizadores = DB::table('avaliacao_alunos as avl')
            ->join('metricas as mt', 'mt.id', 'avl.metricas_id')
            ->leftJoin('user_parameters as u_p9', function ($q) {
                $q->on('avl.updated_by', '=', 'u_p9.users_id')
                    ->where('u_p9.parameters_id', 1);
            })
            ->join('plano_estudo_avaliacaos as plano', 'plano.id', 'avl.plano_estudo_avaliacaos_id')
            ->select(['avl.updated_by as criado_por', 'mt.nome as metricas', 'u_p9.value as criador_fullname','plano.disciplines_id as disciplina'])
            // ->where('avl.id_turma', $turna_anoLectivo[0]->id_turma)
            ->where('avl.id_turma',1)
            
            ->whereIn('mt.code_dev', $MetricasCOde_devs)
            ->where('plano.disciplines_id',1185)
            ->distinct('avl.metricas_id')
    
            ->get();


          
            
          

         return response()->json(["dados_Request"=>$request,"Estudantes"=>$medias,"Alunos"=>$SudentWithGrade->all()]);
         

    }

    public function ajax($anoLectivo)
    { 
        
        try {
            $calendario_avalicao = Avaliacao::join('users as u1', 'u1.id', '=', 'avaliacaos.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'avaliacaos.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'avaliacaos.deleted_by')
                ->leftJoin('calendario_prova as calend',function ($join)
                {
                    $join->on('calend.id_avaliacao', '=', 'avaliacaos.id')
                    ->whereNull('calend.deleted_by')
                    ->whereNull('calend.deleted_at');
                    
                })
                ->join('tipo_avaliacaos as ta', 'ta.id', '=', 'avaliacaos.tipo_avaliacaos_id')
                ->select([
                    'avaliacaos.anoLectivo as anoLectivo',
                    'avaliacaos.id as avaliacao_id',
                    'avaliacaos.lock as avaliacao_lock',
                    'avaliacaos.nome',
                    'avaliacaos.code_dev as code_dev',

                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'ta.nome as ta_nome',

                    'avaliacaos.created_at as created_at',
                    'avaliacaos.updated_at as updated_at',

                    'calend.id_avaliacao as calend_id_avaliacao', 
                    'calend.deleted_at as deleted_at',
                    'calend.deleted_by as deleted_by'
                ])
                ->distinct('calendario_prova.id_avaliacao')
                ->where('avaliacaos.anoLectivo',$anoLectivo)
                ->where('ta.anoLectivo',$anoLectivo);
                   
            return DataTables::eloquent($calendario_avalicao)
                    ->addColumn('actions', function ($item) {
                            return view('Avaliations::avaliacao.datatables.actions')->with(['item'=>$item]);
                    })
                    ->rawColumns(['actions'])
                    ->toJson();
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
 

    
    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Http\Response
     */
    public function create($Year)
    {
        try {
                
               //Pegar o ano lectivo na select
           $lectiveYears = LectiveYear::with(['currentTranslation'])
           ->get();
   
          $currentData = Carbon::now();
          $lectiveYearSelected = DB::table('lective_years')
               ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
               ->first();
           $lectiveYearSelected = $lectiveYearSelected->id ?? 6;


           if($lectiveYearSelected!=$Year){ Toastr::error(__('Erro na busca do ano lectivo corrente para criação de uma nova avaliação, por favor tente novamente!'), __('toastr.error'));
            return redirect()->route('avaliacao.index');}


          $tipo_avaliacaos = TipoAvaliacao::where('anoLectivo',$lectiveYearSelected)->get();

            return view("Avaliations::avaliacao.create-avaliacao", compact('tipo_avaliacaos'));
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {

           //Pegar o ano lectivo na select
           $lectiveYears = LectiveYear::with(['currentTranslation'])
           ->get();
   
             $currentData = Carbon::now();
             $lectiveYearSelected = DB::table('lective_years')
               ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
               ->first();
            $lectiveYearSelected = $lectiveYearSelected->id ?? 6;

             $avaliacao = Avaliacao::create([
                'nome' => $request->get('nome'),
                'tipo_avaliacaos_id' => $request->get('tipo_avaliacao'),
                'percentage' => $request->get('percentage'),
                'anoLectivo' => $lectiveYearSelected,
                'created_by' => Auth::user()->id,
             ]);
             $avaliacao->save();
            //return redirect()->route('plano_estudo_avaliacao.index');

            // Success message
            Toastr::success(__('Registo cadastrado com sucesso'), __('toastr.success'));
            return redirect()->route('avaliacao.index');
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
    public function show($id)
    {
        return $id;
    }

    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return $id;
    }

    /**
     * Update the specified resource in storage.
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
        $getAV_1 = Metrica::where('avaliacaos_id', $id)->get();
        $getAV_2 = PlanoEstudoAvaliacao::where('avaliacaos_id', $id)->get();
        //$getAV_3 = AvalicaoAlunoHistorico::where('avaliacaos_id', $id)->get();


        /* if (!$getAV_1->isEmpty() && !$getAV_2->isEmpty() && !$getAV_3->isEmpty()) {
             // Error message
             Toastr::error(__('Avaliação já foi associada'), __('toastr.error'));
             return redirect()->route('avaliacao.index');
         } else*/


        if (!$getAV_1->isEmpty() && !$getAV_2->isEmpty()) {
            // Error message
            Toastr::error(__('Avaliação já foi associada'), __('toastr.error'));
            return redirect()->route('avaliacao.index');
        } else {
            //return $id;
            $currentData = Carbon::now();
            $id_user=Auth::user()->id;
            DB::table('calendario_prova')
                         ->where('id_avaliacao', $id)
                             ->update(
                          [
                           'updated_by' => $id_user,
                           'updated_at' => $currentData,
                           'deleted_at' => $currentData,
                           'deleted_by' => $currentData
                          ]         
                      ); 
            $av = Avaliacao::find($id);
            $av->delete();
            $av->deleted_by = Auth::user()->id;
            //$av->save();
            // Success message
            Toastr::success(__('Avaliação eliminada com sucesso'), __('toastr.success'));
            return redirect()->route('avaliacao.index');
        }
    }

 



    // PREENCHE A TABELA DE AVALIAÇÕES
    public function showPainelAvaliationTabela($lective_year)
    {
        
        try {

             $coordinator_course = DB::table('coordinator_course  as coordenador_curso')
             ->where('coordenador_curso.user_id',Auth::user()->id) 
            ->get();

            $professor = DB::table('users  as professor')
            ->join('user_classes as turma', 'professor.id', '=', 'turma.user_id')
            ->join('user_disciplines  as disciplina', 'professor.id', '=', 'disciplina.users_id')
             ->where('professor.id',Auth::user()->id) 
             ->select(['professor.id as id_prof','turma.class_id as turma','disciplina.disciplines_id as disciplina'])
            ->get();  
         
          $plano_avaliacao = DB::table('plano_estudo_avaliacaos  as pea')
             ->join('avaliacaos  as avaliacao', 'avaliacao.id', '=', 'pea.avaliacaos_id')
             ->whereNull('pea.deleted_by')
             ->select(['pea.study_plan_editions_id as codigo_plano','pea.disciplines_id as codigo_disciplina','avaliacao.nome as avaliacao'])
             ->get();

            

         
             $allDiscipline = DB::table('study_plan_editions  as edpe')
            ->join('study_plans as stp', 'stp.id', '=', 'edpe.study_plans_id')
            ->join('study_plans_has_disciplines as stp_discipline', 'stp_discipline.study_plans_id', '=', 'stp.id')
            ->join('courses as curso', 'curso.id', '=', 'stp.courses_id')
            
            ->join('study_plan_edition_disciplines as stdp_disci', 'stdp_disci.study_plan_edition_id', '=', 'edpe.id')
            ->join('disciplines as disciplina', 'disciplina.id', '=', 'stdp_disci.discipline_id')
            ->join('classes as turma', function($join){
                $join->on('turma.courses_id', '=', 'curso.id');
                $join->on('turma.year', '=', 'edpe.course_year');
            })


            ->leftJoin('disciplines_translations as dt', function ($join) {
                $join->on('dt.discipline_id', '=', 'stdp_disci.discipline_id');
                $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dt.active', '=', DB::raw(true));
            })
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'curso.id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })

            ->select(['edpe.lective_years_id as anoLectivo','edpe.course_year as ano_curricular','curso.id as curso_id','ct.display_name as nome_curso','stdp_disci.discipline_id as id_disciplina_no_plano','dt.display_name as nome_disciplina','turma.display_name as nome_turma','turma.courses_id as turma_id_curso','stp.courses_id as id_curso_plano','turma.year as TurmaAno_1','turma.id as id_turma','disciplina.code as codigo_disciplina'])
            ->where('edpe.lective_years_id', $lective_year) 
  

            ->where('turma.lective_year_id', $lective_year)  
          
            ->whereNull('turma.deleted_at')  
            ->whereNull('stp.deleted_at')  
            ->whereNull('curso.deleted_at')  
            ->distinct('dt.display_name')
            // ->limit(400)
            ->get();
 


            // Lista as pautas em função das disciplinas de um determinado coordenador

            /*
            
            $coordenador = DB::table("users as coordenador")
            ->join('coordinator_course as coordenador_curso', 'coordenador_curso.user_id', '=', 'coordenador.id')
            ->where('coordenador_curso.courses_id',"=",$allDiscipline[100]->curso_id)
            ->select(["coordenador.name as nome_coordenador"])
            ->get();
            
            */
            
            $dados = DB::table('pauta_path as pauta')
                ->join('publicar_pauta as publicar', 'publicar.id', '=', 'pauta.id_publicar_pauta')
                ->join('lective_years as lective_year', 'lective_year.id', '=', 'publicar.id_ano_lectivo')
                ->join('lective_year_translations as lective_year_translation', 'lective_year_translation.lective_years_id', '=', 'lective_year.id')
                ->join('classes', 'classes.id', '=', 'publicar.id_turma')
                ->join('disciplines_translations', 'disciplines_translations.discipline_id', '=', 'publicar.id_disciplina')
                ->join('users as user', 'user.id', '=', 'publicar.id_user_publish')
                ->join('users as user1', 'user1.id', '=', 'publicar.updated_by')
                ->select([
                    'pauta.path as pauta_link',
                    'pauta.last as pauta_last',
                    'publicar.estado as pauta_estado',
                    'publicar.pauta_tipo as pauta_tipo',
                    'pauta.created_at as data_publicacao',
                    'pauta.updated_at as data_atualizacao',
                    'user.name as nome_usuario',
                    'user1.name as atualizacao_usuario',
                    'lective_year_translation.description as ano_lectivo',
                    'classes.display_name as nome_turma',
                    'classes.id as id_turma',
                    'disciplines_translations.display_name as nome_disciplina',
                    'disciplines_translations.discipline_id as id_disciplina'
                ])
                ->groupBy('pauta.id')
                ->orderBy('pauta.updated_at', 'DESC')
                ->distinct('publicar.pauta_tipo')
                ->where('publicar.id_ano_lectivo', $lective_year) 
                ->where('pauta.last',"=",1) 
                ->get();
              


            return Datatables::of($allDiscipline)
                // ->addColumn('actions', function ($item) {
                //     return view('Avaliations::avaliacao.datatables.actions_pauta_show')->with('item',$item);
                // })
                // // ->addColumn('states', function ($state) {
                // //     return view('Avaliations::avaliacao.datatables.states_pauta_show')->with('state',$state);
                // // })
                ->addColumn('mac', function ($allDiscipline) use ($dados,$coordinator_course,$professor){
                    return view('Avaliations::avaliacao.datatables.mac',compact('dados','allDiscipline','coordinator_course','professor'));
                }) 
                ->addColumn('exame', function ($allDiscipline) use ($dados,$coordinator_course,$professor){
                    return view('Avaliations::avaliacao.datatables.exame',compact('dados','allDiscipline','coordinator_course','professor'));
                })
                ->addColumn('cf', function ($allDiscipline) use ($dados,$coordinator_course,$professor){
                    return view('Avaliations::avaliacao.datatables.final',compact('dados','allDiscipline','coordinator_course','professor'));
                })
                ->addColumn('recurso', function ($allDiscipline) use ($dados,$coordinator_course,$professor){
                    return view('Avaliations::avaliacao.datatables.recurso',compact('dados','allDiscipline','coordinator_course','professor'));
                })
                ->addColumn('exame_especial', function ($allDiscipline) use ($dados,$coordinator_course,$professor){
                    return view('Avaliations::avaliacao.datatables.exame_especial',compact('dados','allDiscipline','coordinator_course','professor'));
                })
                ->addColumn('seminario', function ($allDiscipline) use ($dados,$coordinator_course,$professor){
                    return view('Avaliations::avaliacao.datatables.seminario',compact('dados','allDiscipline','coordinator_course','professor'));
                })
                ->addColumn('tfc', function ($allDiscipline) use ($dados,$coordinator_course,$professor){
                    return view('Avaliations::avaliacao.datatables.tfc',compact('dados','allDiscipline','coordinator_course','professor'));
                })
                 ->rawColumns([/*'actions', 'states',*/'mac','exame','cf','recurso','exame_especial', 'seminario', 'tfc'])
                ->addIndexColumn()
                ->toJson();


        } catch (Exception | Throwable $e) {
            return response()->json($e);
            logError($e);
            return response()->json($e->getMessage(), 500);
        }
    }




 



    
}
