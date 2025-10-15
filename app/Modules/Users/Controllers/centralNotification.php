<?php

namespace App\Modules\Users\Controllers;
use App\Modules\Users\Models\WhatsappForlearn;

use App\Helpers\LanguageHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Users\Models\Matriculation;
use App\Modules\Users\Models\User;
use Illuminate\Support\Facades\DB;
use App\Modules\GA\Models\LectiveYear;
use App\Modules\Avaliations\Models\AvaliacaoAluno;
use App\Modules\Avaliations\Models\Avaliacao;
use App\Modules\Avaliations\Models\GradePath;
use App\Modules\Avaliations\Models\PlanoEstudoAvaliacao;
use App\Modules\GA\Models\Course;
use App\Modules\GA\Models\Discipline;
use App\Modules\Avaliations\Models\Avaliations;
use App\Modules\GA\Models\StudyPlan;
use App\Modules\GA\Models\StudyPlanEdition;
use App\Modules\Payments\Models\Article;
use App\Modules\Payments\Models\ArticleRequest;
use App\Modules\Payments\Models\Transaction;
use App\Modules\Users\Models\TranferredStudent;
use App\Modules\Users\Models\TransferredStudent;
use App\Modules\Users\Models\UserState;

use Brian2694\Toastr\Facades\Toastr;
use App\Modules\Avaliations\Models\TipoAvaliacao;
use App\Modules\Avaliations\Models\TipoMetrica;
use Exception;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Mail;
use App\Model\Institution;
use App\Mail\OrderShipped;
use App\Modules\Users\util\EnumVariable;
     
use GuzzleHttp\Client;


class centralNotification extends Controller
{
    
    public function generator_ticker(){
        $currentData = Carbon::now();
        $institution = Institution::latest()->first();
        $ticket = DB::table('generator_ticket')->orderBy('id','desc')->first();
        $lectiveYear = DB::table('lective_years')->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
         ->first();
        $year = Carbon::parse($lectiveYear->start_date)->format('y');
        $exist = DB::table('generator_ticket')
                ->where("year_id",$lectiveYear->id)->exists();
        if(!$exist || !isset($ticket->id)){
            $code = $institution->abrev.$year."0001";
        }else{
            $prefix = $institution->abrev.$year;
            $tam = strlen($institution->abrev)+strlen($year);
            $seq = substr($ticket->code, $tam);
            $number = (10000 + ($seq + 1 ))."";
            $next = substr($number, 1);
            $code = $prefix.$next;
        }
        return response()->json(["code" => $code]);
    }
    
    private function fetch($apoio = false){
        try {
        $email_apoio = EnumVariable::$EMAIL_APOIO;
        
        $notificacao=all_notification();
        
            
        $lectiveYears = LectiveYear::with(['currentTranslation'])
        ->get();
        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
         ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
         ->first();
        //Todos usuario Para mandar sms
        $user=DB::table('users')
        ->leftJoin('user_parameters as full_name', function ($join) {
            $join->on('users.id', '=', 'full_name.users_id')
           ->where('full_name.parameters_id', 1);
        })
        ->join('model_has_roles as usuario_cargo', 'users.id', '=', 'usuario_cargo.model_id')
        ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')
        ->where('usuario_cargo.role_id',"!=",2)
        ->where('users.id','!=',Auth::user()->id)
        ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
        ->whereNull('users.deleted_at')
        ->select(['users.id','users.name','users.email','full_name.value'])
        ->distinct('users.id');
        
        if($apoio){
           // $user->where('users.email',$email_apoio);
        }
        
        if(auth()->user()->hasRole("student")){
           $user->where('usuario_cargo.role_id',"!=",6);
        }        
        $users = DB::table('users')->select('id', 'name', 'email', 'user_whatsapp')->get();
        $user = $user->get();
         
        $lectiveYearSelected = $lectiveYearSelected->id ?? 9;
        $Avaliacao= Avaliacao::all();
        $institution = Institution::latest()->first();
        return view('Users::central-notification.index', compact(
            'lectiveYears',
            'lectiveYearSelected',
            'Avaliacao',
            'notificacao',
            'user',
            'users',
            'institution',
            'apoio',
            'email_apoio'));
        }catch (Exception | Throwable $e) {
            Log::error($e);
            Toastr::error($e->getMessage(), __('toastr.error'));
            return redirect()->back();
        }
    }
    
