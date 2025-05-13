<?php

namespace App\Modules\GA\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\GA\Models\DocumentsTypes;
use Illuminate\Http\Request;
use Exception;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;
use Carbon\Carbon;
use Brian2694\Toastr\Facades\Toastr;
use App\Modules\GA\Requests\DocumentsTypeRequest;
use App\Modules\GA\Requests\DocumentsTypeUpdateRequest;
use App\Modules\GA\Requests\DocumentsTypeDeleteRequest;
use App\Modules\GA\Requests\DocumentsTypeRestoreRequest;
use App\Modules\GA\Requests\DocumentsTypeForceDeleteRequest;
use App\Modules\GA\Requests\DocumentsTypeShowRequest;


class DocumentsTypesController extends Controller
{
    public function index()
    {
        return view('GA::documents-types.index');
    }
    public function teste()
    {
        return  "as rotas assim como os controllers estao OK!"; //view('GA::documents-types.documents-types', ['action'=>'create']);
    }
    public function show($id)
    {
        

        $data = DB::table('documentation_type')->find($id);
        if($data == null)
        {
            Toastr::error(__('Tipo de documento não encontrado'), __('toastr.error'));
            return redirect()->route('documents-types.index');
        }   
        return view('GA::documents-types.documents-types', ['id' => $id, 'action'=>'show','data'=>$data]);
    }
    public function create() {
        $create = 'create';
        return view('GA::documents-types.documents-types')->with('action', $create);;
    }
    public function edit($id){

        $data = DB::table('documentation_type')->find($id);

        return view('GA::documents-types.documents-types', ['id' => $id, 'action'=>'edit','data'=>$data]);
    }
    public function update(Request $request, $id)
    {
        try{
            DB::table('documentation_type')

                        ->where('id', $id)
                        ->Update([
                           
                            'name' => $request->get('name'),
                            'observation' => $request->get('observation'),
                            //'codigo' => $request->get('codigo'),
                            //'abreviacao' => $request->get('abreviacao'),
                            'updated_by' => auth()->user()->id,
                            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                        ]);

                Toastr::success(__('Actualizado com sucesso'), __('toastr.success'));   
                return redirect()->route('documents-types.index');

        }
        catch(Exception $e){
            Log::error($e);
           return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }

    }
    
    public function store(Request $request){
        try{
                $request->validate([
                    'name' => 'required|max:255',
                    'observation' => 'nullable|max:255',
                ]);
                $registo = new DocumentsTypes();
                $registo->name = $request->get('name');
                $registo->observation = $request->get('observation');
                $registo->created_by = auth()->user()->id;
                $registo->save();

                Toastr::success(__('Criado com sucesso'), __('toastr.success'));   
                return redirect()->route('documents-types.index');
    
            } catch (Exception | Throwable $e) {
                Toastr::error($e->getMessage(), __('toastr.error'));
                Log::error($e);
                return $request->ajax()
                    ? response()->json($e->getMessage(), 500)
                    : abort(500);
            }
 
    }

    public function ajax() {

        try{

          $model= DB::table('documentation_type')
                ->whereNull('deleted_at')
                ->whereNull('deleted_by')
                ->get();

            foreach($model as $item)
            { 
                // pegando o nome do usuário que criou

                $data = DB::table('documentation_type')
                    ->join('users','documentation_type.created_by','=','users.id' )
                    ->select('users.name')
                    ->where('users.id', $item->created_by)
                    ->first();

                    $item->created_by = $data->name;

                // pegando o nome do usuário que actualizou
                if($item->updated_by != null )
                {
                    
                    $data = DB::table('documentation_type')
                    ->join('users','documentation_type.updated_by','=','users.id' )
                    ->select('users.name')
                    ->where('users.id', $item->updated_by)
                    ->first();
                    
                    $item->updated_by = $data->name;
               }
    
            }

            return DataTables::of($model)
            ->addColumn('actions', function ($item) {
                 return view('GA::documents-types.datatables.actions')->with('item', $item);
                         })
            ->addIndexColumn()   
            ->rawColumns(['actions'])
            ->toJson();
         
        }
        catch(Exception | Throwable $e)
        {
            dd($e);
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }

    
    }

}
//fim do codigo