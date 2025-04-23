<?php

/**
 *	Grade Helper
 */

use App\Modules\Users\Models\User;
use Illuminate\Database\QueryException;
use App\Modules\Payments\Models\Article;
use App\Modules\Payments\Models\ArticleRequest;
use App\Modules\Payments\Models\Transaction;
use Carbon\Carbon;
/**
 * @param Exception|Throwable|QueryException $e
 */

//Trás a contagem de notificações de cada usuário
function count_notificationApp($id){

        $count=DB::table('tb_notification')
            ->join('users as u', 'u.id', '=', 'tb_notification.sent_by')
            ->where('sent_to',$id)
            ->select(['tb_notification.*','u.email','u.name','u.image'])
            ->whereNull('tb_notification.state_read')
            ->whereNull('tb_notification.deleted_by')
            ->whereNull('tb_notification.deleted_at')
            ->orderBy('tb_notification.date_sent','DESC')
            ->get();
            
        return $count;
}

//Trás a contagem de notificações de cada usuário lidas e não lidas
function all_notificationApp($id){

    $count=DB::table('tb_notification')
       ->join('users as u', 'u.id', '=', 'tb_notification.sent_by')
       ->where('sent_to',$id)
       ->select(['tb_notification.*','u.email','u.name','u.image'])
       ->whereNull('tb_notification.deleted_by')
       ->whereNull('tb_notification.deleted_at')
       ->orderBy('tb_notification.date_sent','DESC')
       ->get();
     return $count;
}
function deletar_restaurar_ler_notify($id_user,$id_notificacao=[],$config){
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
             ->update(['state_read' => 1]);
    
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