    public function index()
    {
        return $this->fetch();
    }

    public function apoio_notification(){
        return $this->fetch(true);
    }

    public function getCreate($id_anolectivo)
    {
           
    }

    public function create()
    {
     
        try {
       
           
        } catch (Exception | Throwable $e) {
            Log::error($e);
            Toastr::error($e->getMessage(), __('toastr.error'));
            return redirect()->back();
        }
        
    }


    public function marcar_estrela(Request $request)
    {
     
        try {
            $sms = DB::table('tb_notification')
            ->where('sent_to', Auth::user()->id)
            ->where('id',(int) $request->id)
            ->first(); 

            $dado="";
            if($sms){
                $estado= $sms->star==null?1:null;
                $dado=$sms->star==null?1:2;
                estrelar($request->id,$estado);
            }else{
                $dado=0;
            }

           return response()->json($dado);
           
        } catch (Exception | Throwable $e) {
            Log::error($e);
            Toastr::error($e->getMessage(), __('toastr.error'));
            return redirect()->back();
        }
        
    }
     
    public function ajaxCalendarie()
    {
        try {

            $currentData = Carbon::now();
            $lectiveYear = DB::table('lective_years')
            ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
            ->first();

          $model = DB::table('calendario_prova as cl')
                ->join('users as u1', 'u1.id', '=', 'cl.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'cl.updated_by')
                ->leftJoin('discipline_periods as pr', 'pr.id', '=', 'cl.simestre')
                ->leftJoin('discipline_period_translations as dt', function ($join) {
                    $join->on('dt.discipline_periods_id', '=', 'pr.id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                    })
                ->select([
                    'cl.*',
                    'cl.code as code',
                    'cl.id as id_calendario',
                    'cl.simestre',
                    'dt.display_name as periodo',
                    'cl.display_name as name',
                    'cl.date_start as data_inicio',
                    'cl.data_end as date_fim',
                    'u1.name as us_created_by',
                    'u2.name as us_updated_by'
                 ])
                 ->where('cl.deleted_by','=',null)
                //  ->where('cl.deleted_at',null)
                 ->distinct('name')
                 ->get();
               

            return Datatables::of($model)
                ->addColumn('actions', function ($item) {
                    return view('Avaliations::calendario-prova.datatable.action')->with('item', $item);
                })              
                ->rawColumns(['actions'])
                ->addIndexColumn()
                ->toJson();
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }


    public function show($id)
    {
       
       $notificacao=all_notification();

        $lectiveYears = LectiveYear::with(['currentTranslation'])
        ->get();
        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
         ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
         ->first();
        //Todos usuario Para mandar sms
        $user=DB::table('users')
        ->leftJoin('user_parameters as full_name', function ($join) {
            $join->on('users.id', '=', 'full_name.users_id')
           ->where('full_name.parameters_id', 1);
        })
        ->where('users.id','!=',Auth::user()->id)
        ->whereNull('users.deleted_at')
        ->select(['users.id','users.name','users.email','full_name.value'])
        ->distinct('users.id')
        ->get();
         
        $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
        $Avaliacao= Avaliacao::all();

        
        
        $singleNotification=DB::table('tb_notification as n')
        ->join('users as u','u.id','n.sent_by')
        ->where('n.id',$id)
        ->where('n.sent_to', Auth::user()->id)
        ->whereNull('n.deleted_by')
        ->select(['n.*','u.email','u.name','u.id as id_user','u.image'])
        ->first();
        $central_control="smsSingle";

      

        if($singleNotification){
            return view('Users::central-notification.index', compact('central_control','singleNotification','Avaliacao','notificacao','user'));
        }else{
            //enviado Para
            $singleNotification=DB::table('tb_notification as n')
                 ->join('users as u','u.id','n.sent_to')
                 ->where('n.id',$id)
                 ->where('n.sent_by', Auth::user()->id)
                 ->whereNull('n.deleted_by')
                 ->select(['n.*','u.email','u.name','u.id as id_user','u.image'])
                 ->first();
         if($singleNotification){
                     //é uma mensagem enviada
                     $central_control="to";
                    
                     
        }else{
            //ver da lixeira
            $singleNotification=DB::table('tb_notification as n')
                    ->join('users as u','u.id','n.sent_by')
                    ->where('n.id',$id)
                    ->where('n.sent_to', Auth::user()->id)
                    ->whereNotNull('n.deleted_by')
                    ->select(['n.*','u.email','u.name','u.id as id_user','u.image'])
                    ->first();
                        
                    if($singleNotification){

                        $central_control="trash_view";
                    }else{
                        
                        // $central_control="without";

                    }


            }

        return view('Users::central-notification.index', compact('central_control','singleNotification','Avaliacao','notificacao','user'));

         }

      
      
        
    }


    public function edit($id)

    {
    
         try {
            $tb_calendario= DB::table('calendario_prova as cl')->where('cl.id',$id)
            ->leftJoin('discipline_periods as pr', 'pr.id', '=', 'cl.simestre')
            ->leftJoin('discipline_period_translations as dt', function ($join) {
                $join->on('dt.discipline_periods_id', '=', 'pr.id');
                $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dt.active', '=', DB::raw(true));
                })
                -> select(['cl.*','dt.display_name as simestre_nome'])
               ->get();
                 
                $date=[
                    'menu_activo'=>$_GET['menu_avalicao'],
                    'action' => 'edit',
                    'tb_calendario'=>$tb_calendario
                ];
                return view('Avaliations::calendario-prova.calendario')->with($date);
           
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
         }
    }
     

    public function update(Request $request, $id)
    {
        try{ 
           
     //Amber Heard --deve ser demitida
     //Por tentar acusar o Jhonny Deep
     //de agreção.     
     $AQUAMAN=2;
     
    
                 
                
   } catch (Exception | Throwable $e) {
            Log::error($e);
            dd($e->getMessage());
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
        
    }

    public function store(Request $request)
    {
        if(!isset($request->page_type))
            switch ($request->canal) {

                case '0':
                    $this->SendforLEARN($request);
                    return redirect()->back();
                    break;

                case '1':
                    $this->SendWhatsapp($request);
                    return redirect()->back();
                    break;

                case '2':
                    if ($this->SendSMS($request)) {
                        $this->SendforLEARN($request);
                    }
                    return redirect()->back();
                    break;
            }
        
    }
    public function SendSMS(Request $request){
        try{  
            $id_user = auth()->user()->id ?? 0;
            // if(!in_array($id_user,[10166])){
            //     Toastr::success(__('Em manutenção'), __('toastr.warning'));
            //     return redirect()->back();
            // }
            $email_apoio = EnumVariable::$EMAIL_APOIO;          
            //Método de fazer o envio de mensagem 
            //return $request;
            $icon="fas fa-envelope";
            //Teoria  
            $to=$request->to ?$request->to:$request->studant;
            //Request->Ruanda
            $type="";
            if($to=="studant"){
            //Pegar id dos usuário com cargo Apoio ao estudante
            //e enviar notificações para todos eles.
        
            $to = User::whereHas('roles', function ($q) {
                    $q->whereIn('id', [49]);
                }) ->leftJoin('user_parameters as u_p9', function ($q) {
                    $q->on('users.id', '=', 'u_p9.users_id')
                    ->where('u_p9.parameters_id', 1);
                })
                ->get()
                ->map(function($item,$key){
                    return $item->users_id;
                }); 
            $type="[Apoio ao estudante] ";

            }

            //
            $subject=$type.$request->subject;
            $body=$request->body;
            
            //Este método está no helper user 
            $apoio = User::where(['email' => $email_apoio])->first();
            $is_msg = isset($apoio->id) && in_array($apoio->id, $to);
            
            if($is_msg){
                $array = [];
                
                $currentData = Carbon::now();
                $lectiveYear = DB::table('lective_years')->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')->first();
                
                if(!isset($lectiveYear->id)){
                    $lectiveYear = DB::table('lective_years')->orderBy('id','DESC')->first();
                }
                
                
                DB::table('generator_ticket')->insert([
                    "code" => $request->ticket,
                    "year_id" => $lectiveYear->id,
                    "user_id" => Auth::user()->id,
                    "created_by" => Auth::user()->id,
                    "created_at" => Carbon::now()
                ]);
                
            }
            if(!$is_msg)
                $notify = notification($icon,$subject,$body,$to,null,null);
            Toastr::success(__('Mensagem enviada com sucesso'), __('toastr.success'));
            return redirect()->back();
         } catch (Exception | Throwable $e) {
            Log::error($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

   public function destroy(Request $request)
    {
        try{

        Toastr::success(__('Eliminado com sucesso'), __('toastr.success'));
        return redirect()->back();
     }
     catch (Exception | Throwable $e) {
        Log::error($e);
        dd($e->getMessage());
        return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
    }



    
    }


    public function apagar_notificacao(Request $request)
    {
        try{
        if(isset($request->deletar)){
            //helper notificaticação-User
            deletar_restaurar_ler($request->deletar,"trash");
            Toastr::success(__('Eliminado com sucesso'), __('toastr.success'));
            return redirect()->back();
        } else{

            Toastr::warning(__('Atenção! selecione uma notificação antes de tentar excluir.'), __('toastr.warning'));
            return redirect()->back();

        }   
     }
     catch (Exception | Throwable $e) {
        Log::error($e);
        dd($e->getMessage());
        return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
    }
    }





    public function pesquisar_notificacao(Request $request)
    {
     try{
        
            $busca=$request->pesquisa;
            
            $busca=DB::table('tb_notification as n')
            ->join('users as u','u.id','n.sent_by')
            ->where('n.subject','LIKE','%'.$busca.'%')
            ->where('n.sent_to', Auth::user()->id)
            ->whereNull('n.deleted_by')
            ->select(['n.*',''])
            ->get();



        return response()->json($busca); 
        
      }
      
        catch (Exception | Throwable $e) {
        Log::error($e);
            dd($e->getMessage());
        return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }

        }





    public function singleSms($id){
     try{
            
            // $busca="$request->pesquisa";
            
            $busca=DB::table('tb_notification as n')
            ->join('users as u','u.id','n.sent_by')
            ->where('n.subject','LIKE','%'.$busca.'%')
            ->where('n.sent_to', Auth::user()->id)
            ->whereNull('n.deleted_by')
            ->select(['n.*',''])
            ->get();



        return response()->json($busca); 
        
      }
      
        catch (Exception | Throwable $e) {
        Log::error($e);
            dd($e->getMessage());
        return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
    private function SendWhatsapp($request) {
        // Validação dos dados
        $validated = $request->validate([
            'canal' => 'required|in:0,1,2',
            'to' => 'required|array',
            'subject' => 'required_if:canal,0',
            'body' => 'required|string',
        ]);

        // Processamento dos dados para o bd
        foreach ($validated['to'] as $userId) {
            WhatsappForlearn::create([
                'whatsapp_to' => $userId,
                'whatsapp_body' => $validated['body'],
                'whatsapp_of_number' => '@forlearn',
                'created_by' => auth()->id(),
            ]);
        }
        //processamento dos dados para api
         $this->submeterMensagemWhatsapp($userId, $validated['body']);

        Toastr::success('Mensagem Enviada com sucesso!');
        return redirect()->back();        
    }
    
    public function submeterMensagemWhatsapp($userId, $body){
        $url = "https://waapi.app/api/v1/instances/68035/client/action/send-message";

        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->request('POST', $url, [
                'headers' => [
                    'Authorization' => 'Bearer cfBUVLVthm81GUOFcIxLH3vdK3lzHVaSi2RbVaBv50fe7c76',
                    'Content-Type'  => 'application/json',
                    'Accept'        => 'application/json',
                ],
                'json' => [
                    'chatId'  => '244' . $userId . '@c.us',
                    'message' => $body,
                ],
            ]);

            // Verifica se o status da resposta é 2xx
            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                // A chamada foi bem sucedida
                return json_decode($response->getBody(), true); // ou apenas true se não precisares da resposta
            } else {
                return "Erro no envio: " . $response->getStatusCode() . ' - ' . $response->getBody();
            }
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // Em caso de erro na requisição
            if ($e->hasResponse()) {
                return "Erro na requisição: " . $e->getResponse()->getStatusCode() . ' - ' . $e->getResponse()->getBody();
            }
            return "Erro na requisição: " . $e->getMessage();
        }
    }
}

    




