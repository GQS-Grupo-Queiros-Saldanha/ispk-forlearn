<?php

/**
 *    Users Helper
 */

use App\Modules\Users\Models\User;
use Illuminate\Database\QueryException;
use App\Modules\Payments\Models\Article;
use App\Modules\Payments\Models\ArticleRequest;
use App\Modules\Payments\Models\Transaction;
use Carbon\Carbon;

use App\Helpers\LanguageHelper;
use App\Modules\Users\Enum\RoleEnum;
use App\Modules\GA\Models\LectiveYear;
use App\Modules\Users\util\EnumVariable;
use App\Modules\Users\Models\Matriculation;
/**
 * @param Exception|Throwable|QueryException $e
 */

function users_exemplo($adim = true){
    if($adim && auth()->user()->hasRole('superadmin')) return [];

    $arrayExemplos = User::where('email','like','%forlearn%'.EnumVariable::$CONVERT_TO_EMAIL)
        ->get()->map(function($item){return $item->id;})->all();

    $arraySuper = User::query()->whereHas('roles', function($q) {
         $q->where('id', '=', RoleEnum::SUPERADMIN);
    })->get()->map(function($item){return $item->id;})->all();
   
    return array_merge($arrayExemplos, $arraySuper);
}


//Trás a contagem de notificações de cada usuário
function count_notification(){

    $count=DB::table('tb_notification')
       ->join('users as u', 'u.id', '=', 'tb_notification.sent_by')
       ->where('sent_to',Auth::user()->id)
       ->select(['tb_notification.*','u.email','u.name','u.image'])
       ->whereNull('tb_notification.state_read')
       ->whereNull('tb_notification.deleted_by')
       ->whereNull('tb_notification.deleted_at')
       ->orderBy('tb_notification.date_sent','DESC')
       ->get();
     return $count;
}

//Trás a contagem de notificações de cada usuário lidas e não lidas
function all_notification(){
          $aluno = auth()->user()->id;
    $userStudant = DB::table('users')->where('id', $aluno)->first();
                if ($userStudant != null) 
                    $estudanteNome = '%'.$userStudant->name .'%';


    $count=DB::table('tb_notification')
       ->join('users as u', 'u.id', '=', 'tb_notification.sent_by')
       ->where('sent_to',Auth::user()->id)
       ->where('tb_notification.body_messenge','like',$estudanteNome)
       ->select(['tb_notification.*','u.email','u.name','u.image'])
       ->whereNull('tb_notification.deleted_by')
       ->whereNull('tb_notification.deleted_at')
       ->orderBy('tb_notification.date_sent','DESC')
       ->paginate(10);
     return $count->withPath('?central-control=inbox');
}

//motor das notificações
function notification($icon,$subject,$body,$destinatario=[],$file,$cod_notification){
   
    $Myid=Auth::user()->id;
    $currentData = Carbon::now();
    if(count($destinatario)>0) {
        foreach($destinatario as $to){
            $save=DB::table('tb_notification')->insert([
                ['icon' =>  $icon, 
                'subject' => $subject,
                'body_messenge' => $body,
                'sent_by' => $Myid,
                'sent_to' => $to,
                'date_sent'=>$currentData,
                'file'=>$file
                ] 
            ]);
        }
    }

}
//Avaliar as notificações com enviada
function enviada(){

    $count=DB::table('tb_notification')
       ->join('users as u', 'u.id', '=', 'tb_notification.sent_to')
       ->where('sent_by',Auth::user()->id)
       ->select(['tb_notification.*','u.email','u.name'])
       ->orderBy('tb_notification.date_sent','DESC')
       ->whereNull('tb_notification.deleted_by')
       ->paginate(10);
     //->get();

     return $count->withPath('?central-control=sent');

}

//Avaliar as notificações com estrela
function estrelar($id_notification,$estado){

        $marcar = DB::table('tb_notification')
        ->where('sent_to', Auth::user()->id)
        ->where('id',(int) $id_notification)
        ->update(['star' => $estado]);  
}

