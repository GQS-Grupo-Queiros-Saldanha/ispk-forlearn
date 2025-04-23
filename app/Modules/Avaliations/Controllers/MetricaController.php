<?php

namespace App\Modules\Avaliations\Controllers;

use App\Helpers\LanguageHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Avaliations\Models\Avaliacao;
use App\Modules\Avaliations\Models\AvaliacaoAluno;
use App\Modules\Avaliations\Models\Metrica;
use App\Modules\Avaliations\Models\TipoAvaliacao;
use Toastr;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Log;
class MetricaController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return view("Avaliations::tipo-avaliacao.tipo-avaliacao");
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
    public function ajax()
    {
        try {
            $model = TipoAvaliacao::join('users as u1', 'u1.id', '=', 'tipo_avaliacaos.created_by')
                    ->leftJoin('users as u2', 'u2.id', '=', 'tipo_avaliacaos.updated_by')
                    ->leftJoin('users as u3', 'u3.id', '=', 'tipo_avaliacaos.deleted_by')
                    ->select([
                        'tipo_avaliacaos.nome as tipo_avaliacao_nome',
                        'u1.name as created_by',
                        'u2.name as updated_by',
                        'u3.name as deleted_by',
                        //'u0.name as student',
                    ]);

            return DataTables::eloquent($model)
                    ->addColumn('actions', function ($item) {
                        return view('Avaliations::tipo-avaliacao.datatables.actions')->with('item', $item);
                    })
                /*  ->editColumn('created_at', function ($item) {
                        return TimeHelper::time_elapsed_string($item->created_at);
                    })
                    ->editColumn('updated_at', function ($item) {
                        return TimeHelper::time_elapsed_string($item->updated_at);
                    })
                    ->editColumn('deleted_at', function ($item) {
                        return TimeHelper::time_elapsed_string($item->deleted_at);
                    })*/
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
            return view("Avaliations::tipo-avaliacao.create-tipo-avaliacao");
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Metodo que permite inserir uma nova metrica e associar a uma
     * avaliacao.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        try {
            
                /*
                Antes de salvar fazer validação para ver
                se a as somas das percentagens das metricas com
                a mesma avaliacao (id) é inferior a 100 - salvar
                Avaliar tambem se a percentagem adicionada passa dos 100
                adicionada a soma com as outras - Não Salvar
                */

                $avaliacao_id = $request->get('avaliacao_id');
                $soma_percentagem = DB::table('avaliacaos')
                ->join('metricas', 'metricas.avaliacaos_id', '=', 'avaliacaos.id')
                ->select('metricas.percentagem', DB::raw('SUM(percentagem) as total_percentagem'))
                ->where('metricas.avaliacaos_id', $avaliacao_id)
                ->where('metricas.deleted_at',null)
                ->where('metricas.deleted_by',null)
                // ->groupBy('metricas.percentagem')
                ->get();
                
                //criar variavel de Percentagem a ser inserida
                $percentagem = $request->get('percentagem');
                
                
                //NAO PERMETIR ASSOCIAR O MESMO NOME DE METRICA PRA MESMA AVALIACAO
                $pesq_metrica = Metrica::select('metricas.id')
                ->where('metricas.nome', $request->get('nome_metrica'))
                ->where('metricas.avaliacaos_id', $avaliacao_id)
                ->get();


                //VERIFICAR SE RETORNA DADOS (METRICA COM ESSE NOME) SE RETORNAR, EXIBIR ERRO SENAO, SEGUIR
                if ($pesq_metrica->isEmpty()) {

                //verificar se a collection esta vazia permitir criar a primeira vez apenas.
                    if ($soma_percentagem->isEmpty()) {
                        $metrica = new Metrica;
                        $metrica->nome = $request->get('nome_metrica');
                        $metrica->percentagem = $request->get('percentagem');
                        $metrica->created_by = Auth::user()->id;
                        $metrica->avaliacaos_id = $request->get('avaliacao_id');
                        $metrica->tipo_metricas_id = $request->get('tipo_avaliacao');
                        $metrica->calendario = $request->get('outrasAvaliacao'); 
                        $metrica->save();
                        return response()->json(['success'=>'Métrica associada  com sucesso']);        
                        
                    } else {
                        //Percorrer a collection para obter o total da percentagem em integer
                        foreach ($soma_percentagem as $value) {
                           $total_persentagem=$value->total_percentagem;
                        }
                            //avaliar se a percentagem a ser inserida está entre 1 a 100
                            if ($percentagem > 0 && $percentagem <= 100) {
                                //Avaliar se o total das percentagens + a percentagem a ser inserida passa de 100
                               $persentagem_total=$total_persentagem + $percentagem;
                               intval($persentagem_total);
                                if ($persentagem_total<=100){
                                    $metrica = new Metrica;
                                    $metrica->nome = $request->get('nome_metrica');
                                    $metrica->percentagem = $request->get('percentagem');
                                    $metrica->created_by = Auth::user()->id;
                                    $metrica->avaliacaos_id = $request->get('avaliacao_id');
                                    $metrica->tipo_metricas_id = $request->get('tipo_avaliacao');
                                    $metrica->calendario = $request->get('outrasAvaliacao'); 
                                    $metrica->save();
                                    return response()->json(['success'=>'Métrica associada com sucesso!']);
                                }
                                else {
                                    return response()->json(['error'=>'Erro ao inserir métrica! percentagem muito auta']);
                                }
                            }
                        
                    }
                } else {
                    return response()->json(['error'=>'Já existe métrica com esse nome!']);
                }

            //Somar o total de percentages ja existentes + a percentagem a ser inserida
            //return $soma_percentagem->pluck('metricas.percentagem') + $percentagem;
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $id;
    }

    public function fetch()
    {
        return "Ola Mundo!";
    }

    //Metodo para associar uma avaliacao a uma metrica
    public function associar(Request $request)
    {
        try {
            $id_avaliacao = $request->get('avaliacao_id');
            $checkMetas = $request->get('checkMeta');
            
            foreach ($checkMetas as $checkMeta) {
                $metrica = new Metrica;
                
                $metrica->avaliacaos_id = $id_avaliacao;
                $metrica->tipo_metricas_id = 1;
                $metrica->created_by = Auth::user()->id;

                $metrica->save();
            }

            return "Success!!";

            $metrica = Metrica::find($id_avaliacao);
            $metrica->avaliacaos_id = $request->get('avaliacao_id');
        
            $metrica->save();
                    
            return "Success!";
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function delete_metrica($id)
    {

        //Apagar a metrica apartir da modal
        //Verificar se a avaliacao já está FECHADA, não ser possivel apagar
        
        /*
        Antes de apagar uma metrica verificar se o registo existe
        na tabela avaliacao_alunos (campo - metricas_id)
        se nao existir pode ser permetido apagar
        se existir nao apagar
        */

        $avaliacao_aluno = AvaliacaoAluno::join('metricas', 'avaliacao_alunos.metricas_id', '=', 'metricas.id')
                    ->where('avaliacao_alunos.metricas_id', $id)
                    ->get();

        //Se a collection que consulta as avaliacoes por id da metrica
        //retornar vazio podemos apagar a metrica
        if ($avaliacao_aluno->isEmpty()) {
            $metrica = Metrica::find($id);
            $metrica->delete();
            //$metrica->deleted_by = Auth::user()->id;
            //$metrica->save();
            //Toastr::success(__('Metríca eliminada com sucesso'), __('toastr.success'));
            return response()->json(['success'=>'Metríca eliminada com sucesso!']);
            //return redirect()->route('avaliacao.index');
        } else {
            //Retornar Erro porque existe registo com o id da metrica selecionado
            //Não vai ser possivel apagar
            //Toastr::error(__('Metríca já foi associada'), __('toastr.error'));
            return response()->json(['error'=>'Metríca já foi associada']);

            //return redirect()->route('avaliacao.index');
        }
    }


    public function metricaCalendario($id_metrica,$id_semestre,$id_avaliacao)
    {

        $metrica=DB::table('metricas')
        ->where('metricas.id', $id_metrica)
        ->get();

        $metrica_calendario=DB::table('avaliacaos as av')
        ->join('metricas as mt','mt.avaliacaos_id','=','av.id')
        ->leftJoin('calendario_prova as cp','cp.id_avaliacao','=','av.id')
        ->leftJoin('calendarie_metrica as cm',function($join)
        {
            $join->on('cm.id_metrica','=','mt.id');
        })
        ->select([
            'mt.nome as nome_metrica',

            'cp.id as id_caledProva',
            'cp.date_start as data_starProva',
            'cp.data_end as data_endProva',

            'cm.id as id_calendMetrica',
            'cm.data_inicio as data_starMetrica',
            'cm.data_fim as data_endMetrica'
        ])
        ->where('cp.simestre',$id_semestre)
        ->where('cm.id_periodo_simestre', $id_semestre)
        ->where('cm.id_metrica', $id_metrica)
        ->where('cm.deleted_at', null)
        ->where('cp.deleted_by', null)
        ->get();

        $semestre= DB::table('discipline_periods as period')
             ->leftJoin('discipline_period_translations as dt', function ($join) {
             $join->on('dt.discipline_periods_id', '=', 'period.id');
             $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
             $join->on('dt.active', '=', DB::raw(true));
             })
             ->where('period.id',$id_semestre)
             ->select(['dt.display_name as nome_semestre','period.id'])
             ->orderBy('dt.display_name', 'asc')
             ->get();
             $consulta=count($metrica_calendario);
             if($consulta>0){
                    return  response()->json(['metrica'=>$metrica, 'semestre'=>$semestre,'metrica_calendario'=>$metrica_calendario]);
             }
             else{
                $calendario_avaliacao=DB::table('avaliacaos as av')
                    ->join('calendario_prova as cp','cp.id_avaliacao','=','av.id')
                    ->select([
                        'cp.id as id_caledProva',
                        'cp.date_start as data_starProva',
                        'cp.data_end as data_endProva',
                    ])
                    ->where('cp.id_avaliacao',$id_avaliacao)
                    ->where('cp.simestre',$id_semestre)
                    ->where('cp.deleted_by', null)
                    ->get();
                 return  response()->json(['metrica'=>$metrica, 'semestre'=>$semestre,'metrica_calendario'=>$metrica_calendario,'calendario_avaliacao'=>$calendario_avaliacao]);
             }

    }
    public function ad_metricaCalendario(Request $request)
    {

        if(strtotime($request->get('data_inicio'))>strtotime($request->get('data_fim'))){
            return response()->json(['error'=>'Data incial invalida digite uma data menor ou igual que a data final! ']);
          
        }else{
            $currentData = Carbon::now();
            $id_usuario=Auth::user()->id;
            $calendario_avaliacao=DB::table('avaliacaos as av')
                    ->join('calendario_prova as cp','cp.id_avaliacao','=','av.id')
                    ->select([
                        'cp.id as id_caledProva',
                        'cp.date_start as data_starProva',
                        'cp.data_end as data_endProva',
                    ])
                    ->where('cp.id_avaliacao',$request->get('id_avaliacao'))
                    ->where('cp.simestre',$request->get('semestre_id'))
                    ->where('cp.deleted_by', null)
                    ->get();

                    // VERIFICAR SE DATA DA METRICA ENTROCONTRA-SE NO INTERVALO DA DATA DA PROVA (AVALIACAO) 
                    foreach ($calendario_avaliacao as $item_calendario) {}
                    if (strtotime($request->get('data_inicio'))>=strtotime($item_calendario->data_starProva) and strtotime($request->get('data_inicio'))<=strtotime($item_calendario->data_endProva)) {

                        if (strtotime($request->get('data_fim'))>=strtotime($item_calendario->data_starProva) and strtotime($request->get('data_fim'))<=strtotime($item_calendario->data_endProva)) {
                            
                            $pesq_last_metrica =DB::table('metricas as mt') 
                                ->join('calendarie_metrica as cm','cm.id_metrica','=','mt.id')
                                ->select([
                                    'cm.id as id',
                                    'cm.data_inicio as data_starProva',
                                    'cm.data_fim as data_endProva',
                                ])
                                ->where('mt.avaliacaos_id',$request->get('id_avaliacao'))
                                ->where('mt.calendario',0)
                                ->where('cm.id_periodo_simestre',$request->get('semestre_id'))
                                ->where('cm.deleted_at',null)
                                ->where('cm.deleted_by',null)
                                ->get()->last();

                            

                            if($pesq_last_metrica!=null){
                                foreach ($pesq_last_metrica as $item_pesq_last_metrica) {}
                                if(strtotime($request->get('data_inicio'))>=strtotime($item_pesq_last_metrica)){
                                    DB::table('calendarie_metrica')->insert([
                                        'id_metrica' => $request->get('metrica_id'),
                                        'id_periodo_simestre' => $request->get('semestre_id'),
                                        'data_inicio' =>$request->get('data_inicio'),
                                        'data_fim' => $request->get('data_fim'),
                                        'created_by' =>$id_usuario,
                                        'updated_by' => $id_usuario,
                                        'deleted_by' =>null,
                                        'deleted_at' => null
                                    ]);
                                    return response()->json(['success'=>"Métrica registada com sucesso"]);

                                }else{
                                    return response()->json(['error'=>"Data da Métrica indisponível! a data inicial tem que superior a ".$item_pesq_last_metrica]);
                                }
                                    
                          
                                    
                            }else{
                                DB::table('calendarie_metrica')->insert([
                                    'id_metrica' => $request->get('metrica_id'),
                                    'id_periodo_simestre' => $request->get('semestre_id'),
                                    'data_inicio' =>$request->get('data_inicio'),
                                    'data_fim' => $request->get('data_fim'),
                                    'created_by' =>$id_usuario,
                                    'updated_by' => $id_usuario,
                                    'deleted_by' =>null,
                                    'deleted_at' => null
                                ]);
                                return response()->json(['success'=>"Métrica registada com sucesso"]);
                            }
                        }
                    }
        }
    }
    public function delete_calendMetrica ($id_calendMetrica)
    {
        $currentData = Carbon::now();
        $id_usuario=Auth::user()->id;
        DB::table('calendarie_metrica')
        ->where('calendarie_metrica.id',$id_calendMetrica)
        ->update([
            'deleted_by' =>$id_usuario,
            'deleted_at' => $currentData
        ]);

        DB::table('calendarie_metrica_segunda_chamada')
        ->where('id_calendarie_metrica.id',$id_calendMetrica)
        ->update([
            'deleted_by' =>$id_usuario,
            'deleted_at' =>$currentData
         
        ]);
        return response()->json(['success'=>"Métrica com calêndario Eliminada com sucesso"]);
      
    }

    public function editarMetrica(Request $request)
    {  
       try {
        $currentData = Carbon::now();

        if ($request->get('id_metricaCalendario')==0) {
            $somaPercentagem = DB::table('avaliacaos')
                ->join('metricas', 'metricas.avaliacaos_id', '=', 'avaliacaos.id')
                ->select('metricas.percentagem', DB::raw('SUM(percentagem) as total_percentagem'))
                ->where('metricas.avaliacaos_id',$request->get('id_avaliacao') )
                ->where('metricas.calendario','!=',1)
                ->where('metricas.deleted_at',null)
                ->where('metricas.deleted_by',null)
                // ->groupBy('metricas.percentagem')
                ->get(); 

            foreach ($somaPercentagem as $value) {$total_persentagem=$value->total_percentagem;}
                $percentagem=$request->get('percentagem_metrica');
                //avaliar se a percentagem a ser inserida está entre 1 a 100
                if ($percentagem > 0 && $percentagem <= 100) {
                        //Avaliar se o total das percentagens + a percentagem a ser inserida passa de 100
                    $persentagem_total=$total_persentagem + $percentagem;
                    intval($persentagem_total);
                    if ($persentagem_total<=100){
                            $update_OA= DB::table('metricas as mt') 
                                ->where('mt.calendario',1)
                                ->where('mt.deleted_by',null)
                                ->where('mt.deleted_at',null)
                                ->where('mt.avaliacaos_id',$request->get('id_avaliacao'))
                                ->update([
                                    'mt.nome' => $request->get('nomeMetrica'),
                                    'mt.percentagem' =>$request->get('percentagem_metrica'),
                                    'mt.tipo_metricas_id' => $request->get('tipos_metricas'),
                                    'mt.updated_at' => $currentData
                            ]);

                        return response()->json(['success'=>"Métrica atualizada com sucesso"]);
                    }
                    else{
                        return response()->json(['error'=>"Percentagem superior à 100%, digite outra percentagem."]);
                    }
                }
            
        }
        else{
            if(strtotime($request->get('dataInicio_metrica'))>strtotime($request->get('dataFim_metrica'))){
                return response()->json(['error'=>'Data incial invalida digite uma data menor ou igual que a data final! ']);
            
            }else{
                // SOMAR TODAS AS PERCENTAGENS JÁ CADASTRADAS.
            
            
                //NAO PERMETIR ASSOCIAR O MESMO NOME DE METRICA PRA MESMA AVALIACAO
                $pesq_metricaCalendario= Metrica::select('metricas.id')
                    ->join('calendarie_metrica as  cm', 'cm.id_metrica','=','metricas.id')
                    // ->where('metricas.nome', $request->get('nomeMetrica'))
                    ->where('metricas.deleted_at',null)
                    ->where('cm.deleted_at',null)
                    ->where('cm.deleted_by',null)
                    ->where('cm.id',$request->get('id_metricaCalendario'))
                    ->where('metricas.avaliacaos_id',$request->get('id_avaliacao'))
                    ->get();
            
                foreach ($pesq_metricaCalendario as $item) {}
                $pes_mestricas=DB::table('metricas')->where('metricas.id','!=',$item->id)
                    ->where('metricas.nome', $request->get('nomeMetrica'))
                    ->where('metricas.avaliacaos_id',$request->get('id_avaliacao') )
                    ->get();
                $conta=count($pes_mestricas);



                $soma_percentagem = DB::table('avaliacaos')
                ->join('metricas', 'metricas.avaliacaos_id', '=', 'avaliacaos.id')
                ->select('metricas.percentagem', DB::raw('SUM(percentagem) as total_percentagem'))
                ->where('metricas.avaliacaos_id',$request->get('id_avaliacao') )
                ->where('metricas.id','!=',$item->id)
                ->where('metricas.deleted_at',null)
                ->where('metricas.deleted_by',null)
                // ->groupBy('metricas.percentagem')
                ->get();

                if($conta>0){
                return response()->json(['error'=>"Nome da métrica já existe"]);
                }else{
                    foreach ($soma_percentagem as $value) {$total_persentagem=$value->total_percentagem;}
                    $percentagem=$request->get('percentagem_metrica');
                    //avaliar se a percentagem a ser inserida está entre 1 a 100
                    if ($percentagem > 0 && $percentagem <= 100) {
                            //Avaliar se o total das percentagens + a percentagem a ser inserida passa de 100
                        $persentagem_total=$total_persentagem + $percentagem;
                        intval($persentagem_total);
                        if ($persentagem_total<=100){

                            $calendario_avaliacao=DB::table('avaliacaos as av')
                                ->join('calendario_prova as cp','cp.id_avaliacao','=','av.id')
                                ->select([

                                    'cp.id as id_caledProva',
                                    'cp.date_start as data_starProva',
                                    'cp.data_end as data_endProva',
                                ])
                                ->where('cp.id_avaliacao',$request->get('id_avaliacao'))
                                ->where('cp.simestre',$request->get('semestre'))
                                ->where('cp.deleted_by', null)
                                ->get();
                                foreach ($calendario_avaliacao as $item_calendario) {}
                            if (strtotime($request->get('dataInicio_metrica'))>=strtotime($item_calendario->data_starProva) and strtotime($request->get('dataInicio_metrica'))<=strtotime($item_calendario->data_endProva)) {
                                if (strtotime($request->get('dataFim_metrica'))>=strtotime($item_calendario->data_starProva) and strtotime($request->get('dataFim_metrica'))<=strtotime($item_calendario->data_endProva)) {
                                    

                                        // CODE QUE PERMITE VERFICAR SE A DATA DA METRICA ESTA DISPONIVEL PARA SER CADASTRADA.
                                    // $pesq_last_metrica =DB::table('metricas as mt') 
                                    //     ->join('calendarie_metrica as cm','cm.id_metrica','=','mt.id')
                                    //     ->select([
                                    //         'cm.id as id',
                                    //         'cm.data_inicio as data_starProva',
                                    //         'cm.data_fim as data_endProva',
                                    //     ])
                                    //     ->where('mt.avaliacaos_id',$request->get('id_avaliacao'))
                                    //     ->where('mt.calendario',0)
                                    //     ->where('cm.id_periodo_simestre',$request->get('semestre'))
                                    //     ->where('cm.id',"!=",$request->get('id_metricaCalendario'))
                                    //     ->where('cm.deleted_at',null)
                                    //     ->where('cm.deleted_by',null)
                                    //     ->get();

                                        // for ($i=0; $i <count($pesq_last_metrica) ; $i++) { 
                                        //     if (strtotime($request->get('dataInicio_metrica'))>strtotime($pesq_last_metrica[$i]->data_starProva && strtotime($request->get('dataInicio_metrica'))<strtotime($pesq_last_metrica[$i]->data_endProva) )) {
                                            
                                        //         return response()->json(['data- '=>$i ,'data inicio'=>$pesq_last_metrica[$i]->data_starProva ,'data fim'=>$pesq_last_metrica[$i]->data_endProva]);
                                        //         break;
                                        //     }
                                        // }
                                            $update= DB::table('metricas as mt') 
                                                ->where('mt.id',$item->id)
                                                ->update([
                                                    'mt.nome' => $request->get('nomeMetrica'),
                                                    'mt.percentagem' =>$request->get('percentagem_metrica'),
                                                    'mt.tipo_metricas_id' => $request->get('tipos_metricas'),
                                                    'mt.updated_at' => $currentData
                                                    
                                                ]);

                                               

                                                DB::table('calendarie_metrica as cm') 
                                                    ->where('cm.id',$request->get('id_metricaCalendario'))
                                                    ->update([
                                                        'cm.data_inicio' => $request->get('dataInicio_metrica'),
                                                        'cm.data_fim' => $request->get('dataFim_metrica'),
                                                        'cm.update_at' => $currentData

                                                    ]);

                                            return response()->json(['success'=>"Data atualizada com sucesso"]);
                                }
                            }   

                        
                        }else{
                            return response()->json(['error'=>"Percentagem superior à 100%, digite outra percentagem."]);
                        } 
                    }
                }
                
                return response()->json(['error'=>"Conflito com o intervalo no calendario de prova"]);
            }
        }  
        } catch (Exception | Throwable $e) {
            Log::error($e);
         
            return response()->json($e->getMessage(), 500);
        }
    }

    public function calendMetrica_segundaChamada(Request $request){
        try{
           
            $mc = DB::table('calendarie_metrica') 
            ->where('id', $request->get('id_metricaCalendario')) 
            ->first();
            $exists = strtotime($mc->data_fim) >= strtotime($request->get('dataInicio_metrica'));

           if($request->get('segunda_chamada') == null){
            if(strtotime($request->get('dataInicio_metrica'))>strtotime($request->get('dataFim_metrica'))){
                return response()->json(['error'=>'Data inicial invalida, digite uma data menor ou igual que a data final! ']);
            
            }
            else if($exists)
        
        {
            return response()->json(['error'=>'Data inicial invalida, digite uma data maior que a data final da primeira chamada! ']);
        }
            else{
                DB::table('calendarie_metrica_segunda_chamada')->insert([
                    'id_calendarie_metrica' => $request->get('id_metricaCalendario'),
                    'data_inicio' =>$request->get('dataInicio_metrica'),
                    'data_fim' => $request->get('dataFim_metrica'),
                    'created_by' =>auth()->user()->id,
                    'created_at' =>Carbon::now()
                 
                ]);
            }
            
           }
           else {
            if(strtotime($request->get('dataInicio_metrica'))>strtotime($request->get('dataFim_metrica'))){
                return response()->json(['error'=>'Data incial invalida digite uma data menor ou igual que a data final! ']);
            
            }
            else if($exists)
        
        {
            return response()->json(['error'=>'Data inicial invalida, digite uma data maior que a data final da primeira chamada! ']);
        }
            else{
            DB::table('calendarie_metrica_segunda_chamada')
                    ->where('id', $request->get('segunda_chamada'))
                    ->update([
                'data_inicio' =>$request->get('dataInicio_metrica'),
                'data_fim' => $request->get('dataFim_metrica'),
                'updated_by' =>auth()->user()->id,
                'updated_at' =>Carbon::now()
            ]);
           }
        }

            return response()->json(['success'=>"Data gravada com sucesso"]);
        }
        catch(Exception $e){
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function metrica_edit($id){
    
        $lective = DB::table('lective_years')->where('is_termina', 0)->first();

       $metrica = DB::table('metricas')->where('id',$id)->first();

       $tipo_metricas = DB::table('tipo_metricas')->where('anoLectivo',$lective->id)->get();


       return [

        "metrica" => $metrica,
        "tipo_metricas" => $tipo_metricas

       ];

    }


    public function metrica_actualizar(Request $request){

        try{

            DB::beginTransaction();

        DB::table('metricas')
        ->where('id',$request->metrica_id_edit)
        ->update([
            'percentagem' => $request->percentagem_edit,
            'nome' => $request->nome_metrica_edit,
            'tipo_metricas_id' => $request->tipo_metrica_edit
        ]
        )
        ;
        
        DB::commit();
        Toastr::success(__("MÉTRICA ACTUALIZADA COM SUCESSO"), __("toastr.success"));
        return redirect()->route('avaliacao.index');
    }
        catch(Exception $e){
            DB::rollBack();



        }


    }


}