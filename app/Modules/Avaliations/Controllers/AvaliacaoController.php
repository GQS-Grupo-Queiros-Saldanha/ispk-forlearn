<?php

namespace App\Modules\Avaliations\Controllers;

use App\Helpers\LanguageHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Avaliations\Models\Avaliacao;
use App\Modules\Avaliations\Models\AvalicaoAlunoHistorico;
use App\Modules\Avaliations\Models\Metrica;
use App\Modules\Avaliations\Models\PlanoEstudoAvaliacao;
use App\Modules\Avaliations\Models\TipoAvaliacao;
use App\Modules\Avaliations\Models\TipoMetrica;
use App\Modules\Avaliations\Models\PautaAvaliationStudentShow;
use App\Modules\GA\Models\StudyPlanEdition;
use Toastr;
use App\Modules\GA\Models\LectiveYear;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use App\Modules\Avaliations\Models\Avaliations;
use App\Modules\Users\Models\User;
use App\Model\Institution;
use PDF;
use App\Modules\Avaliations\Controllers\AvaliacaoAlunoControllerNew;

class AvaliacaoController extends Controller
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
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();
            $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
            //-----------------------------------------------------------------------

            $semestre = DB::table('discipline_periods as period')
                ->leftJoin('discipline_period_translations as dt', function ($join) {
                    $join->on('dt.discipline_periods_id', '=', 'period.id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                })
                ->select(['dt.display_name', 'period.id'])
                ->orderBy('dt.display_name', 'asc')
                ->get();

            $data = [
                'semestre' => $semestre,
                'lectiveYearSelected' => $lectiveYearSelected,
                'lectiveYears' => $lectiveYears
            ];


            return view("Avaliations::avaliacao.avaliacao")->with($data);
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    public function duplicar_avaliacao(Request $request)
    {

        try {

            // Verifica se avaliação existe
            $exit_avaliacao = DB::table('avaliacaos as avaliacao')
                ->where('avaliacao.id', $request->flag_avaliation)
                ->get();

            // if ($exit_avaliacao->isEmpty())
            // {
            //     return 2530;
            // }
            // else            
            // {
            // Verifica se já existe uma avaliação com estas caratéristicas para este ano lectivo
            $copy_avaliacao = DB::table('avaliacaos as avaliacao')
                ->where('avaliacao.nome', $exit_avaliacao[0]->nome)
                ->where('avaliacao.anoLectivo', $request->lective_years)
                ->where('avaliacao.code_dev', $exit_avaliacao[0]->code_dev)
                ->get();

            // Caso não encontrar uma valiação no ano lectivo em questão, faz a copia da avalição
            if ($copy_avaliacao->isEmpty()) {
                // Pesquisa pela avaliação
                $copy_tipo_avaliacao = DB::table('tipo_avaliacaos')
                    ->where('id', $exit_avaliacao[0]->tipo_avaliacaos_id)
                    ->get();
                // ->first();

                // Caso não encontrar um tipo de valiação, faz a copia do tipo de avaliação
                if (!$copy_tipo_avaliacao->isEmpty()) {
                    // Pesquisa pela metrica
                    $copy_metricas = DB::table('metricas')
                        ->where('avaliacaos_id', $exit_avaliacao[0]->id)
                        ->get();
                    // ->first();

                    if (!$copy_metricas->isEmpty()) {
                        // DUPLICA DO TIPO AVALIAÇÃO                
                        $tipo_avaliacao_id = DB::table('tipo_avaliacaos')->insertGetId([
                            'nome' => $copy_tipo_avaliacao[0]->nome,
                            'codigo' => $copy_tipo_avaliacao[0]->codigo,
                            'descricao' => $copy_tipo_avaliacao[0]->descricao,
                            'anoLectivo' => $request->lective_years,
                            'abreviatura' => $copy_tipo_avaliacao[0]->abreviatura,
                            'created_at' => Carbon::Now(),
                            'created_by' => Auth::user()->id,
                        ]);

                        // DUPLICA A AVALIAÇÃO
                        $avaliacao_id = DB::table('avaliacaos')->insertGetId([
                            'nome' => $exit_avaliacao[0]->nome,
                            'tipo_avaliacaos_id' => $tipo_avaliacao_id,
                            'percentage' => $exit_avaliacao[0]->percentage,
                            'anoLectivo' => $request->lective_years,
                            'lock' => 0,
                            'data_inicio' => $exit_avaliacao[0]->data_inicio,
                            'data_fim' => $exit_avaliacao[0]->data_fim,
                            'code_dev' => $exit_avaliacao[0]->code_dev,
                            'created_at' => Carbon::Now(),
                            'created_by' => Auth::user()->id,
                        ]);

                        $save = False;

                        // DUPLICA A METRICA
                        foreach ($copy_metricas as $metrica) {
                            // Pesquisa pelo tipo de metrica
                            $exit_tipo_metrica = DB::table('tipo_metricas')
                                ->where('id', $metrica->tipo_metricas_id)
                                ->get();

                            // Caso haja um tipo de metrica
                            if (count($exit_tipo_metrica) > 0 && $save == False) {
                                $save == True;

                                // Cria o tipo de metrica
                                $tipo_metrica_id = DB::table('tipo_metricas')->insertGetId([
                                    'nome' => $exit_tipo_metrica[0]->nome,
                                    'codigo' => $exit_tipo_metrica[0]->codigo,
                                    'descricao' => $exit_tipo_metrica[0]->descricao,
                                    'anoLectivo' => $request->lective_years,
                                    'abreviatura' => $exit_tipo_metrica[0]->abreviatura,
                                    'created_at' => Carbon::Now(),
                                    'created_by' => Auth::user()->id,
                                ]);
                            }

                            // Cria a metrica
                            $metrica_save = DB::table('metricas')->insertGetId([
                                'nome' => $metrica->nome,
                                'percentagem' => $metrica->percentagem,
                                'avaliacaos_id' => $avaliacao_id,
                                'tipo_metricas_id' => $tipo_metrica_id,
                                'calendario' => $metrica->calendario,
                                'code_dev' => $metrica->code_dev,
                                'created_at' => Carbon::Now(),
                                'created_by' => Auth::user()->id,
                            ]);


                            // DUPLICA O CALENDARIO DE PROVA
                            // $calendario_save = DB::table('calendario_prova')->insertGetId([
                            //     'code' => $calendario->code,
                            //     'display_name' => $calendario->display_name,
                            //     'date_start' => $calendario->date_start,
                            //     'data_end' => $calendario->data_end,
                            //     'lectiveYear' => $request->lective_years,
                            //     'id_avaliacao' => $avaliacao_id,
                            //     'simestre' => $calendario->simestre,
                            //     'created_at' => Carbon::Now(),
                            //     'created_by' => Auth::user()->id,
                            // ]);
                        }

                        // DUPLICA O CALENDARIO DE AVALIAÇÕES
                        // DUPLICA O CALENDARIO DE PROVA

                        Toastr::success(__('A avaliação foi duplicada com sucesso'), __('toastr.success'));
                        return redirect()->route('avaliacao.index');
                    } else {
                        Toastr::error(__('Erro na duplicação da avaliação, porque não existe uma metrica associada a esta avaliação!'), __('toastr.error'));
                        return redirect()->route('avaliacao.index');
                    }
                } else {
                    Toastr::error(__('Erro na duplicação da avaliação, porque não existe um tipo de avaliação associada a esta avaliação!'), __('toastr.error'));
                    return redirect()->route('avaliacao.index');
                }
            } else {
                Toastr::error(__('Erro na duplicação da avaliação, a avaliação que pretende duplicar já existe para este ano lectivo!'), __('toastr.error'));
                return redirect()->route('avaliacao.index');
            }
            // }

        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);

            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function avaliacaoOpen(Request $request)
    {
        if (!isset($request->avaliacao))  return 0;
        try {
            $avaliacao = Avaliacao::find($request->avaliacao);
            if ($avaliacao->lock == 1) {
                $avaliacao->update(["lock" => 0]);
                return 1;
            } else {
                $avaliacao->update(["lock" => 1]);
            }
            return 0;
        } catch (Exception $e) {
            dd($e);
            return 0;
        }
    }

    public function ajax($anoLectivo)
    {

        try {
            $calendario_avalicao = Avaliacao::join('users as u1', 'u1.id', '=', 'avaliacaos.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'avaliacaos.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'avaliacaos.deleted_by')
                ->leftJoin('calendario_prova as calend', function ($join) {
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
                ->where('avaliacaos.anoLectivo', $anoLectivo)
                ->whereNull('avaliacaos.deleted_at')
                ->where('ta.anoLectivo', $anoLectivo);

            return DataTables::eloquent($calendario_avalicao)
                ->addColumn('actions', function ($item) {
                    return view('Avaliations::avaliacao.datatables.actions')->with(['item' => $item]);
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
     *
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
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();
            $lectiveYearSelected = $lectiveYearSelected->id ?? 6;


            if ($lectiveYearSelected != $Year) {
                Toastr::error(__('Erro na busca do ano lectivo corrente para criação de uma nova avaliação, por favor tente novamente!'), __('toastr.error'));
                return redirect()->route('avaliacao.index');
            }


            $tipo_avaliacaos = TipoAvaliacao::where('anoLectivo', $lectiveYearSelected)->get();

            return view("Avaliations::avaliacao.create-avaliacao", compact('tipo_avaliacaos'));
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

            //Pegar o ano lectivo na select
            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();

            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
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
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return $id;
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
            $id_user = Auth::user()->id;
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

    public function fetch_metrica($id, $id_anoLectivo)
    {

        $metricas = Metrica::join('tipo_metricas', 'tipo_metricas.id', '=', 'metricas.tipo_metricas_id')
            ->join('avaliacaos', 'avaliacaos.id', '=', 'metricas.avaliacaos_id')
            ->select([
                'metricas.calendario as calendario',
                'metricas.id as metrica_id',
                'metricas.nome as metrica_nome',
                'metricas.percentagem as metrica_percentagem',
                'tipo_metricas.nome as tipo_metricas_nome',
                // 'metricas.data_inicio as data_inici->where('md.exam_only',1)o',
                // 'metricas.data_fim as data_fim',
                'avaliacaos.nome as avalicao_nome'
            ])
            ->where('metricas.avaliacaos_id', $id)
            ->get();

        $model = DB::table('avaliacaos')
            ->join('metricas as metr', 'metr.avaliacaos_id', '=', 'avaliacaos.id')
            // ->select('*')
            ->where('avaliacaos.anoLectivo', $id_anoLectivo)
            ->where('metr.calendario', 1)
            ->get();

        return json_encode(array('data' => $metricas, 'model' => $model, 'avaliacao_id' => $id));
    }

    public function fetch_metricaSemestre($avaliacao_id, $id_semestre)
    {
        $metrica_calendario = DB::table('avaliacaos as av')
            ->join('metricas as mt', 'mt.avaliacaos_id', '=', 'av.id')
            ->leftJoin('calendario_prova as cp', 'cp.id_avaliacao', '=', 'av.id')
            ->leftJoin('calendarie_metrica as cm', function ($join) {
                $join->on('cm.id_metrica', '=', 'mt.id');
            })
            ->leftJoin('tipo_metricas as tp', function ($join) {
                $join->on('mt.tipo_metricas_id', '=', 'tp.id');
            })
            ->select([
                'mt.nome as nome_metrica',
                'mt.id as id_metrica',
                'mt.calendario as calendario',
                'mt.percentagem as percentagem_metrica',
                'av.nome as nome_avaliacao',
                'tp.nome as nome_tipoMetrica',

                'cp.id as id_caledProva',
                'cp.date_start as data_starProva',
                'cp.data_end as data_endProva',

                'cm.id as id_calendMetrica',
                'cm.data_inicio as data_starMetrica',
                'cm.data_fim as data_endMetrica'
            ])
            ->where('cp.id_avaliacao', $avaliacao_id)
            ->where('cp.simestre', $id_semestre)
            ->where('cm.id_periodo_simestre', '=', $id_semestre)
            ->where('cm.deleted_at', null)
            ->where('cp.deleted_by', null)
            ->orderBy('mt.nome', 'ASC')
            ->get();

        $avaliacao_OA = DB::table('tipo_metricas as tipo_mt')
            ->join('metricas as mt', 'mt.tipo_metricas_id', '=', 'tipo_mt.id')
            ->where('mt.calendario', 1)
            ->where('mt.avaliacaos_id', $avaliacao_id)
            ->get();


        $conta = count($metrica_calendario);
        if ($conta > 0) {
            return json_encode(array('model' => $metrica_calendario, 'avaliavao_OA' => $avaliacao_OA));
        } else {
            $calendario = DB::table('avaliacaos as av')
                ->join('calendario_prova as cp', 'cp.id_avaliacao', '=', 'av.id')
                ->where('cp.id_avaliacao', $avaliacao_id)
                ->where('cp.simestre', $id_semestre)
                ->select([
                    'av.nome as nome_avaliacao',
                    'cp.id as id_caledProva',
                    'cp.date_start as data_starProva',
                    'cp.data_end as data_endProva',
                ])
                ->get();

            return json_encode(array('model' => $metrica_calendario, 'data' => $calendario, 'avaliavao_OA' => $avaliacao_OA));
        }
    }

    public function avaliacao_metricaSemestre_calendario($avaliacao_id, $id_semestre)
    {
        $metrica_calendario = DB::table('avaliacaos as av')
            ->join('metricas as mt', 'mt.avaliacaos_id', '=', 'av.id')
            ->leftJoin('calendario_prova as cp', 'cp.id_avaliacao', '=', 'av.id')
            ->leftJoin('calendarie_metrica as cm', function ($join) {
                $join->on('cm.id_metrica', '=', 'mt.id');
            })
            ->leftJoin('tipo_metricas as tp', function ($join) {
                $join->on('mt.tipo_metricas_id', '=', 'tp.id');
            })
            ->select([
                'mt.nome as nome_metrica',
                'mt.id as id_metrica',
                'mt.percentagem as percentagem_metrica',

                'av.nome as nome_avaliacao',
                'tp.nome as nome_tipoMetrica',

                'cp.id as id_caledProva',
                'cp.date_start as data_starProva',
                'cp.data_end as data_endProva',

                'cm.id as id_calendMetrica',
                'cm.data_inicio as data_starMetrica',
                'cm.data_fim as data_endMetrica'
            ])
            ->where('cp.id_avaliacao', $avaliacao_id)
            ->where('cp.simestre', $id_semestre)
            ->where('cm.id_periodo_simestre', $id_semestre)
            ->where('cm.deleted_at', null)
            ->where('cp.deleted_by', null)
            ->orderBy('mt.nome', 'ASC')
            ->get();

        $conta = count($metrica_calendario);
        if ($conta > 0) {
            return json_encode(array('model' => $metrica_calendario));
        } else {
            $calendario = DB::table('avaliacaos as av')
                ->join('calendario_prova as cp', 'cp.id_avaliacao', '=', 'av.id')
                ->where('cp.id_avaliacao', $avaliacao_id)
                ->where('cp.simestre', $id_semestre)
                ->select([
                    'av.nome as nome_avaliacao',
                    'cp.id as id_caledProva',
                    'cp.date_start as data_starProva',
                    'cp.data_end as data_endProva',
                ])
                ->get();

            return json_encode(array('model' => $metrica_calendario, 'data' => $calendario));
        }
    }

    public function fetch_tipo_metrica($anoLectivo)
    {
        $tipo_metricas = TipoMetrica::where('anoLectivo', $anoLectivo)->get();
        return json_encode(array('data' => $tipo_metricas));
    }

    //Função que vai permitir apagar a metrica apartir da view Jquery
    public function delete_metrica($id)
    {
        return "Ola Mundo!";
    }

    public function concluir_avaliacao(Request $request)
    {
        /*
            So permitir concluir uma avaliacao se o total
            das percentagens para aquela avaliacao for igual a 100
            fazer update no campo lock para true
            Depois de concluir a avaliacao não ser possivel
            apagar a mesma
        */
        $avaliacao_id = $request->get("idAvaliacao");


        $soma_percentagem = Metrica::join('avaliacaos', 'avaliacaos.id', '=', 'metricas.avaliacaos_id')
            ->select('metricas.percentagem', DB::raw('SUM(percentagem) as total_percentagem'))
            //->groupBy('metricas.percentagem')
            ->where('metricas.avaliacaos_id', $avaliacao_id)
            ->get();

        //Avaliar se a soma da percentagem for diferente de vazio
        if (!$soma_percentagem->isEmpty()) {
            foreach ($soma_percentagem as $item) {
                if ($item->total_percentagem == 100) {
                    $avaliacao = Avaliacao::find($avaliacao_id);
                    $avaliacao->lock = true;
                    $avaliacao->save();

                    // Success message
                    Toastr::success(__('Avaliação fechada com sucesso'), __('toastr.success'));
                    return redirect()->route('avaliacao.index');
                } else {
                    // Error message
                    Toastr::error(__('Métricas não estão corretas'), __('toastr.error'));
                    return redirect()->route('avaliacao.index');
                }
            }
            //return $soma_percentagem->pluck('total_percentagem');
        } else {
            // Error message
            Toastr::error(__('Sem métricas registadas'), __('toastr.error'));
            return redirect()->route('avaliacao.index');
        }
    }

    public function fetch_single_avaliacao($id)
    {
        $avaliacao = Avaliacao::select('avaliacaos.nome as avaliacao_nome')
            ->where('avaliacaos.id', $id)
            ->get();
        return json_encode(array('data' => $avaliacao));
    }

    public function atualizar_avaliacao(Request $request)
    {
        $avaliacao = Avaliacao::find($request->get('avaliacao_id'));
        $avaliacao->nome = $request->get('nome_avaliacao');
        $avaliacao->tipo_avaliacaos_id = $request->get('tipo_avaliacao');
        $avaliacao->save();

        Toastr::success(__('Avaliação editada com sucesso'), __('toastr.success'));
        return redirect()->route('avaliacao.index');
    }
    public function cadastroProva_Avariocao($id)
    {
        try {

            $PERIODO = DB::table('discipline_periods as period')
                ->leftJoin('discipline_period_translations as dt', function ($join) {
                    $join->on('dt.discipline_periods_id', '=', 'period.id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                })
                ->select(['dt.display_name', 'period.id'])
                ->get();

            $nomeAvaliacao = Avaliacao::find($id);
            $data = [
                'periodo' => $PERIODO,
                'action' => 'create',
                'nomeAvaliacao' => $nomeAvaliacao->nome,
                'id_Avaliacacao' => $nomeAvaliacao->id,
                'menu_activo' => $_GET['menu_avalicao'],
                'dadosAvaliacao' => $nomeAvaliacao
            ];
          
            return view('Avaliations::calendario-prova.calendario')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }
    public function editarMetrica_calendarizada($id_avaliacao, $id_metrica, $semestre)
    {

        if ($id_metrica == 0) {
            $avaliacao_OA = DB::table('tipo_metricas as tipo_mt')
                ->join('metricas as mt', 'mt.tipo_metricas_id', '=', 'tipo_mt.id')
                ->where('mt.calendario', 1)
                ->where('mt.avaliacaos_id', $id_avaliacao)
                ->get();
            return json_encode(array('data_oa' => $avaliacao_OA, 'data' => 0));
        } else {
            $metrica_calendario = DB::table('metricas as mt')
                ->join('calendarie_metrica as cm', 'cm.id_metrica', '=', 'mt.id')
                ->select([
                    'mt.nome as nome_metrica',
                    'mt.id as id_metrica',
                    'mt.percentagem as percentagem_metrica',
                    'cm.id as id_calendMetrica',
                    'cm.data_inicio as data_starMetrica',
                    'cm.data_fim as data_endMetrica'
                ])

                ->where('cm.id_periodo_simestre', $semestre)
                ->where('cm.id', $id_metrica)
                ->where('cm.deleted_at', null)
                ->where('cm.deleted_by', null)
                ->get();

            return json_encode(array('data_oa' => 0, 'data' => $metrica_calendario));
        }
    }

    public function get_calendMetrica_segundaChamada($id_metrica, $semestre)
    {

        $metrica_calendario = DB::table('metricas as mt')
            ->join('calendarie_metrica as cm', 'cm.id_metrica', '=', 'mt.id')
            ->select([
                'mt.nome as nome_metrica',
                'mt.id as id_metrica',
                'mt.percentagem as percentagem_metrica',
                'cm.id as id_calendMetrica',
                'cm.data_inicio as data_starMetrica',
                'cm.data_fim as data_endMetrica'
            ])

            ->where('cm.id_periodo_simestre', $semestre)
            ->where('cm.id', $id_metrica)
            ->where('cm.deleted_at', null)
            ->where('cm.deleted_by', null)
            ->first();

        if (isset($metrica_calendario)) {
            $model = DB::table('calendarie_metrica_segunda_chamada as sc')
                ->where('sc.id_calendarie_metrica', $metrica_calendario->id_calendMetrica)
                ->first();
        }

        return json_encode(array('metrica' => $metrica_calendario, 'model' => $model));
    }





    //PAINEL AVALIAÇÃO
    public function showPainelAvaliation()
    {
        try {

            // VERIFICA SE A VIEW EXISTE CASO NÃO VOLTA PARA O INÍCIO
            if (view()->exists('Avaliations::avaliacao.show-panel-avaliation')) {
                // return "uis"; 
                $lectiveYears = LectiveYear::with(['currentTranslation'])->get();

                $currentData = Carbon::now();
                $lectiveYearSelected = DB::table('lective_years')
                    ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                    ->first();

                $lectiveYearSelected = $lectiveYearSelected->id ?? 11;

                $data = [
                    'lectiveYearSelected' => $lectiveYearSelected,
                    'lectiveYears' => $lectiveYears
                ];

                return view('Avaliations::avaliacao.show-panel-avaliation')->with($data);
            } else {
                return redirect('/');
            }
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    // PREENCHE A TABELA DE AVALIAÇÕES
    public function showPainelAvaliationTabela($lective_year)
    {

        try {
            set_time_limit(300);
            // return $lective_year;  

            $coordinator_course = DB::table('coordinator_course  as coordenador_curso')
                ->where('coordenador_curso.user_id', Auth::user()->id)
                ->get();

            $professor = DB::table('users  as professor')
                ->join('user_classes as turma', 'professor.id', '=', 'turma.user_id')
                ->join('user_disciplines  as disciplina', 'professor.id', '=', 'disciplina.users_id')
                ->where('professor.id', Auth::user()->id)
                ->select(['professor.id as id_prof', 'turma.class_id as turma', 'disciplina.disciplines_id as disciplina'])
                ->get();

            $plano_avaliacao = DB::table('plano_estudo_avaliacaos  as pea')
                ->join('avaliacaos  as avaliacao', 'avaliacao.id', '=', 'pea.avaliacaos_id')
                ->whereNull('pea.deleted_by')
                ->select(['pea.study_plan_editions_id as codigo_plano', 'pea.disciplines_id as codigo_disciplina', 'avaliacao.nome as avaliacao'])
                ->get();


            if (!$coordinator_course->isEmpty()) {

                $allDiscipline = StudyPlanEdition::join('study_plans as stp', 'stp.id', '=', 'study_plan_editions.study_plans_id')
                    ->join('study_plans_has_disciplines as stp_discipline', 'stp_discipline.study_plans_id', '=', 'stp.id')
                    ->join('courses as curso', 'curso.id', '=', 'stp.courses_id')
                    ->whereIn('curso.id', $coordinator_course->pluck('courses_id')->toArray())
                    ->join('study_plan_edition_disciplines as stdp_disci', 'stdp_disci.study_plan_edition_id', '=', 'study_plan_editions.id')
                    ->join('disciplines as disciplina', 'disciplina.id', '=', 'stdp_disci.discipline_id')
                    ->join('classes as turma', function ($join) {
                        $join->on('turma.courses_id', '=', 'curso.id');
                        $join->on('turma.year', '=', 'study_plan_editions.course_year');
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

                    ->select(['study_plan_editions.lective_years_id as anoLectivo', 'study_plan_editions.course_year as ano_curricular', 'curso.id as curso_id', 'ct.display_name as nome_curso', 'stdp_disci.discipline_id as id_disciplina_no_plano', 'dt.display_name as nome_disciplina', 'turma.display_name as nome_turma', 'turma.courses_id as turma_id_curso', 'stp.courses_id as id_curso_plano', 'turma.year as TurmaAno_1', 'turma.id as id_turma', 'disciplina.code as codigo_disciplina'])
                    ->where('study_plan_editions.lective_years_id', $lective_year)


                    ->where('turma.lective_year_id', $lective_year)

                    ->whereNull('turma.deleted_at')
                    ->whereNull('stp.deleted_at')
                    ->whereNull('curso.deleted_at')
                    ->distinct('dt.display_name')
                    // ->limit(400)
                    ->get();
            } else if (!$professor->isEmpty()) {

                $allDiscipline = StudyPlanEdition::join('study_plans as stp', 'stp.id', '=', 'study_plan_editions.study_plans_id')
                    ->join('study_plans_has_disciplines as stp_discipline', 'stp_discipline.study_plans_id', '=', 'stp.id')
                    ->join('courses as curso', 'curso.id', '=', 'stp.courses_id')
                    // ->when(!$coordinator_course->isEmpty(), function ($query) use ($coordinator_course){
                    //     return  $query->whereIn('curso.id', $coordinator_course->pluck('courses_id')->toArray());
                    // })
                    ->join('study_plan_edition_disciplines as stdp_disci', 'stdp_disci.study_plan_edition_id', '=', 'study_plan_editions.id')
                    ->join('disciplines as disciplina', 'disciplina.id', '=', 'stdp_disci.discipline_id')
                    ->join('classes as turma', function ($join) {
                        $join->on('turma.courses_id', '=', 'curso.id');
                        $join->on('turma.year', '=', 'study_plan_editions.course_year');
                    })
                    ->whereIn('disciplina.id', $professor->pluck('disciplina')->toArray())

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

                    ->select(['study_plan_editions.lective_years_id as anoLectivo', 'study_plan_editions.course_year as ano_curricular', 'curso.id as curso_id', 'ct.display_name as nome_curso', 'stdp_disci.discipline_id as id_disciplina_no_plano', 'dt.display_name as nome_disciplina', 'turma.display_name as nome_turma', 'turma.courses_id as turma_id_curso', 'stp.courses_id as id_curso_plano', 'turma.year as TurmaAno_1', 'turma.id as id_turma', 'disciplina.code as codigo_disciplina'])
                    ->where('study_plan_editions.lective_years_id', $lective_year)


                    ->where('turma.lective_year_id', $lective_year)

                    ->whereNull('turma.deleted_at')
                    ->whereNull('stp.deleted_at')
                    ->whereNull('curso.deleted_at')
                    ->distinct('dt.display_name')
                    // ->limit(400)
                    ->get();
            } else {



                $allDiscipline = StudyPlanEdition::join('study_plans as stp', 'stp.id', '=', 'study_plan_editions.study_plans_id')
                    ->join('study_plans_has_disciplines as stp_discipline', 'stp_discipline.study_plans_id', '=', 'stp.id')
                    ->join('courses as curso', 'curso.id', '=', 'stp.courses_id')

                    ->join('study_plan_edition_disciplines as stdp_disci', 'stdp_disci.study_plan_edition_id', '=', 'study_plan_editions.id')
                    ->join('disciplines as disciplina', 'disciplina.id', '=', 'stdp_disci.discipline_id')
                    ->join('classes as turma', function ($join) {
                        $join->on('turma.courses_id', '=', 'curso.id');
                        $join->on('turma.year', '=', 'study_plan_editions.course_year');
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

                    ->select(['study_plan_editions.lective_years_id as anoLectivo', 'study_plan_editions.course_year as ano_curricular', 'curso.id as curso_id', 'ct.display_name as nome_curso', 'stdp_disci.discipline_id as id_disciplina_no_plano', 'dt.display_name as nome_disciplina', 'turma.display_name as nome_turma', 'turma.courses_id as turma_id_curso', 'stp.courses_id as id_curso_plano', 'turma.year as TurmaAno_1', 'turma.id as id_turma', 'disciplina.code as codigo_disciplina'])
                    ->where('study_plan_editions.lective_years_id', $lective_year)


                    ->where('turma.lective_year_id', $lective_year)

                    ->whereNull('turma.deleted_at')
                    ->whereNull('stp.deleted_at')
                    ->whereNull('curso.deleted_at')
                    ->distinct('dt.display_name')
                    // ->limit(400)
                    ->get();
            }






           $dados = $this->dados_pauta($lective_year);



            $dados_mac = DB::table('lancar_pauta as publicar')
                ->where('publicar.active', 1)
                ->join('lective_years as lective_year', 'lective_year.id', '=', 'publicar.id_ano_lectivo')
                ->join('lective_year_translations as lective_year_translation', 'lective_year_translation.lective_years_id', '=', 'lective_year.id')
                ->join('classes', 'classes.id', '=', 'publicar.id_turma')
                ->join('disciplines_translations', 'disciplines_translations.discipline_id', '=', 'publicar.id_disciplina')
                ->join('users as user', 'user.id', '=', 'publicar.id_user_launched')
                ->join('users as user1', 'user1.id', '=', 'publicar.updated_by')
                ->join('plano_estudo_avaliacaos  as pea', 'pea.disciplines_id', 'publicar.id_disciplina')
                ->join('avaliacaos as avl', 'avl.id', 'pea.avaliacaos_id')
                ->join('metricas', function ($join) {
                    $join->on('metricas.code_dev', 'publicar.pauta_tipo');
                    $join->on('metricas.avaliacaos_id', 'avl.id');
                })
                ->join('study_plan_editions as spe', 'spe.id', 'pea.study_plan_editions_id')

                ->select([
                    'publicar.id as id',
                    'publicar.estado as pauta_estado',
                    'publicar.pauta_tipo as pauta_tipo',
                    'publicar.tipo',
                    'publicar.created_at as data_publicacao',
                    'publicar.updated_at as data_atualizacao',
                    'user.name as nome_usuario',
                    'user1.name as atualizacao_usuario',
                    'lective_year_translation.description as ano_lectivo',
                    'classes.display_name as nome_turma',
                    'classes.id as id_turma',
                    'disciplines_translations.display_name as nome_disciplina',
                    'disciplines_translations.discipline_id as id_disciplina',
                    'avl.id as avl_id',
                    'metricas.id as mt_id',
                    'spe.id as spe_id',
                    'avl.anoLectivo as lective_year',
                    'publicar.segunda_chamada as segunda_chamada',
                    'publicar.path as path'
                ])
                ->groupBy('publicar.id')
                ->orderBy('publicar.updated_at', 'DESC')
                ->distinct('publicar.pauta_tipo')
                ->where('publicar.id_ano_lectivo', $lective_year)
                ->where('avl.anoLectivo', $lective_year)

                ->where('spe.lective_years_id', $lective_year)
                ->get();



            $dados_mac_1c = $dados_mac->where('segunda_chamada', null);

            $dados_mac_2c = $dados_mac->where('segunda_chamada', 1);

            $d = Datatables::of($allDiscipline)

                ->addColumn('pf1', function ($allDiscipline) use ($dados_mac_1c, $coordinator_course, $professor) {
                    return view('Avaliations::avaliacao.datatables.pf1', compact('dados_mac_1c', 'allDiscipline', 'coordinator_course', 'professor'));
                })
                ->addColumn('pf2', function ($allDiscipline) use ($dados_mac_1c, $coordinator_course, $professor) {
                    return view('Avaliations::avaliacao.datatables.pf2', compact('dados_mac_1c', 'allDiscipline', 'coordinator_course', 'professor'));
                })
                ->addColumn('pf1_2c', function ($allDiscipline) use ($dados_mac_2c, $coordinator_course, $professor) {
                    return view('Avaliations::avaliacao.datatables.pf1_2c', compact('dados_mac_2c', 'allDiscipline', 'coordinator_course', 'professor'));
                })
                ->addColumn('pf2_2c', function ($allDiscipline) use ($dados_mac_2c, $coordinator_course, $professor) {
                    return view('Avaliations::avaliacao.datatables.pf2_2c', compact('dados_mac_2c', 'allDiscipline', 'coordinator_course', 'professor'));
                })
                ->addColumn('oa', function ($allDiscipline) use ($dados_mac_1c, $coordinator_course, $professor) {
                    return view('Avaliations::avaliacao.datatables.oa', compact('dados_mac_1c', 'allDiscipline', 'coordinator_course', 'professor'));
                })
                ->addColumn('mac', function ($allDiscipline) use ($dados, $coordinator_course, $professor) {
                    return view('Avaliations::avaliacao.datatables.mac', compact('dados', 'allDiscipline', 'coordinator_course', 'professor'));
                })
               
                ->addColumn('exame_escrito', function ($allDiscipline) use ($dados_mac_1c, $coordinator_course, $professor) {
                    return view('Avaliations::avaliacao.datatables.exame_escrito_new', compact('dados_mac_1c', 'allDiscipline', 'coordinator_course', 'professor'));
                })
                ->addColumn('exame_oral', function ($allDiscipline) use ($dados, $coordinator_course, $professor) {
                    return view('Avaliations::avaliacao.datatables.exame_oral', compact('dados', 'allDiscipline', 'coordinator_course', 'professor'));
                })
                ->addColumn('cf', function ($allDiscipline) use ($dados, $coordinator_course, $professor) {
                    return view('Avaliations::avaliacao.datatables.final', compact('dados', 'allDiscipline', 'coordinator_course', 'professor'));
                })
                ->addColumn('recurso', function ($allDiscipline) use ($dados, $coordinator_course, $professor) {
                    return view('Avaliations::avaliacao.datatables.recurso', compact('dados', 'allDiscipline', 'coordinator_course', 'professor'));
                })
                ->addColumn('exame_especial', function ($allDiscipline) use ($dados, $coordinator_course, $professor) {
                    return view('Avaliations::avaliacao.datatables.exame_especial', compact('dados', 'allDiscipline', 'coordinator_course', 'professor'));
                })
                ->addColumn('exame_extraordinario', function ($allDiscipline) use ($dados, $coordinator_course, $professor) {
                    return view('Avaliations::avaliacao.datatables.exame_extraordinario', compact('dados', 'allDiscipline', 'coordinator_course', 'professor'));
                })
                ->addColumn('seminario', function ($allDiscipline) use ($dados, $coordinator_course, $professor) {
                    return view('Avaliations::avaliacao.datatables.seminario', compact('dados', 'allDiscipline', 'coordinator_course', 'professor'));
                })
                ->addColumn('tfc', function ($allDiscipline) use ($dados, $coordinator_course, $professor) {
                    return view('Avaliations::avaliacao.datatables.tfc', compact('dados', 'allDiscipline', 'coordinator_course', 'professor'));
                })
                  ->addColumn('pauta_geral', function ($allDiscipline) use ($dados,$coordinator_course,$professor){
                    return view('Avaliations::avaliacao.datatables.pauta_geral',compact('dados','allDiscipline','coordinator_course','professor'));
                })
                ->rawColumns(['pf1', 'pf2', 'pf1_2c', 'pf2_2c', 'oa', 'mac', 'exame_escrito', 'exame_oral', 'cf', 'recurso', 'exame_especial', 'exame_extraordinario', 'seminario', 'tfc','pauta_geral'])
                ->addIndexColumn();


            return $d->make(true);
        } catch (Exception | Throwable $e) {
            dd($e);
            return response()->json($e);
            logError($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    private function dados_pauta($lective_year = null, $pauta_id = null){

        $dados = DB::table('pauta_path as pauta')->when(isset($pauta_id),function($q)use($pauta_id){
            $q->where('pauta.id_publicar_pauta',$pauta_id);
        })
        ->join('publicar_pauta as publicar', 'publicar.id', '=', 'pauta.id_publicar_pauta')
        ->join('lective_years as lective_year', 'lective_year.id', '=', 'publicar.id_ano_lectivo')
        ->join('lective_year_translations as lective_year_translation', 'lective_year_translation.lective_years_id', '=', 'lective_year.id')
        ->join('classes', 'classes.id', '=', 'publicar.id_turma')
        ->join('courses_translations as ct', 'ct.courses_id', 'classes.courses_id')
        ->join('disciplines', 'disciplines.id', 'publicar.id_disciplina')
        ->join('disciplines_translations', 'disciplines_translations.discipline_id', '=', 'disciplines.id')

        ->join('users as user', 'user.id', '=', 'publicar.id_user_publish')
        ->join('users as user1', 'user1.id', '=', 'publicar.updated_by')
        ->select([
            'pauta.path as pauta_link',
            'pauta.last as pauta_last',
            'publicar.estado as pauta_estado',
            'publicar.pauta_tipo as pauta_tipo',
            'publicar.tipo',
            'publicar.id as pauta_id',
            'pauta.created_at as data_publicacao',
            'pauta.updated_at as data_atualizacao',
            'pauta.path',
            'user.name as nome_usuario',
            'user1.name as atualizacao_usuario',
            'lective_year_translation.description as ano_lectivo',
            'classes.display_name as nome_turma',
            'classes.id as id_turma',
            'disciplines_translations.display_name as nome_disciplina',
            'disciplines_translations.discipline_id as id_disciplina',
            'disciplines.code as codigo_disciplina',
            'ct.display_name as nome_curso',
            'ct.courses_id as id_curso',
            'classes.year as ano',
            'lective_year.id as lective_year_id',
        ])
        ->groupBy('pauta.id')
        ->orderBy('pauta.updated_at', 'DESC')
        ->distinct('publicar.pauta_tipo')
        ->when(isset($lective_year),function($q)use($lective_year){
            $q->where('publicar.id_ano_lectivo', $lective_year);
        })
        ->where('pauta.last', "=", 1)
        ->where('publicar.estado', "=", 1)
        ->get();

        return $dados;
    }


    // SHOW GRADES AVALIATION STUDENT CONFIGURATION
    public function pautaAvaliationStudentConfig()
    {

        try {

            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();
            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();
            $lectiveYearSelected = $lectiveYearSelected->id ?? 6;

            return view("Avaliations::avaliacao.pauta_avaliation_student_config", compact('lectiveYears', 'lectiveYearSelected'));
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function pautaAvaliationStudentConfigAjax($lective_year)
    {
        try {

            $model = PautaAvaliationStudentShow::leftJoin('lective_years', 'lective_years.id', '=', 'pauta_avaliation_student_shows.lective_year_id')
                ->leftJoin('lective_year_translations as lyt', function ($join) {
                    $join->on('lyt.lective_years_id', '=', 'lective_years.id');
                    $join->on('lyt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('lyt.active', '=', DB::raw(true));
                })
                ->leftJoin('users as full_name', function ($join) {
                    $join->on('full_name.id', '=', 'pauta_avaliation_student_shows.created_by');
                    // ->where('full_name.parameters_id', 1);
                })
                // query()
                ->select([
                    // 'pauta_avaliation_student_shows.*',
                    'pauta_avaliation_student_shows.id',
                    'pauta_avaliation_student_shows.quantidade_mes',
                    'pauta_avaliation_student_shows.quatidade_day',
                    'lyt.display_name as lective_year_id',
                    'full_name.name as created_by',
                    'pauta_avaliation_student_shows.updated_by',
                    'pauta_avaliation_student_shows.deleted_by',
                ])
                ->where('pauta_avaliation_student_shows.lective_year_id', $lective_year);

            return DataTables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('Avaliations::avaliacao.datatables.actions_pauta_config')->with('item', $item);
                })
                ->rawColumns(['actions'])
                ->addIndexColumn()
                ->toJson();
        } catch (Exception | Throwable $e) {
            logError($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function pautaAvaliationStudentConfigCreate()
    {
        try {

            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();
            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();
            $lectiveYearSelected = $lectiveYearSelected->id ?? 6;

            return view("Avaliations::avaliacao.pauta_avaliation_student_config_create", compact('lectiveYears', 'lectiveYearSelected'));
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function pautaAvaliationStudentConfigStore(Request $request)
    {
        $findAno = LectiveYear::select('lective_years.id')
            ->where('id', $request->lective_year_id)
            ->get();
        $getAno = PautaAvaliationStudentShow::select('pauta_avaliation_student_shows.lective_year_id')
            // ->where('lective_year_id', $request->get('lective_year_id'))
            ->where('lective_year_id', $request->lective_year_id)
            ->get();

        if ($getAno->isEmpty()) {
            if (!$findAno->isEmpty()) {

                $saveDATA = PautaAvaliationStudentShow::create([
                    'quantidade_mes' => $request->get('quantidade_mes'),
                    'quatidade_day' => $request->quatidade_day,
                    'lective_year_id' => $request->lective_year_id,
                    'created_by' => Auth::user()->id
                ]);
                $saveDATA->save();

                // Success message
                Toastr::success(__('Registo adicionado com sucesso'), __('toastr.success'));
                return redirect()->route('pauta_student_config.create');
            } else {
                // Error message
                Toastr::error(__('Já existe um critério'), __('toastr.error'));
                return redirect()->route('pauta_student_config.create');
            }
        } else {
            // Error message
            Toastr::error(__('Já existe um critério com este ano alectivo'), __('toastr.error'));
            return redirect()->route('pauta_student_config.create');
        }
    }


    public function pautaAvaliationStudentConfigEdit($id)
    {
        $getAno = PautaAvaliationStudentShow::find($id);

        $lectiveYears = LectiveYear::with(['currentTranslation'])
            ->get();
        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
            // ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
            ->where('id', $getAno->lective_year_id)
            ->first();
        $lectiveYearSelected = $lectiveYearSelected->id ?? 6;

        return view("Avaliations::avaliacao.pauta_avaliation_student_config_edit", compact('getAno', 'lectiveYears', 'lectiveYearSelected'));
    }

    public function pautaAvaliationStudentConfigUpdate(Request $request)
    {
        try {
            $getAno = PautaAvaliationStudentShow::find($request->id_criterio);

            $getAno->quantidade_mes = $request->get('quantidade_mes');
            $getAno->quatidade_day = $request->quatidade_day;
            $getAno->lective_year_id = $request->lective_year_id;
            $getAno->updated_by  = Auth::user()->id;
            $getAno->save();

            Toastr::success(__('Registo editado com sucesso'), __('toastr.success'));
            return redirect()->route('pauta_student.config');
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function pautaAvaliationStudentConfigDestroy($id)
    {
        try {
            $getAno = PautaAvaliationStudentShow::find($id);
            $getAno->delete();

            Toastr::success(__('Registo excluido com sucesso'), __('toastr.success'));
            return redirect()->route('pauta_student.config');
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function generalPautePDF($pauta_id)
    {
        try {
           
            $pauta = $this->dados_pauta(null,$pauta_id)
                            ->first();

            $regime = substr($pauta->codigo_disciplina, -3, 1);
            $regimeFinal = "";
            if ($regime == "1" || $regime == "2") {
                $regimeFinal = $regime . 'º ' . "Semestre";
            } else if ($regime == "A") {
                $regimeFinal = "Anual";
            }
            //dados da instituição
            $institution = Institution::latest()->first();
            $config = DB::table('avalicao_config')
                ->where('lective_year', $pauta->lective_year_id)
                ->first();

            //Logotipo
            $Logotipo_instituicao = "https://" . $_SERVER['HTTP_HOST'] . "/storage/attachment/" . $institution->logotipo;
            // $titulo_documento = "Pauta de";
            // $documentoGerado_documento = "Documento gerado a";
            $documentoCode_documento = 101;

            $av = new AvaliacaoAlunoControllerNew();
            $students = $av->students_matriculado($pauta->id_disciplina, $pauta->lective_year_id)
                ->where('mc.class_id', $pauta->id_turma)
                ->get();

            $students = $students->each(function ($student) use ($pauta) {
                $percurso = BoletimNotas_Student($pauta->lective_year_id, $student->id_curso, $student->id_mat, $pauta->id_disciplina, $pauta->id_turma);
                $percurso =  $percurso->map(function ($grupo) {

                    return $grupo->reject(function ($avl) use ($grupo) {
                        $faltou =  isset($avl->presence);
                        $nota_normal = !isset($avl->segunda_chamada);

                        $fez_segunda_chamada = $grupo->where('user_id', $avl->user_id)
                            ->where('Disciplia_id', $avl->Disciplia_id)
                            ->where('Avaliacao_aluno_Metrica', $avl->Avaliacao_aluno_Metrica)
                            ->where('Avaliacao_aluno_turma', $avl->Avaliacao_aluno_turma)
                            ->where('segunda_chamada', 1)
                            ->isNotEmpty();




                        $sai =  $faltou && $nota_normal && $fez_segunda_chamada;


                        return $sai;
                    });
                });
                $student->percurso = $percurso;
            });

            //pegar os utilizadores que lançaram as notas 
            $teacher = DB::table('teacher_classes as tc')
                ->whereNotIn('tc.user_id', [23, 24,734])
                ->join('classes', function ($join) use ($pauta) {
                    $join->on('classes.id', 'tc.class_id');
                    $join->where('classes.id', $pauta->id_turma);
                })
                ->join('user_disciplines as ud', function ($q) use ($pauta) {
                    $q->on('ud.users_id', 'tc.user_id')
                        ->where('ud.disciplines_id', $pauta->id_disciplina);
                })
                ->leftJoin('user_parameters as u_p9', function ($q) {
                    $q->on('tc.user_id', '=', 'u_p9.users_id')
                        ->where('u_p9.parameters_id', 1);
                })
                ->select(['u_p9.value as fullname'])
                ->first();

                $coordenador = DB::table('coordinator_course')
                ->where('coordinator_course.courses_id', $pauta->id_curso)
                ->whereNotIn('coordinator_course.user_id', [23, 24,734])
                ->leftJoin('user_parameters as u_p9', function ($q) {
                    $q->on('coordinator_course.user_id', '=', 'u_p9.users_id')
                        ->where('u_p9.parameters_id', 1);
                })
                ->select(['u_p9.value as fullname'])
                ->first();


            $data = [
                'turma' => $pauta->nome_turma,
                'disciplina' => $pauta->codigo_disciplina . ' - ' . $pauta->nome_disciplina,
                'curso' => $pauta->nome_curso,
                'ano' => $pauta->ano,
                'ano_lectivo' => $pauta->ano_lectivo,
                'regime' => $regimeFinal,
                'institution' => $institution,
                'logotipo' => $Logotipo_instituicao,
                'students' => $students,
                'config' => $config,
                'pauta' => $pauta,
                'teacher' => $teacher,
                'coordenador' => $coordenador
            ];

            $parts = explode('/', $pauta->ano_lectivo);
            $fileName = 'Pauta-' . Carbon::now()->format('h:i:s') . '-' . $pauta->codigo_disciplina . '-' . $pauta->nome_turma . '_' . $parts[0] . '.pdf';

            $pdf = PDF::loadView("Avaliations::avaliacao-aluno.pauta_grades.pdf.pautaGeral", $data);
            $pdf->setOption('margin-top', '2mm');
            $pdf->setOption('margin-left', '2mm');
            $pdf->setOption('margin-bottom', '13mm');
            $pdf->setOption('margin-right', '2mm');
            $pdf->setOption('enable-javascript', true);
            $pdf->setOption('debug-javascript', true);
            $pdf->setOption('javascript-delay', 1000);
            $pdf->setOption('enable-smart-shrinking', true);
            $pdf->setOption('no-stop-slow-scripts', true);

            $pdf->setPaper('a4');
            $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
            $pdf->setOption('footer-html', $footer_html);

            return $pdf->stream($fileName . '.pdf');
        } catch (Exception | Throwable $e) {
            return $e;
            // logError($e);
            // return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
}