//Deletar notificações
function deletar_restaurar_ler($id_notificacao=[],$config){
           $id[]= $id_notificacao; 
            $currentData = Carbon::now();
            switch ($config) { 

                case 'trash':
                   foreach($id_notificacao as $id_noty){
                       $deletar = DB::table('tb_notification')
                       ->where('id',(int) $id_noty)
                       ->where('sent_to', Auth::user()->id)
                       ->update(['deleted_by' => Auth::user()->id,'deleted_at'=>$currentData]);
                      }
                break; 


                case 'restaurar':
                    foreach($id_notificacao as $id_noty){
                    $restaurar = DB::table('tb_notification')
                    ->where('id', $id_noty)
                    ->where('sent_to', Auth::user()->id)
                    ->update(['deleted_by' => null,'deleted_at'=>null] );
                }
                break;


                case 'ler':
                    $let = DB::table('tb_notification')
                    ->whereIn('id', $id)
                    ->where('sent_to', Auth::user()->id)
                    ->update(['state_read' => 1]
                );
                break; 

                case 'count':
                    $count = DB::table('tb_notification')
                    ->whereNotnull('deleted_by')
                    ->whereNotnull('deleted_at')
                    ->where('sent_to', Auth::user()->id)
                    ->get();

                    return $count;
                break;

                case 'paginate':
                    $count = DB::table('tb_notification')
                    ->whereNotnull('deleted_by')
                    ->whereNotnull('deleted_at')
                    ->where('sent_to', Auth::user()->id)
                    ->paginate(10);
                    return $count;
                break;

                case 'favourite':
                   
                    $count=DB::table('tb_notification')
                    ->join('users as u', 'u.id', '=', 'tb_notification.sent_by')
                    ->where('sent_to',Auth::user()->id)
                    ->select(['tb_notification.*','u.email','u.name','u.image'])
                    ->whereNull('tb_notification.deleted_by')
                    ->whereNull('tb_notification.deleted_at')
                    ->orderBy('tb_notification.date_sent','DESC')
                    ->whereNotNull('tb_notification.star')
                    ->paginate(10);

                   return $count->withPath('?central-control=favourite');
                break;
                


                default:
                
                break;
            }

       

}



























function logError($e)
{
    // dd($e);
    if (config('telescope.enabled')) {
        Log::error($e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTrace(),
        ]);
    } else {
        Log::error($e);
    }
}

function studentsSelectList($roles = [6, 15], $disciplineId = null)
{
    $users = User::whereHas('roles', function ($q) use ($roles) {
        $q->whereIn('id', $roles);
    });

    if ($disciplineId) {
        $users = $users
            ->where(function ($query) use ($disciplineId) {
                $query
                    ->whereHas('matriculation.disciplines', function ($q) use ($disciplineId) {
                        $q->where('id', $disciplineId);
                    })
                    ->orWhereHas('disciplines', function ($q) use ($disciplineId) {
                        $q->where('id', $disciplineId);
                    });
            });
    }

    $users = $users
        ->with(['parameters' => function ($q) {
            $q->whereIn('code', ['nome', 'n_mecanografico']);
        }])
        ->get()
        ->map(function ($user) {
            $fullNameParameter = $user->parameters->firstWhere('code', 'nome');
            $fullName = $fullNameParameter && $fullNameParameter->pivot->value ?
                $fullNameParameter->pivot->value : $user->name;

            $studentNumberParameter = $user->parameters->firstWhere('code', 'n_mecanografico');
            $studentNumber = $studentNumberParameter && $studentNumberParameter->pivot->value ?
                $studentNumberParameter->pivot->value : "000";

            $displayName = "$fullName #$studentNumber ($user->email)";
            return ['id' => $user->id, 'display_name' => $displayName];
        });

    return $users ? $users->sortBy(function ($item) {
        return strtr(
            utf8_decode($item['display_name']),
            utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'),
            'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY'
        );
    }) : null;
}


















//Helper para auxilio de agendamento de estados


