<?php

namespace App\Modules\Avaliations\Controllers;

use Toastr;
use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Modules\GA\Models\LectiveYear;
use Yajra\DataTables\Facades\DataTables;
use App\Modules\Avaliations\Models\AvaliacaoConfig;

class ConfigurationController extends Controller{
    
    private $strategies = ["ISPK" => "Padrão ISPK" ];
    
    private $table = "avalicao_config";

    public function index(){
        $currentData = Carbon::now();
        $lectiveYears = LectiveYear::with(['currentTranslation'])->get();
        $lectiveYearSelected = DB::table('lective_years')
            ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
            ->first();
        $strategies = $this->strategies;
        return view('Avaliations::config-avaliacao.index',compact('lectiveYears','lectiveYearSelected','strategies'));
    }
    
    public function store(Request $request){
        try{
            $config = AvaliacaoConfig::updateOrInsert(
                [ "lective_year" => $request->lective_year ],
                [
                    "lective_year" => $request->lective_year,
                    "strategy" => $request->strategy,
                    "mac_nota_recurso" => $request->mac_nota_recurso,
                    "exame_nota_inicial" => $request->exame_nota_inicial,
                    "exame_nota_final" => $request->exame_nota_final,
                    "exame_nota" => $request->exame_nota,
                    "mac_nota_dispensa" => $request->mac_nota_dispensa,
                ]
            );
            Toastr::success(__('Registo criado com sucesso'), __('toastr.success'));
        }catch(Exception $e){
            dd($e->getMessage());
            Toastr::error($e->getMessage(), __('toastr.error'));
        }
        return redirect()->route('avaliacao.config');
    }    
    
    public function update(Request $request, $id){
        try{
            $data = $request->all();
            $config = AvaliacaoConfig::find($id);
            $config->update($data);
            Toastr::success(__('Registo actualizado com sucesso'), __('toastr.success'));
        }catch(Exception $e){
            dd($e->getMessage());
            Toastr::error($e->getMessage(), __('toastr.error'));
        }
        return redirect()->route('avaliacao.config');
    }  
    
    public function destroy($id){
        try{
            $config = AvaliacaoConfig::find($id);
            $config->delete();
            Toastr::success(__('Registo eliminado com sucesso'), __('toastr.success'));
        }catch(Exception $e){
            dd($e->getMessage());
            Toastr::error($e->getMessage(), __('toastr.error'));
        }
        return redirect()->route('avaliacao.config');        
    }  

    public function getConfigurations(Request $request)
    {
        try {
            $model = DB::table($this->table.' as ac')
                ->join('lective_year_translations as lyt','lyt.lective_years_id','ac.lective_year')
                ->where('lyt.active',1)
                ->select('ac.*','lyt.display_name');
                
            if(isset($request->lective_year)) $model = $model->where('ac.lective_year',$request->lective_year);
            
            $model = $model->get();
            return Datatables::of($model)->addColumn('actions', function ($item) {
                return view('Avaliations::config-avaliacao.datatable.actions')->with('item', $item);
           })->rawColumns(['actions'])->addIndexColumn()->toJson();
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

}