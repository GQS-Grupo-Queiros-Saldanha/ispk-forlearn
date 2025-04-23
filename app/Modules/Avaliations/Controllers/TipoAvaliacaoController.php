<?php

namespace App\Modules\Avaliations\Controllers;

use App\Helpers\LanguageHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Avaliations\Models\Avaliacao;
use App\Modules\Avaliations\Models\TipoAvaliacao;
use Toastr;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use App\Modules\GA\Models\LectiveYear;
class TipoAvaliacaoController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            /*$model = TipoAvaliacao::join('users as u1', 'u1.id', '=', 'tipo_avaliacaos.created_by')
                   ->leftJoin('users as u2', 'u2.id', '=', 'tipo_avaliacaos.updated_by')
                   ->leftJoin('users as u3', 'u3.id', '=', 'tipo_avaliacaos.deleted_by')
                   ->select([
                       'tipo_avaliacaos.id as id',
                       'tipo_avaliacaos.nome as tipo_avaliacao_nome',
                       'u1.name as created_by',
                       'u2.name as updated_by',
                       'u3.name as deleted_by',
                       'tipo_avaliacaos.created_at as created_at'
                       //'u0.name as student',
                   ])
                   ->get();*/

                   $lectiveYears = LectiveYear::with(['currentTranslation'])
                   ->get();
                  $currentData = Carbon::now();
                  $lectiveYearSelected = DB::table('lective_years')
                   ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
                   ->first();
                  $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
               
            return view("Avaliations::tipo-avaliacao.tipo-avaliacao", compact('lectiveYears','lectiveYearSelected'));
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }















    
    public function ajax_anoLectivo($anoLectivo)
    {

        try {
            // return $anoLectivo;
             $model = TipoAvaliacao::join('users as u1', 'u1.id', '=', 'tipo_avaliacaos.created_by')
                    ->leftJoin('users as u2', 'u2.id', '=', 'tipo_avaliacaos.updated_by')
                    ->leftJoin('users as u3', 'u3.id', '=', 'tipo_avaliacaos.deleted_by')
                    ->select([
                        'tipo_avaliacaos.id',
                        'tipo_avaliacaos.nome',
                        'tipo_avaliacaos.codigo as codigo',
                        'tipo_avaliacaos.abreviatura as abreviatura',
                        'tipo_avaliacaos.descricao as descricao',
                        'u1.name as created_by',
                        'u2.name as updated_by',
                        'u3.name as deleted_by',
                        'tipo_avaliacaos.created_at as created_at',
                        'tipo_avaliacaos.updated_at as updated_at'
                        //'u0.name as student',
                    ])->where('tipo_avaliacaos.anoLectivo',$anoLectivo)
                    
                    
                    ;

            return DataTables::eloquent($model)
                    ->addColumn('actions', function ($item) {
                        return view('Avaliations::tipo-avaliacao.datatables.actions')->with('item', $item);
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
            // return view("Avaliations::tipo-avaliacao.create-tipo-avaliacao");
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }




    public function create_type($anoLective)
    {
        try {
            return view("Avaliations::tipo-avaliacao.create-tipo-avaliacao",compact('anoLective'));
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
        // return $request;
        try {
            //Verificar se já existe um tipo de avaliacao com o mesmo nome
            $getTPAV = TipoAvaliacao::select('*')->where('nome', $request->get('nome')) ->where('anoLectivo', $request->lectiveYear) ->get();
            if ($getTPAV->isEmpty()) {
                $tipoAvaliacao = TipoAvaliacao::create([
                    'nome' => $request->get('nome'),
                    'codigo' => $request->codigo,
                    'abreviatura' => $request->abreviatura,
                    'anoLectivo' => $request->lectiveYear,
                    'descricao' => $request->descricao,
                    'created_by' => Auth::user()->id
                ]);
                $tipoAvaliacao->save();

                // Success message
                Toastr::success(__('Registo adicionado com sucesso'), __('toastr.success'));

                return redirect()->route('tipo_avaliacao.index');
            } else {
                // Error message
                Toastr::error(__('Já existe um tipo de avaliação com este nome'), __('toastr.error'));

                return redirect()->route('tipo_avaliacao.index');
            }
            
            //return view("Avaliations::tipo-avaliacao.tipo-avaliacao");
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
        try {
            $tipoAvaliacao = TipoAvaliacao::find($id);
            return view('Avaliations::tipo-avaliacao.show-tipo-avaliacao', compact('tipoAvaliacao'));
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
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
        try {
            $tipoAvaliacao = TipoAvaliacao::find($id);
            return view("Avaliations::tipo-avaliacao.edit-tipo-avaliacao", compact('tipoAvaliacao'));
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
            //Verificar se já existe um tipo de avaliacao com o mesmo nome
            $getTPAV = TipoAvaliacao::select('*')->where('nome', $request->get('nome'))->get();
            if ($getTPAV->isEmpty()) {
                $tipoAvaliacao = TipoAvaliacao::find($id);
                $tipoAvaliacao->nome = $request->get('nome');
                $tipoAvaliacao->codigo = $request->get('codigo');
                $tipoAvaliacao->descricao = $request->get('descricao');
                $tipoAvaliacao->abreviatura = $request->get('abreviatura');
                $tipoAvaliacao->updated_by = Auth::user()->id;
                $tipoAvaliacao->save();
            
                // Success message
                Toastr::success(__('Tipo de Avaliação editado com sucesso'), __('toastr.success'));
                return redirect()->route('tipo_avaliacao.index');
            } else {
                // Error message
                Toastr::error(__('Já existe um tipo de avaliação com este nome'), __('toastr.error'));
                return redirect()->route('tipo_avaliacao.index');
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
        $getTPAV = Avaliacao::where('tipo_avaliacaos_id', $id)->get();
        if (!$getTPAV->isEmpty()) {
            // Error message
            Toastr::error(__('Tipo de Avaliação já foi associada'), __('toastr.error'));
            return redirect()->route('tipo_avaliacao.index');
        } else {
            $tpav = TipoAvaliacao::find($id);
            $tpav->delete();
            //$tpav->deleted_by = Auth::user()->id;
            //$tpav->save();
            // Success message
            Toastr::success(__('Tipo de Avaliação eliminada com sucesso'), __('toastr.success'));
            return redirect()->route('tipo_avaliacao.index');
        }
        
        return $getTPAV;
    }

    public function fetch()
    {
        $tipo_avaliacaos = TipoAvaliacao::all();
        return json_encode(array('data'=>$tipo_avaliacaos));
    }
}
