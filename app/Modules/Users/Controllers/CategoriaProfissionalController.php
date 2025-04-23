<?php

namespace App\Modules\Users\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use DB;
use Illuminate\Http\Request;
use Toastr;
use Carbon\Carbon;
use Log;
use Exception;
use App\Modules\Users\Requests\CategoriaProfissionalRequest;
class CategoriaProfissionalController extends Controller {

    public function index() {

       
        return view('Users::categoria-profissional.index');
    }

    public function show($id){

        $data = DB::table('categoria_profissional')
        ->find($id);

     

    return view('Users::categoria-profissional.categoria-profissional', 
        ['id' => $id, 'action'=>'show','data'=>$data]);
    }


    public function edit($id){

        $data = DB::table('categoria_profissional')
                    ->find($id);

        return view('Users::categoria-profissional.categoria-profissional', 
        ['id' => $id, 'action'=>'edit','data'=>$data]);
    }

    public function create() {
        $create = 'create';
    return view('Users::categoria-profissional.categoria-profissional')->with('action', $create);
    }


    public function ajax() {

        try{

          $model= DB::table('categoria_profissional')
                ->whereNull('deleted_at')
                ->whereNull('deleted_by')
                ->get();

            foreach($model as $item)
            { 

                // pegando o nome do usuário que criou
                $data = DB::table('categoria_profissional')
                    ->join('users','categoria_profissional.created_by','=','users.id' )
                    ->select('users.name')
                    ->where('users.id', $item->created_by)
                    ->first();

                    $item->created_by = $data->name;

                // pegando o nome do usuário que actualizou
                if($item->updated_by != null )
                {
                    
                $data = DB::table('categoria_profissional')
                ->join('users','categoria_profissional.updated_by','=','users.id' )
                ->select('users.name')
                ->where('users.id', $item->updated_by)
                ->first();

                $item->updated_by = $data->name;
               }
    
            }

            return DataTables::of($model)
            ->addColumn('actions', function ($item) {
                 return view('Users::categoria-profissional.datatables.actions')->with('item', $item);
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


    public function store(CategoriaProfissionalRequest $request){

        try{
                
            DB::table('categoria_profissional')
                ->insert(
                    [
                    'nome' => $request->get('nome'),
                    'descricao' => $request->get('descricao'),
                    'codigo' => $request->get('codigo'),
                    'abreviacao' => $request->get('abreviacao'),
                    'created_by' => auth()->user()->id
                ]);

              // Success message
              Toastr::success(__('Criado com sucesso'), __('toastr.success'));
              return redirect()->route('categoria-profissional.index');
        }
         catch (Exception | Throwable $e) {
          dd($e);
           Toastr::error($e->getMessage(), __('toastr.error'));
            Log::error($e);
           return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    
    
       
    }

    public function update($id, CategoriaProfissionalRequest $request){
        try{

            DB::table('categoria_profissional')
                        ->where('id', $id)
                        ->Update([
                           
                            'nome' => $request->get('nome'),
                            'descricao' => $request->get('descricao'),
                            'codigo' => $request->get('codigo'),
                            'abreviacao' => $request->get('abreviacao'),
                            'updated_by' => auth()->user()->id,
                            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                        ]);

            Toastr::success(__('Actualizado com sucesso'), __('toastr.success'));
            return redirect()->route('categoria-profissional.index', $id);

        }
        catch(Exception $e){
            Log::error($e);
           return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
    }

    }


    public function destroy($id) {

        try{
    
    
            DB::table('categoria_profissional')
            ->where('id', $id)
            ->update(
              [
                'deleted_by' => auth()->user()->id,
                'deleted_at' => Carbon::now()->format('Y-m-d H:i')
              ]);
    
          // Success message
          Toastr::success('Success');
          return redirect()->route('categoria-profissional.index');
       
      }
     catch (Exception | Throwable $e) {
      //  dd($e);
        Toastr::error($e->getMessage(), __('toastr.error'));
        Log::error($e);
        return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
       }
    }





}