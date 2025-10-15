<?php

namespace App\Modules\RH\Controllers;


use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;


use Cache;
use App\Helpers\LanguageHelper;
use App\Modules\Cms\Models\Language;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Users\Models\Role;
use App\Modules\GA\Models\DayOfTheWeek;
use App\Modules\GA\Models\Schedule;
use App\Modules\GA\Models\ScheduleType;

use App\Modules\Users\Models\User;
use App\Modules\GA\Models\LectiveYear;
use Yajra\DataTables\Facades\DataTables as YajraDataTables;
use Carbon\Carbon;
use DataTables;
use DB;
use Exception;
use Illuminate\Http\Response;
use Log;
use Throwable;
use Toastr;
use Auth;
use App\Model\Institution;
use LynX39\LaraPdfMerger\Facades\PdfMerger;
use PDF;
use Illuminate\Support\Facades\Storage;
use App\Modules\Users\Controllers\MatriculationController;
use App\Modules\Cms\Controllers\mainController;


class WhatsappBotApi extends Controller
{

    public function login(Request $request)
    {

        try {

            
          
                // Autenticação bem-sucedida
                $user = DB::table('users')
                    ->Join('user_courses', 'users.id', '=', 'user_courses.users_id')
                    ->Join('courses', 'user_courses.courses_id', '=', 'courses.id')
                    ->join('courses_translations as ct', function ($join) {
                        $join->on('ct.courses_id', '=', 'courses.id');
                        $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('ct.active', '=', DB::raw(true));
                    })

                    ->leftJoin('user_parameters as fullname', function ($join) {
                        $join->on('users.id', '=', 'fullname.users_id')
                            ->where('fullname.parameters_id', 1);
                    })
                   
                    ->leftJoin('matriculations as mat', function ($join) {
                        $join->on('users.id', '=', 'mat.user_id');
                    })
                    ->leftJoin('matriculation_classes as mat_classe', function ($join) {
                        $join->on('mat.id', '=', 'mat_classe.matriculation_id')
                        ->join("classes as turma",'mat_classe.class_id','turma.id');
                    })
                    ->leftJoin('user_parameters as matriculaNumber', function ($join) {
                        $join->on('users.id', '=', 'matriculaNumber.users_id')
                            ->where('matriculaNumber.parameters_id', 19);
                    })
                    ->leftJoin('user_parameters as bi', function ($join) {
                        $join->on('users.id', '=', 'bi.users_id')
                            ->where('bi.parameters_id', 14);
                    })
                    ->leftJoin('user_parameters as telefone', function ($join) {
                        $join->on('users.id', '=', 'telefone.users_id')
                            ->where('telefone.parameters_id', 36);
                    })
                    ->leftJoin('user_parameters as whatsapp', function ($join) {
                        $join->on('users.id', '=', 'whatsapp.users_id')
                            ->where('whatsapp.parameters_id', 39);
                    })
                    ->leftJoin('user_parameters as emailPessoal', function ($join) {
                        $join->on('users.id', '=', 'emailPessoal.users_id')
                            ->where('emailPessoal.parameters_id', 34);
                    })
                    ->leftJoin('user_parameters as pai', function ($join) {
                        $join->on('users.id', '=', 'pai.users_id')
                            ->where('pai.parameters_id', 23);
                    })
                    ->leftJoin('user_parameters as mae', function ($join) {
                        $join->on('users.id', '=', 'mae.users_id')
                            ->where('mae.parameters_id', 24);
                    })
                    ->leftJoin('courses as c.', function ($join) {
                        $join->on('users.id', '=', 'matriculaNumber.users_id')
                            ->where('matriculaNumber.parameters_id', 19);
                    })
                    ->leftJoin('user_parameters as fotografia', function ($join) {
                        $join->on('users.id', '=', 'fotografia.users_id')
                            ->where('fotografia.parameters_id', 25);
                    })
                    ->select(['fullname.value as fullname','turma.display_name as turma','users.id as id_user','mae.value as mae','pai.value as pai','telefone.value as telefone','emailPessoal.value as email_pessoal','whatsapp.value as whatsapp','ct.display_name as course','mat.course_year as curricular','bi.value as bi','users.email', 'users.name','users.credit_balance', 'matriculaNumber.value as matricula', 'fotografia.value as image'])
                    ->where('whatsapp.value', $request->phone)
                    ->orderBy('mat.course_year','DESC')
                    ->first();
               
                    $host = "http://" . $_SERVER['HTTP_HOST'];
                    $imageUrl = "";

                    if (is_object($user) && property_exists($user, 'image')) {
                        $imageUrl = $host . "/storage/attachment/" . $user->image;
                    } 
                    
                   if($user){
                       return response()->json(['status' => "sucesso", 'user_secret' => base64_encode(1), 'user' => $user,'imageUrl' => $imageUrl]);
                   }
                     return response()->json(["Mensagem"=>"Erro ao localizar utilizador" ]);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return $e;
            // return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    
    # Start payments


    public function getCurrentAccount($student){
        
        $articles = $this->verify_CurrentAccount($student);
        
        
        if(isset($articles['payments']) && (count($articles['payments'])>0)){
            
            $institution = Institution::latest()->first(); 

           

            $student_info = $this->get_information_student($student);

            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
            ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
            ->first();
            

            $articles = $articles['payments'];
            $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
            
            $pdf = PDF::loadView("RH::whatsapp.current_account",compact('articles','institution','student_info'))
                    ->setOption('margin-top', '2mm')
                    ->setOption('margin-left', '2mm')
                    ->setOption('margin-bottom', '13mm')
                    ->setOption('margin-right', '2mm')
                    ->setOption('footer-html', $footer_html)
                        ->setPaper('a4');   

            return $pdf->stream('conta_corrente_'.$student_info->matricula.'_'
            .$student_info->full_name.'_'
            .$student_info->lective_year.'.pdf');
            
        }else{
            return response()->json(["error"=>"Matrícula não encontrada"]);

        }
    }
    public function verify_CurrentAccount($student)
    {

        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
        ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
        ->first();
        if(isset($lective_year)){
            $lectiveYearSelected = $lective_year;
        }else{
            $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
        } 

            
               

        $payments = DB::table("article_requests as ar")
        ->join("articles as art","art.id","ar.article_id")
        ->join("article_translations as art_t","art_t.article_id","art.id")
        ->where("ar.user_id", $student)
        ->whereNull("ar.deleted_at")
        ->whereNull("art.deleted_at")
        ->where("art_t.active",1)
        ->where("art.anoLectivo",$lectiveYearSelected)
        ->where("art_t.language_id",1) 
        ->select(["ar.id","art_t.display_name","ar.base_value","ar.status","ar.month","ar.year"])
        ->orderBy("ar.status","asc")
        ->get(); 



        
 
        $payments = collect($payments)->map(function($item,$key){
            $month = [
                "",
                "Janeiro",
                "Fevereiro",
                "Março",
                "Abril",
                "Maio",
                "Junho",
                "Julho",
                "Agosto",
                "Setembro",
                "Outubro",
                "Novembro",
                "Dezembro"
              ];

           if(isset($item->month) && ($item->month>0)){
             
               $item->mes = " (".$month[(int) $item->month]." ".$item->year.")";

           }

           if($item->status=="total"){
                $item->recibo = $this->get_recibo($item->id);
           }

           return $item;
        });



        $config_divida = DB::table("config_divida_instituicao")
        ->where("status","ativo")
        ->whereNull("deleted_at")
        ->select(["qtd_divida","dias_exececao"])
        ->first();
        
        $count = collect($payments)->groupBy("status")->map(function($item,$key) use($config_divida){ 
        //    return count($item);
        $i = null;
       
            if($key=="pending"){
                foreach ($item as $mensalidade) {
                    if(isset($mensalidade->year) && ($mensalidade->year>0) ){
                        $hoje = Carbon::create(date("Y-m-d"));
                        $limite = Carbon::create($mensalidade->year."-".$mensalidade->month."-".$config_divida->dias_exececao);
                         
                        if($hoje>=$limite){
                            ++$i;
                        }
                    }else{
                        $i++;
                    }
                }
                return $i;
            }else{
                return count($item);
            }

        });





        $dividas = collect($payments)->groupBy("status")->map(function($item,$key) use ($config_divida){ 

        $i = null;
       
            if($key=="pending"){
                foreach ($item as $mensalidade) {
                    if(isset($mensalidade->year) && ($mensalidade->year>0) ){
                        $hoje = Carbon::create(date("Y-m-d"));
                        $limite = Carbon::create($mensalidade->year."-".$mensalidade->month."-".$config_divida->dias_exececao);
                        if($hoje>=$limite){
                            
                            ++$i;
                        }
                    }
                }
            }
            if($config_divida->qtd_divida<$i){
                return $i;
            }
            
        });

        return [
            "payments" => $payments,
            "count" => $count,
            "dividas" => $dividas,
        ];
    }
    public function get_recibo($articles)
    {
        // Pegando os dados completos da referência

        $transacao=DB::table('transaction_info as tinfo')
        ->leftJoin('transaction_receipts as tr','tr.transaction_id',"=","tinfo.transaction_id")
        ->leftJoin('transaction_article_requests as tar','tar.transaction_id',"=","tinfo.transaction_id")
        ->leftJoin('banks as bk','bk.id',"=","tinfo.bank_id")
        ->leftJoin('article_requests as ar','ar.id',"=","tar.article_request_id")
        ->leftJoin('user_parameters as up','up.users_id',"=","ar.user_id")
        ->leftJoin('article_translations as at','at.article_id',"=","ar.article_id")
        ->leftJoin('articles as art','art.id',"=","ar.article_id")
        ->where('ar.id',$articles)
        ->where('up.parameters_id',1)
        ->where('at.active',1)
        ->select([
            "tinfo.reference as referencia",
            "ar.id as article_request_id",
            "tr.path as recibo",
             "at.display_name as emolumento" 
        ]) 
        ->get();
        
        
        $n_recibo = $transacao[0]->recibo;
        $rt = explode('-',explode('/',$n_recibo )[3]);
        

        $recibo = $rt[1]."-".explode('.',$rt[2])[0];

        return $recibo;
        
        

    }

    #end Payments

    public function get_information_student($student){


        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
        ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
        ->first();
        $lectiveYearSelected_id = $lectiveYearSelected->id ?? 6;

        

       return $model=DB::table("matriculations as mat")
        ->join("matriculation_classes as mat_class",'mat.id','mat_class.matriculation_id')
        ->join("classes as turma",'mat_class.class_id','turma.id')
        ->join("users as user",'mat.user_id','user.id')
        ->leftJoin('user_parameters as u_p', function ($join) {
            $join->on('user.id', '=', 'u_p.users_id')
            ->where('u_p.parameters_id', 1);
        })
       ->leftJoin('user_parameters as up_meca', function ($join) {
            $join->on('user.id','=','up_meca.users_id')
            ->where('up_meca.parameters_id', 19);
       }) 
       ->leftJoin('lective_year_translations as ly', function ($join) {
            $join->on('turma.lective_year_id','=','ly.lective_years_id')
            ->where('ly.active', 1)
            ->where('ly.language_id', 1);
       }) 
       ->leftJoin('courses_translations as ct', function ($join) {
            $join->on('turma.courses_id','=','ct.courses_id')
            ->where('ct.active', 1)
            ->where('ct.language_id', 1);
       }) 
 
      ->leftJoin('user_parameters as up_bi', function ($join) {
        $join->on('user.id','=','up_bi.users_id')
      ->where('up_bi.parameters_id', 14);
       })
      ->join("article_requests as user_emolumento",'user_emolumento.user_id','user.id')
      ->join("articles as article_emolumento",'user_emolumento.article_id','article_emolumento.id')
      ->join("code_developer as code_dev",'code_dev.id','article_emolumento.id_code_dev')
      ->whereIn('code_dev.code', ["confirm","p_matricula"])
      ->where('user_emolumento.status', "total")
      ->whereBetween('article_emolumento.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
      ->where("user.id", $student)
      ->select([
          'u_p.value as full_name',
          'up_meca.value as matricula',
          'user.email',
          'ct.display_name as course_name',
            'user_emolumento.status as pago',
            'article_emolumento.id as id_article',
            'article_emolumento.code as code_article',
            'turma.display_name as turma',
            'mat.code',
            'mat.id as id_matriculation',
            'up_bi.value as n_bi',
            'turma.lective_year_id as id_anoLectivo',
            'ly.display_name as lective_year',
            ])
        ->distinct(['up_bi.value','mat.code','u_p.value'])
        ->whereBetween('mat.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
        ->where("turma.lective_year_id",$lectiveYearSelected_id)    
        ->whereNull('mat.deleted_at')   
        ->first();
    }

    public function getMatriculations($student){
        

        $matriculations = $this->get_information_student($student);
      

        if(isset($matriculations->id_matriculation)){
            return MatriculationController::openReport($matriculations->id_matriculation);
        }else{
            return response()->json(["error"=>"Matrícula não emcontrada"]);
        }
    }


    public function getSchedules($student){
        
        
        
        $student_info = $this->get_information_student($student);
        
        $horario = mainController::schedule($student);
        $tempo = mainController::times($student); 
        
        if(!is_object($horario)){
            return response()->json(["error"=>"nenhum dado encontrado"]); 
        }
        
        $institution = Institution::latest()->first();  
        $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
        $pdf = PDF::loadView("RH::whatsapp.schedule",compact('horario','institution','student_info','tempo'))
        ->setOption('margin-top', '2mm')
        ->setOption('margin-left', '2mm')
        ->setOption('margin-bottom', '13mm')
        ->setOption('margin-right', '2mm')
        ->setOption('footer-html', $footer_html)
        ->setPaper('a4','landscape');   

        return $pdf->stream('Horário | '.$student_info->turma .
        ' | ' .$student_info->lective_year.'.pdf');

    }
    
    
    public function getGrades($matriculation){
       
        
        
        
        $matriculations = DB::table("matriculations")
       ->where("id",$matriculation)
       ->whereNull("deleted_at")
       ->select(["lective_year","id","user_id"])
       ->orderBy("lective_year","asc")
       ->first();

       $courses = DB::table("user_courses")
       ->where("users_id",$matriculations->user_id)
       ->select(["courses_id"])
       ->first(); 
       

       if(!isset($matriculations->lective_year)){
           return "Nenhuma matrícula encontrada neste ano lectivo";
       }
        
        
       $disciplines = mainController::get_disciplines($matriculations->lective_year,$matriculations->user_id);
       $percurso = BoletimNotas_Student($matriculations->lective_year, $courses->courses_id, $matriculations->id);  
       $articles =  mainController::get_payments($matriculations->lective_year,$matriculations->user_id); 
       $plano = mainController::study_plain($matriculations->lective_year,$matriculations->user_id); 
       $student_info = mainController::get_matriculation_student($matriculations->lective_year,$matriculations->user_id); 

       $matriculations = $student_info;
       
       $institution = Institution::latest()->first();  
       
       $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
       
       $pdf = PDF::loadView("Cms::initial.pdf.boletim",compact("percurso","articles","plano","matriculations","disciplines","student_info","institution","matriculations"))
       
       ->setOption('margin-top', '2mm')
       ->setOption('margin-left', '2mm')
       ->setOption('margin-bottom', '13mm')
       ->setOption('margin-right', '2mm')
       ->setOption('footer-html', $footer_html)
       ->setPaper('a4','landscape');    
       
       
       return $pdf->stream('Boletim | '.$student_info->matricula 
       .' | ' .
       $student_info->lective_year .'.pdf');

   }



}
