<?php

namespace App\Modules\Avaliations\Controllers;

use App\Helpers\LanguageHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Avaliations\Models\Avaliacao;
use App\Modules\Avaliations\Models\AvaliacaoAluno;
use App\Modules\Avaliations\Models\Metrica;
use App\Modules\Avaliations\Models\PlanoEstudoAvaliacao;
use App\Modules\Avaliations\Models\TipoAvaliacao;
use App\Modules\Avaliations\Models\TipoMetrica;
use App\Modules\GA\Models\Course;
use App\Modules\GA\Models\Discipline;
use App\Modules\GA\Models\StudyPlan;
use App\Modules\GA\Models\StudyPlanEdition;
use Toastr;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class PlanoEstudoAvaliacaoController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return view("Avaliations::plano-estudo-avaliacao.plano-estudo-avaliacao");
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
    public function ajax()
    {
        try {
            $model = PlanoEstudoAvaliacao::join('users as u1', 'u1.id', '=', 'plano_estudo_avaliacaos.created_by')
                    ->leftJoin('users as u2', 'u2.id', '=', 'plano_estudo_avaliacaos.updated_by')
                    ->join('avaliacaos', 'avaliacaos.id', '=', 'plano_estudo_avaliacaos.avaliacaos_id')
                    ->join('study_plan_editions', 'study_plan_editions.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
                    ->leftJoin('study_plan_edition_translations as spet', function ($join) {
                        $join->on('spet.study_plan_editions_id', '=', 'study_plan_editions.id');
                        $join->on('spet.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('spet.active', '=', DB::raw(true));
                    })
                    ->join('disciplines', 'disciplines.id', '=', 'plano_estudo_avaliacaos.disciplines_id')
                    ->leftJoin('disciplines_translations as dt', function ($join) {
                        $join->on('dt.discipline_id', '=', 'disciplines.id');
                        $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('dt.active', '=', DB::raw(true));
                    })
                    ->select([
                        'plano_estudo_avaliacaos.id as plano_estudo_avaliacaos_id',
                        'avaliacaos.nome as nome',
                        'spet.display_name as spet_nome',
                        'dt.display_name as discipline_nome',
                        'u1.name as created_by',
                        'u2.name as updated_by',
                        'plano_estudo_avaliacaos.created_at as created_at',
                        'plano_estudo_avaliacaos.updated_at as updated_at'
                    ]);

            return DataTables::eloquent($model)
                    ->addColumn('actions', function ($item) {
                        return view('Avaliations::plano-estudo-avaliacao.datatables.actions')->with('item', $item);
                    })
                    ->rawColumns(['actions'])
                    ->toJson();
        } catch (Exception | Throwable $e) {
            logError($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            /*
            Apartir do id da Edição do Plano de Estudo
            Carregar o Planos de Estudo
            Apartir do Id do Plano de Estudo
            Carregar o Curso
            Apartir do Id do Curso
            Carregar as Disciplinas
            VIA AJAX
            */
            $edicao_plano_estudos = StudyPlanEdition::leftJoin('study_plan_edition_translations as spe', function ($join) {
                $join->on('spe.study_plan_editions_id', '=', 'study_plan_editions.id');
                $join->on('spe.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('spe.active', '=', DB::raw(true));
            })
            ->select([
                'study_plan_editions.id as study_plans_edition_id',
                'spe.display_name as spe_display_name',
            ])
            ->distinct()
            ->get();

            $avaliacaos = Avaliacao::select([
                'avaliacaos.id  as avaliacao_id',
                'avaliacaos.nome as avaliacao_nome'
            ])
            ->where('avaliacaos.lock', 1) //SO LISTAR AVALIACAO FECHADAS
            ->get();
            $data = [
                //'plano_estudos' => $plano_estudos,
                'avaliacaos' => $avaliacaos,
                'edicao_plano_estudos' => $edicao_plano_estudos,
                //'cursos' => $cursos,
                //'disciplinas' => $disciplinas
            ];

            return view("Avaliations::plano-estudo-avaliacao.create-plano-estudo-avaliacao")->with($data);
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
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
            /*verificar se uma determinada avaliacao ja esta
              associada a um determinado plano de estudo
              NOTA: UM PLANO DE ESTUDO NAO PODE ESTAR ASSOCIADA
              A MESMA AVALIACAO MAIS DE UMA VEZ
            */
            // return $request;
            
            
          $data = [
                        'avaliacao_id'=> $request->get('avaliacao_id'),
                        'edpe' => $request->get('study_plans_edition_id'),
                        'disciplina' => $request->get('discipline_id') 
                  ];
                  
            $message=[];
            $i=0;
        if(is_array($data['disciplina'])){
            foreach($data['disciplina'] as $key => $disciplina_id){
               
                
                //Gravar cada edição
                foreach($data['avaliacao_id'] as $avaliacao_id){
    
                    $stdplan_edition_id = $data['edpe'];
                    $spa_verif = PlanoEstudoAvaliacao::select([
                        'plano_estudo_avaliacaos.id as spea'
                     ])
                     ->where('plano_estudo_avaliacaos.avaliacaos_id', $avaliacao_id)
                     ->where('plano_estudo_avaliacaos.study_plan_editions_id', $stdplan_edition_id)
                     ->where('plano_estudo_avaliacaos.disciplines_id', $disciplina_id)
                     ->get();
        
                    if ($spa_verif->isEmpty()) {
                        $spa = new PlanoEstudoAvaliacao;
                        $spa->avaliacaos_id = $avaliacao_id;
                        $spa->study_plan_editions_id = $stdplan_edition_id;
                        $spa->disciplines_id = $disciplina_id;
                        $spa->created_by = Auth::user()->id;
                        $spa->save();
        
                        // Success message
                        
                         $textmessage= $key."-"."Foi detatado que já existe associação de uma avaliação com uma respetiva disciplina selecionada.";
                         $message=['type'=>1,'message'=>$textmessage];
                        // Toastr::success(__('Cadastro realizado com sucesso'), __('toastr.success'));
                        
                        
                        // return redirect()->route('plano_estudo_avaliacao.index');
                        
                    } else {
                        // error message
                        
                         $textmessage= $key."-"."Foi detatado que já existe associação de uma avaliação com uma respetiva disciplina selecionada.";
                         $message=['type'=>0,'message'=>$textmessage];
                        
                      
                    }
            
            
            
                 //fim do foreach de cada avaliação associada
                }
            //fim do foreach geral
            
            }   
        }
        else{
            $disciplina_id = $data['disciplina'];
            //Gravar cada edição
            foreach($data['avaliacao_id'] as $avaliacao_id){
    
                $stdplan_edition_id = $data['edpe'];
                $spa_verif = PlanoEstudoAvaliacao::select([
                    'plano_estudo_avaliacaos.id as spea'
                 ])
                 ->where('plano_estudo_avaliacaos.avaliacaos_id', $avaliacao_id)
                 ->where('plano_estudo_avaliacaos.study_plan_editions_id', $stdplan_edition_id)
                 ->where('plano_estudo_avaliacaos.disciplines_id', $disciplina_id)
                 ->get();
    
                if ($spa_verif->isEmpty()) {
                    $spa = new PlanoEstudoAvaliacao;
                    $spa->avaliacaos_id = $avaliacao_id;
                    $spa->study_plan_editions_id = $stdplan_edition_id;
                    $spa->disciplines_id = $disciplina_id;
                    $spa->created_by = Auth::user()->id;
                    $spa->save();
    
                    // Success message
                    
                     $textmessage= "Foi detatado que já existe associação de uma avaliação com uma respetiva disciplina selecionada.";
                     $message=['type'=>1,'message'=>$textmessage];
                    // Toastr::success(__('Cadastro realizado com sucesso'), __('toastr.success'));
                    
                    
                    // return redirect()->route('plano_estudo_avaliacao.index');
                    
                } else {
                    // error message
                    
                     $textmessage= "Foi detatado que já existe associação de uma avaliação com uma respetiva disciplina selecionada.";
                     $message=['type'=>0,'message'=>$textmessage];
                    
                  
                }
        }
    }
                
             Toastr::warning(__($message['message']), __('toastr.warning'));
             return redirect()->route('plano_estudo_avaliacao.index');
            
            
            
        } catch (Exception | Throwable $e) {
            return $e;
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
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $getPEA = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stde', 'stde.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
            ->leftJoin('study_plan_edition_translations as spe', function ($join) {
                $join->on('spe.study_plan_editions_id', '=', 'stde.id');
                $join->on('spe.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('spe.active', '=', DB::raw(true));
            })
            ->leftJoin('disciplines as dc', 'dc.id', '=', 'plano_estudo_avaliacaos.disciplines_id')
             ->leftJoin('disciplines_translations as dcp', function ($join) {
                 $join->on('dcp.discipline_id', '=', 'dc.id');
                 $join->on('dcp.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                 $join->on('dcp.active', '=', DB::raw(true));
             })
            ->leftJoin('avaliacaos as avl', 'avl.id', '=', 'plano_estudo_avaliacaos.avaliacaos_id')

            ->select([
                'plano_estudo_avaliacaos.id as pea_id',
                'stde.id as study_plans_edition_id',
                'spe.display_name as spe_display_name',
                'avl.nome as avaliacao_nome',
                'avl.id as avaliacao_id',
                'dcp.display_name as discipline_name',
                'dc.id as dc_id'
            ])
            ->where('plano_estudo_avaliacaos.id', $id)
            ->distinct()
            ->get();

            $edicao_plano_estudos = StudyPlanEdition::leftJoin('study_plan_edition_translations as spe', function ($join) {
                $join->on('spe.study_plan_editions_id', '=', 'study_plan_editions.id');
                $join->on('spe.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('spe.active', '=', DB::raw(true));
            })

            ->select([
                'study_plan_editions.id as study_plans_edition_id',
                'spe.display_name as spe_display_name',
            ])

            ->distinct()
            ->get();

            $avaliacaos = Avaliacao::select([
                'avaliacaos.id  as avaliacao_id',
                'avaliacaos.nome as avaliacao_nome'
            ])
            ->where('avaliacaos.lock', 1) //SO LISTAR AVALIACAO FECHADAS
            ->get();
            $data = [
                'getPEA' => $getPEA,
                'avaliacaos' => $avaliacaos,
                'edicao_plano_estudos' => $edicao_plano_estudos,

            ];

            return view("Avaliations::plano-estudo-avaliacao.edit-plano-estudo-avaliacao")->with($data);
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
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
        try {
            //Saber se esse plano estudo avaliacao ja esta envolvida em uma avaliacao aluno
            $getPEA = AvaliacaoAluno::select('*')->where('plano_estudo_avaliacaos_id', $id)->get();

            if ($getPEA->isEmpty()) {
                $hidden_disc = $request->get('hidden_disc');
                $disc_id = $request->get('discipline_id');

                $pea = PlanoEstudoAvaliacao::find($id);
                $pea->avaliacaos_id = $request->get('avaliacao_id');
                $pea->study_plan_editions_id = $request->get('study_plans_edition_id');
                $pea->disciplines_id = isset($disc_id) ? $disc_id : $hidden_disc;
                $pea->updated_by = Auth::user()->id;
                $pea->save();

                // Success message
                Toastr::success(__('Registo editado com sucesso'), __('toastr.success'));
                return redirect()->route('plano_estudo_avaliacao.index');
            } else {
                // error message
                Toastr::error(__('Esse plano de estudo, avaliação e disciplina já estão com notas atribuidas'), __('toastr.error'));
                return redirect()->route('plano_estudo_avaliacao.index');
            }
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
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
            $getPEA = AvaliacaoAluno::where('plano_estudo_avaliacaos_id', $id)->get();
            if (!$getPEA->isEmpty()) {
                // Error message
                Toastr::error(__('Plano estudo e avaliação já foi associada'), __('toastr.error'));
                return redirect()->route('plano_estudo_avaliacao.index');
            } else {
                $spa = PlanoEstudoAvaliacao::find($id);
                $spa->delete();
                $spa->deleted_by = Auth::user()->id;
                $spa->save();

                // Success message
                Toastr::success(__('Registo apagado com sucesso'), __('toastr.success'));
                return redirect()->route('plano_estudo_avaliacao.index');
            }
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function editar_spa()
    {
    }

    public function studyPlanAjax($id)
    {

        //TODO: quando carregar as diciplinas avaliar sempre se o professor tem.
        $studyPlans = StudyPlanEdition::join('study_plans', 'study_plans.id', '=', 'study_plan_editions.study_plans_id')
                    ->leftJoin('study_plan_translations as spt', function ($join) {
                        $join->on('spt.study_plans_id', '=', 'study_plans.id');
                        $join->on('spt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('spt.active', '=', DB::raw(true));
                    })
                    ->leftJoin('courses', 'courses.id', '=', 'study_plans.courses_id')
                    ->leftJoin('courses_translations as ct', function ($join) {
                        $join->on('ct.courses_id', '=', 'courses.id');
                        $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('ct.active', '=', DB::raw(true));
                    })
                    ->leftJoin('disciplines', 'disciplines.courses_id', '=', 'courses.id')
                     ->leftJoin('disciplines_translations as dt', function ($join) {
                         $join->on('dt.discipline_id', '=', 'disciplines.id');
                         $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                         $join->on('dt.active', '=', DB::raw(true));
                     })
                    //Exibir só disciplinas que o PROFESSOR TEM
                    //RETIRAR
                    //->leftJoin('user_disciplines', 'user_disciplines.disciplines_id', '=', 'disciplines.id')

                    ->select([
                        'study_plans.id as study_plans_id',
                        'spt.display_name as spt_display_name',
                        'courses.id as course_id',
                        'ct.display_name as ct_display_name',
                        'disciplines.id as discipline_id',
                        'dt.display_name as dt_display_name'
                    ])
                    ->where('study_plan_editions.id', $id)
                    ->orderBy('dt_display_name')
                    ->get();
        return json_encode(array('data'=>$studyPlans));
    }
}
