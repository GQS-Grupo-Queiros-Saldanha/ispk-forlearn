<?php

namespace App\Modules\Users\Controllers;

use App\Helpers\LanguageHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\Classes;
use App\Modules\GA\Models\Course;
use App\Modules\GA\Models\StudyPlan;
use App\Modules\Payments\Models\Article;
use App\Modules\Payments\Models\DisciplineArticle;
use App\Modules\Users\Models\Matriculation;
use App\Modules\Users\Models\UserCandidate;
use App\Modules\Users\Models\User;
use App\Modules\Users\Models\UserState;
use App\Modules\Users\Models\UserStateHistoric;
use App\Modules\Users\Requests\MatriculationRequest;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;
use PDF;
use App\Modules\GA\Models\LectiveYear;
use App\Model\Institution;
use Illuminate\Support\Facades\Storage;
use Auth;

class StudioPhotografyController extends Controller
{
  
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
          
            $path="https://".$_SERVER['HTTP_HOST']."/users/avatar/";

          $student=DB::table('users')
          ->leftJoin('user_parameters as fotografia',function($q){
            $q->on('fotografia.users_id','=','users.id')
            ->where('fotografia.parameters_id',25); 
          })
          ->select('users.*','fotografia.value as fotografia')
          ->WhereNull('users.deleted_by')
          ->WhereNull('users.deleted_at')
          ->get();
            return view('Users::studio-photo.index', compact('lectiveYears', 'lectiveYearSelected','student','path'));
        }
        
        catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    
    public function savePhoto (Request $request)
    
    {   

         $id = explode("@",$request->user_id);

      $user=DB::table('users')
          ->leftJoin('user_parameters as fotografia',function($q){
            $q->on('fotografia.users_id','=','users.id')
            ->where('fotografia.parameters_id',25); 
          }) 
          ->leftJoin('user_parameters as fullname',function($q){
            $q->on('fullname.users_id','=','users.id')
            ->where('fullname.parameters_id',1); 
          }) 
          ->where('users.id',$id[0])
          ->WhereNull('users.deleted_by')
          ->WhereNull('users.deleted_at')
          ->select('users.*','fotografia.value as fotografia','fullname.value as fullname')
          ->first();
         
       
          
          if($request->ImageUpload!=null){
                $base64 = $request->ImageUpload;
                //obtem a extensão
                $extension = explode('/', $base64);
                $extension = explode(';', $extension[1]);
                $extension = '.'.$extension[0];

                //
                $dadosImage=$this->savePhotoHistory($user);
                //gera o nome
                $name = $dadosImage['nome'].$extension;

                //obtem o arquivo
                $separatorFile = explode(',', $base64);
                $file = $separatorFile[1];
                $path = 'attachment/';
                $filee=base64_decode($file);
                //  envia o arquivo
                $save=Storage::put($path.$name, base64_decode($file));
                //Caso salve a imagem no diretório
                if($save){
                $this->storeNewPhoto($dadosImage,$name);
                }
         }

         Toastr::success("A fotografia foi actualizada com sucesso!", __('toastr.success'));
         return redirect()->back();
          

    }









    private function savePhotoHistory($user)
    {
        
        $perfil =[
            "perfil"=>$user->image ?? null,
            "parameters"=> $user->fotografia ??null
        ] ;
        $estado=0;
        $parameterStatus=0;
      

        if($user->image!=null){
            $historic=DB::table('tb_studio_photo')
            ->updateOrInsert(
                ['image' =>  $user->image,
                'user_id'=> $user->id,
            ], [
                "type_image"=>"Profile"
             ]
          );
        
          $estado=1;
       }
        if($user->fotografia!=null){
            $historic=DB::table('tb_studio_photo')
            ->updateOrInsert(
                ['image' =>  $user->fotografia,
                'user_id'=> $user->id,
            ], [
                "type_image"=>"Parameters"
             ]
          );
          $parameterStatus=2;
       }



       $newName=$user->id."_".$user->name."_studio_forlearn_".time();
        
        return $dados = [
            "id"=>$user->id,
            "nome"=>$newName,
            "estado_Perfil"=>$estado,
            "estado_Parameter"=>$parameterStatus,
        ];

    }





    private function storeNewPhoto($dados,$fotoName)
    {
  
        
        if($dados['estado_Perfil']!=0){
            
            $user=User::find($dados['id']);
            $user->image=$fotoName;
            $user->save();
        }
        if($dados['estado_Parameter']!=0){

           $saveParamenter=DB::table('user_parameters')
            ->updateOrInsert(
                ['users_id' =>  $dados['id'],
                'parameters_id'=> 25,
            ], [
                "value"=>$fotoName
             ]
          );

        }
        if($dados['estado_Perfil']==0 && $dados['estado_Parameter']==0){

            $savePerfil=DB::table('users')
            ->updateOrInsert(
                ['id' =>  $dados['id']], ["image"=>$fotoName ]);

                $userParameter=User::find($dados['id']);
                
            //Parametro para aqueles que não têm nenhuma foto
          

                   $user_logado=Auth::user()->id;

                     //fotografia
                     $user_n_number[] = [
                        'parameters_id' => 25,
                        'created_by' => 1 ?? 0,
                        'parameter_group_id' =>1,
                        'value' => $fotoName
                    ];
    
                    $userParameter->parameters()->attach($user_n_number);
             }
    }



    public function grade_images_user(){
        try {
            
        
          
          $path="https://".$_SERVER['HTTP_HOST']."/users/avatar/";

          $student=DB::table('users')
          ->join('tb_studio_photo as st_photo','st_photo.user_id','=','users.id')
          ->leftJoin('user_parameters as fotografia',function($q){
            $q->on('fotografia.users_id','=','users.id')
            ->where('fotografia.parameters_id',25); 
          })
          ->select('users.*','fotografia.value as fotografia')
          ->WhereNull('users.deleted_by')
          ->WhereNull('users.deleted_at')
          ->distinct('users.id')
          ->get();

            return view('Users::studio-photo.grade-image', compact('student','path'));
        }
        
        catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }

    }


    public function grade_images($id_user){

        
        $student=DB::table('users')
        ->join('tb_studio_photo as st_photo','st_photo.user_id','=','users.id')
        ->leftJoin('user_parameters as fotografia',function($q){
          $q->on('fotografia.users_id','=','users.id')
          ->where('fotografia.parameters_id',25); 
        })
        ->select('users.name','st_photo.*' )
        ->WhereNull('users.deleted_by')
        ->WhereNull('users.deleted_at')
        ->Where('users.id',$id_user)
        ->distinct('st_photo.image')
        ->get();

         return response()->json($student);


    }



    public function delete_photo($id_foto){
        

          
       $foto=DB::table('users')
        ->join('tb_studio_photo as st_photo','st_photo.user_id','=','users.id')
        ->leftJoin('user_parameters as fotografia',function($q){
          $q->on('fotografia.users_id','=','users.id')
          ->where('fotografia.parameters_id',25); 
        })
        ->select('users.name','st_photo.*' )
        ->WhereNull('users.deleted_by')
        ->WhereNull('users.deleted_at')
        ->Where('st_photo.id',$id_foto)
        ->distinct('st_photo.image')
        ->first();

        if($foto){
          
            $path = 'attachment/';
            if(Storage::delete($path.$foto->image)){

                    $deleted = DB::table('tb_studio_photo')
                    ->Where('id',$id_foto)
                    ->delete();

                    Toastr::success("A fotografia foi eliminada com sucesso!", __('toastr.success'));
                    return redirect()->back();
             }else{
                    $deleted = DB::table('tb_studio_photo')
                    ->Where('id',$id_foto)
                    ->delete();
                    return redirect()->back();
             }
        }else{
                Toastr::success("A fotografia não encontrada!", __('toastr.success')); 
                redirect()->back();
           }


    }
    



    }

