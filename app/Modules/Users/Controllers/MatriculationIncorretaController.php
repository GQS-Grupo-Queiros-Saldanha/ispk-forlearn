<?php

namespace App\Modules\Users\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Users\Models\User;
use Illuminate\Http\Request;
use DataTables;
use Toastr;
use Auth;
use DB;

class MatriculationIncorretaController extends Controller

{      






    public function numberMatriculation(){
        
       
      $dados  =$this->dataMatriculation();
        if($dados->isEmpty()){
            return  " Já não existe dados que necessitam de ser actualizado! ";
        }else{
            return  " Os números de matrículas foram actualizado com sucesso! ";
        }

    }
    
    
    
    
    


    private function dataMatriculation(){

 
    $signal = '<>';
        
   return   $model = User::query()
     ->whereHas('roles', function ($q) {
         $q->whereIn('id', [6]);
     })
     ->join('matriculations as m','users.id','=','m.user_id')
     ->join('lective_year_translations as lyt','m.lective_year','=','lyt.lective_years_id')
     ->leftJoin('user_parameters as candidate', function ($join) {
         $join->on('users.id', '=', 'candidate.users_id')
             ->where('candidate.parameters_id', 311);
     })
     ->join('user_parameters as matricula', function ($join) {
         $join->on('users.id', '=', 'matricula.users_id')
             ->where('matricula.parameters_id',19);
     })        
     ->leftJoin('user_parameters as full_name', function ($join) {
         $join->on('users.id', '=', 'full_name.users_id')
             ->where('full_name.parameters_id', 1);
     })
     ->whereRaw($signal == '<>' ? 'LENGTH(matricula.value) <> ?' : 'LENGTH(matricula.value) = ?',[$request->tam ?? 9])
     ->where('m.course_year',1)
     ->where('lyt.active',1)
     ->whereNull('users.deleted_by')
     ->whereNull('m.deleted_by')
     ->whereIn('m.lective_year',[7,8])
     ->orderBy('users.created_at','DESC')
     ->select(
         'users.id',
         'users.name',
         'users.email',
         'm.course_year',           
         'm.code as corfirm',           
         'matricula.value as code_matricula',
          DB::raw('LENGTH(matricula.value) as tamanho')
     )
     ->distinct('users.id')
     ->get()
     ->map(function($item){
        
        $finalNumber = substr($item->corfirm, -4);
        $InitialNumber= substr($item->code_matricula,0,5);
        $newNumber=$InitialNumber.$finalNumber;

        $user = User::find($item->id);

        $currentNumber = $user->parameters()->where('parameters.id', 19)->first();
        $currentNumber->pivot->value = $newNumber;
        $currentNumber->pivot->save();

        return $aluno = [
                     'id'=>$item->id,
                     'confirm'=>$item->corfirm,
                     'matricula_antiga'=>$item->code_matricula,
                     'newNumber'=>$newNumber,
                     'initial'=>$InitialNumber,
                     'final'=>$finalNumber,
                     "usuario"=>$user
              ];

       });

    }
    



    
    
    
    
    
    
    
   public function index(){
        return view("Users::candidate.matricula_incorreta.index");
    }

    public function ajax(Request $request){
     $signal = isset($request->signal) ? ($request->signal == 'EQUAL' ? '=' : '<>') : '<>';
    
     $model = User::query()
        ->whereHas('roles', function ($q) {
            $q->whereIn('id', [6]);
        })
        ->join('matriculations as m','users.id','=','m.user_id')
        ->join('lective_year_translations as lyt','m.lective_year','=','lyt.lective_years_id')
        ->leftJoin('user_parameters as candidate', function ($join) {
            $join->on('users.id', '=', 'candidate.users_id')
                ->where('candidate.parameters_id', 311);
        })
        ->join('user_parameters as matricula', function ($join) {
            $join->on('users.id', '=', 'matricula.users_id')
                ->where('matricula.parameters_id',19);
        })        
        ->leftJoin('user_parameters as full_name', function ($join) {
            $join->on('users.id', '=', 'full_name.users_id')
                ->where('full_name.parameters_id', 1);
        })
        ->whereRaw($signal == '<>' ? 'LENGTH(matricula.value) <> ?' : 'LENGTH(matricula.value) = ?',[$request->tam ?? 9])
        ->where('m.course_year',1)
        ->where('lyt.active',1)
        ->whereNull('users.deleted_by')
        ->whereNull('m.deleted_by')
        ->whereIn('m.lective_year',[7,8])
        ->orderBy('users.created_at','DESC')
        ->select(
            'users.id',
            'users.name',
            'users.email',
            'users.created_at',
            'users.updated_at',
            'm.course_year',           
            'matricula.value as code_matricula',
             DB::raw('LENGTH(matricula.value) as tamanho')
        )
        ->distinct()
        ->get();
        //return $model;
        return  Datatables::of($model)
        ->addColumn('actions', function ($item) {
            return view('Users::candidate.matricula_incorreta.datatables.actions')->with('item', $item);
        })  
        ->rawColumns(['actions'])
        ->addIndexColumn()
        ->toJson();
    }

}