function aguarda_matricula(){

    $request="";
    $lectiveYears = LectiveYear::with(['currentTranslation'])
    ->get();

    $currentData = Carbon::now();
    $lectiveYearSelected = DB::table('lective_years')
    ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
    ->first();
   

    
            $model = Matriculation::join('users as u0', 'u0.id', '=', 'matriculations.user_id')
                ->join('users as u1', 'u1.id', '=', 'matriculations.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'matriculations.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'matriculations.deleted_by')
                ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'u0.id')
                ->join('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'uc.courses_id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })
        
                ->leftJoin('matriculation_classes as mc', 'mc.matriculation_id', '=', 'matriculations.id')
                ->join('classes as cl', function ($join) {
                    $join->on('cl.id', '=', 'mc.class_id');
                    $join->on('mc.matriculation_id', '=', 'matriculations.id');
                    $join->on('matriculations.course_year', '=', 'cl.year');
                })
        
        
                ->leftJoin('user_parameters as u_p', function ($join) {
                    $join->on('u0.id', '=', 'u_p.users_id')
                    ->where('u_p.parameters_id', 1);
                })
        
                //Estado do estudante
                ->leftJoin('users_states as u_state', 'u_state.user_id', '=', 'u0.id')
                ->leftJoin('states as states_studant', 'states_studant.id', '=', 'u_state.state_id')
        
                ->leftJoin('user_parameters as up_meca', function ($join) {
                    $join->on('u0.id', '=', 'up_meca.users_id')
                        ->where('up_meca.parameters_id', 19);
                })
                ->leftJoin('user_parameters as up_bi', function ($join) {
                    $join->on('u0.id', '=', 'up_bi.users_id')
                        ->where('up_bi.parameters_id', 14);
                })
        
                ->leftJoin('article_requests as art_requests', function ($join) {
                    $join->on('art_requests.user_id', '=', 'u0.id')
                        ->whereIn('art_requests.article_id', [117, 79]);
                })
        
                ->leftJoin('article_requests as art_reques', function ($join) {
                    $join->on('art_reques.user_id', '=', 'u0.id')
                        ->where('art_reques.year', "!=", null)
                        ->where('art_reques.month', "!=", null)
                        ->where('art_reques.discipline_id', "=", null);
                })
                ->select([
                    
                    'u0.id as id_usuario',
                    'states_studant.name as studant_state',
                    'states_studant.id as studant_state_id',
                    'u_state.state_id as id_state',
                    'u_state.occurred_at',
                    'uc.courses_id as id_curso'
                    

                    
                ])
        
                ->where('art_requests.deleted_by', null)
                ->where('art_requests.deleted_at', null)
                ->groupBy('u_p.value')
        
                ->distinct('id')
                ->get()
            ->map(function($item){

                Mudar_estado($item->id_usuario,$item->occurred_at,$item->id_state,$item->id_curso);
            });
    
            return $model;

    



}

function Mudar_estado($user_id,$date_current_historic,$estado_id,$curso){

    $currentData = Carbon::now();
    $getStudentSates=DB::table('users_states')
    ->where('users_states.user_id',$user_id)
    ->first();
    if(isset($getStudentSates->user_id)){
        $lectiveYearSelecte = DB::table('lective_years')
        ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
        ->first();
        if (strtotime($lectiveYearSelecte->start_date) <= strtotime($getStudentSates->occurred_at) && strtotime($lectiveYearSelecte->end_date) >=strtotime($getStudentSates->occurred_at)) {

        }else{
            DB::transaction(function () use ($user_id,$date_current_historic,$curso) {
                $currentData = Carbon::now();
        
                DB::table('users_states')->updateOrInsert(
                    [
                    'user_id' =>$user_id
                    
                    ]
                    ,
                    [   
                    'state_id' => (int) 4,
                    'occurred_at' => $currentData,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                    'created_at' => $currentData,
                    'updated_at' => $currentData,
                    'courses_id' => $curso
                        
                ]);



            });
        }
    }


    
}

function is_coordenador($user)
{
    $currentUserIsAuthorized = $user->hasAnyRole(['superadmin', 'staff_forlearn', 'staff_inscrições', 'staff_matriculas','rh_chefe','rh_assistente']);
    return !$currentUserIsAuthorized;
}





