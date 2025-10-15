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
use App\Modules\Users\Requests\GrauAcademicoRequest;
class GrauAcademicoController extends Controller {

    public function index() {

       
        return view('Users::grau-academico.index');
    }

    public function show($id){

        $data = DB::table('grau_academico')
        ->find($id);

     

    return view('Users::grau-academico.grau-academico', 
        ['id' => $id, 'action'=>'show','data'=>$data]);
    }


    public function edit($id){

        $data = DB::table('grau_academico')
                    ->find($id);

        return view('Users::grau-academico.grau-academico', 
        ['id' => $id, 'action'=>'edit','data'=>$data]);
    }

    public function create() {
        $create = 'create';
    return view('Users::grau-academico.grau-academico')->with('action', $create);
    }

    public function ajax() {

        try{

          $model= DB::table('grau_academico')
                ->whereNull('deleted_at')
                ->whereNull('deleted_by')
                ->get();

            foreach($model as $item)
            { 

                // pegando o nome do usuário que criou
                $data = DB::table('grau_academico')
                    ->join('users','grau_academico.created_by','=','users.id' )
                    ->select('users.name')
                    ->where('users.id', $item->created_by)
                    ->first();

                    $item->created_by = $data->name;

                // pegando o nome do usuário que actualizou
                if($item->updated_by != null )
                {
                    
                $data = DB::table('grau_academico')
                ->join('users','grau_academico.updated_by','=','users.id' )
                ->select('users.name')
                ->where('users.id', $item->updated_by)
                ->first();

                $item->updated_by = $data->name;
               }
    
            }

            return DataTables::of($model)
            ->addColumn('actions', function ($item) {
                 return view('Users::grau-academico.datatables.actions')->with('item', $item);
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

       /**
     * Store a new blog post.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(GrauAcademicoRequest $request){

        try{
         
            DB::table('grau_academico')
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
              return redirect()->route('grau-academico.index');
        }
         catch (Exception | Throwable $e) {
          
           //Toastr::error($e->getMessage(), __('toastr.error'));
            Log::error($e);
           return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    
    
       
    }

    public function update($id, GrauAcademicoRequest $request){
        try{
             DB::table('grau_academico')
                        ->where('id', $id)
                        ->update([
                           
                            'nome' => $request->get('nome'),
                            'descricao' => $request->get('descricao'),
                            'codigo' => $request->get('codigo'),
                            'abreviacao' => $request->get('abreviacao'),
                            'updated_by' => auth()->user()->id,
                            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                        ]);

            Toastr::success(__('Actualizado com sucesso'), __('toastr.success'));
            return redirect()->route('grau-academico.index', $id);

        }
        catch(Exception $e){
            Log::error($e);
           return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
    }

    }


    public function destroy($id) {

        try{
    
    
            DB::table('grau_academico')
            ->where('id', $id)
            ->update(
              [
                'deleted_by' => auth()->user()->id,
                'deleted_at' => Carbon::now()->format('Y-m-d H:i')
              ]);
    
          // Success message
          Toastr::success('Success');
          return redirect()->route('grau-academico.index');
       
      }
     catch (Exception | Throwable $e) {
      //  dd($e);
        Toastr::error($e->getMessage(), __('toastr.error'));
        Log::error($e);
        return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
       }
    }





}