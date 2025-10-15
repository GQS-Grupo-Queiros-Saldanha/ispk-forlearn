<?php

namespace App\Modules\Users\Controllers;

use DB;
use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Toastr;
class RotacaoRegimeEspecialController extends Controller{

    public function index() {

       
        return view('Users::rotacao-regime-especial.index');
    }

    public function show($id){

        $data = DB::table('rotacao_regime_especial')
        ->find($id);

     

    return view('Users::rotacao-regime-especial.form', 
        ['id' => $id, 'action'=>'show','data'=>$data]);
    }


    public function edit($id){

        $data = DB::table('rotacao_regime_especial')
                    ->find($id);

        return view('Users::rotacao-regime-especial.form', 
        ['id' => $id, 'action'=>'edit','data'=>$data]);
    }

    public function create() {
        $create = 'create';
    return view('Users::rotacao-regime-especial.form')->with('action', $create);
    }

    public function ajax() {

        try{

          $model= DB::table('rotacao_regime_especial')
                ->whereNull('rotacao_regime_especial.deleted_at')
                ->whereNull('rotacao_regime_especial.deleted_by')
                ->leftJoin('users as up1','up1.id','rotacao_regime_especial.created_by' )
                ->leftJoin('users as up2','up2.id','rotacao_regime_especial.updated_by' )
                ->select([
                   'rotacao_regime_especial.*',
                   'up1.name as created_by',
                   'up2.name as updated_by'
                ])
                ->get();

            return DataTables::of($model)
            ->addColumn('actions', function ($item) {
                 return view('Users::rotacao-regime-especial.datatables.actions')->with('item', $item);
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


    /* Store a new blog post.
    *
    * @param  Request  $request
    * @return Response
    */
   public function store(Request $request){

       try{
        
           DB::table('rotacao_regime_especial')
               ->insert(
                   [
                   'nome' => $request->get('nome'),
                   'descricao' => $request->get('descricao'),
                   'codigo' => $request->get('codigo'),
                   'created_by' => auth()->user()->id
               ]);
   
               
   
             // Success message
             Toastr::success(__('Criado com sucesso'), __('toastr.success'));
             return redirect()->route('rotacao-regime-especial.index');
       }
        catch (Exception | Throwable $e) {
         
          //Toastr::error($e->getMessage(), __('toastr.error'));
           Log::error($e);
          return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
       }
   
   
      
   }

   public function update($id,Request $request){
       try{
            DB::table('rotacao_regime_especial')
                       ->where('id', $id)
                       ->update([
            
                           'nome' => $request->get('nome'),
                           'descricao' => $request->get('descricao'),
                           'codigo' => $request->get('codigo'),
                           'updated_by' => auth()->user()->id,
                           'updated_at' => Carbon::now()
                       ]);

           Toastr::success(__('Actualizado com sucesso'), __('toastr.success'));
           return redirect()->route('rotacao-regime-especial.index', $id);

       }
       catch(Exception $e){
           Log::error($e);
          return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
   }

   }


   public function destroy($id) {

       try{
   
   
           DB::table('rotacao_regime_especial')
           ->where('id', $id)
           ->update(
             [
               'deleted_by' => auth()->user()->id,
               'deleted_at' => Carbon::now()
             ]);
   
         // Success message
         Toastr::success('Success');
         return redirect()->route('rotacao-regime-especial.index');
      
     }
    catch (Exception | Throwable $e) {
     //  dd($e);
       Toastr::error($e->getMessage(), __('toastr.error'));
       Log::error($e);
       return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
      }
   }







}