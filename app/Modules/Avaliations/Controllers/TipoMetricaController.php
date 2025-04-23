<?php

namespace App\Modules\Avaliations\Controllers;

use App\Helpers\LanguageHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Avaliations\Models\Metrica;
use App\Modules\Avaliations\Models\TipoAvaliacao;
use App\Modules\Avaliations\Models\TipoMetrica;
use Toastr;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;
use Carbon\Carbon;
use App\Modules\GA\Models\LectiveYear;
use Yajra\DataTables\Facades\DataTables;

class TipoMetricaController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $lectiveYears = LectiveYear::with(['currentTranslation'])
            ->get();
           $currentData = Carbon::now();
           $lectiveYearSelected = DB::table('lective_years')
            ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
            ->first();
           $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
            return view("Avaliations::tipo-metrica.tipo-metrica", compact('lectiveYears','lectiveYearSelected'));
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }



    public function ajax()
    {
        try {
            $model = TipoMetrica::join('users as u1', 'u1.id', '=', 'tipo_metricas.created_by')
                    ->leftJoin('users as u2', 'u2.id', '=', 'tipo_metricas.updated_by')
                    ->leftJoin('users as u3', 'u3.id', '=', 'tipo_metricas.deleted_by')
                    ->select([
                        'tipo_metricas.id',
                        'tipo_metricas.nome',
                        'tipo_metricas.codigo as code',
                        'tipo_metricas.abreviatura as abreviatura',
                        'tipo_metricas.descricao as descricao',
                        'u1.name as created_by',
                        'u2.name as updated_by',
                        'u3.name as deleted_by',
                        //'u0.name as student',
                    ]);

            return DataTables::eloquent($model)
                    ->addColumn('actions', function ($item) {
                        return view('Avaliations::tipo-metrica.datatables.actions')->with('item', $item);
                    })
                    ->rawColumns(['actions'])
                    ->toJson();
        } catch (Exception | Throwable $e) {
            logError($e);
            return response()->json($e->getMessage(), 500);
        }
    }


    
    public function ajax_anoLectivo($anoLectivo)
    {
        try {
            $model = TipoMetrica::join('users as u1', 'u1.id', '=', 'tipo_metricas.created_by')
                    ->leftJoin('users as u2', 'u2.id', '=', 'tipo_metricas.updated_by')
                    ->leftJoin('users as u3', 'u3.id', '=', 'tipo_metricas.deleted_by')
                    ->select([
                        'tipo_metricas.id',
                        'tipo_metricas.nome',
                        'tipo_metricas.codigo as codigo',
                        'tipo_metricas.abreviatura as abreviatura',
                        'tipo_metricas.descricao as descricao',
                        'u1.name as created_by',
                        'u2.name as updated_by',
                        'u3.name as deleted_by',
                        //'u0.name as student',
                    ])->where('anoLectivo',$anoLectivo);

            return DataTables::eloquent($model)
                    ->addColumn('actions', function ($item) {
                        return view('Avaliations::tipo-metrica.datatables.actions')->with('item', $item);
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
            return view("Avaliations::tipo-metrica.create-tipo-metrica");
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    public function create_type($anoLective)
    {
        try {
            return view("Avaliations::tipo-metrica.create-tipo-metrica",compact('anoLective'));
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
            //Verificar se já existe um tipo de metrica com o mesmo nome
            $getTPMT = TipoMetrica::select('*')->where('nome', $request->get('nome'))->where('anoLectivo', $request->lectiveYear)->get();
            if ($getTPMT->isEmpty()) {
                $tipoMetrica = TipoMetrica::create([
                    'nome' => $request->get('nome'),
                    'codigo' => $request->codigo,
                    'abreviatura' => $request->abreviatura,
                    'anoLectivo' => $request->lectiveYear,
                    'descricao' => $request->descricao,
                    'created_by' => Auth::user()->id,
                ]);
                $tipoMetrica->save();
                
                // Success message
                Toastr::success(__('Registo adicionado com sucesso'), __('toastr.success'));
                return redirect()->route('tipo_metrica.index');
            } else {
                // Error message
                Toastr::error(__('Já existe um tipo de metrica com este nome'), __('toastr.error'));
                return redirect()->route('tipo_metrica.index');
            }
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
            $tipoMetrica = TipoMetrica::find($id);
            return view('Avaliations::tipo-metrica.show-tipo-metrica', compact('tipoMetrica'));
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
            $tipoMetrica = TipoMetrica::find($id);
            return view('Avaliations::tipo-metrica.edit-tipo-metrica', compact('tipoMetrica'));
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
            //Verificar se já existe um tipo de metrica com o mesmo nome
            $getTPMT = TipoMetrica::select('*')->where('nome', $request->get('nome'))->get();
            if ($getTPMT->isEmpty()) {
                $tipoMetrica = TipoMetrica::find($id);
                $tipoMetrica->nome = $request->get('nome');
                $tipoMetrica->codigo = $request->get('codigo');
                $tipoMetrica->descricao = $request->get('descricao');
                $tipoMetrica->abreviatura = $request->get('abreviatura');
                $tipoMetrica->updated_by = Auth::user()->id;
                $tipoMetrica->save();
                // Success message
                Toastr::success(__('Tipo de Metrica editado com sucesso'), __('toastr.success'));
                return redirect()->route('tipo_metrica.index');
            } else {
                // Error message
                Toastr::error(__('Já existe um tipo de metrica com este nome'), __('toastr.error'));
                return redirect()->route('tipo_metrica.index');
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
        $getTM = Metrica::where('tipo_metricas_id', $id)->get();

        if (!$getTM->isEmpty()) {
            // Error message
            Toastr::error(__('Tipo de Métrica já foi associada'), __('toastr.error'));
            return redirect()->route('tipo_metrica.index');
        } else {
            $tm = TipoMetrica::find($id);
            $tm->delete();
            //$tm->deleted_by = Auth::user()->id;
            //$tm->save();
            // Success message
            Toastr::success(__('Tipo de Métrica eliminada com sucesso'), __('toastr.success'));
            return redirect()->route('tipo_metrica.index');
        }
    }
}
