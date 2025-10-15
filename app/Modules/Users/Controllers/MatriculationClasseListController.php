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


class MatriculationClasseListController extends Controller
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
            //Curso 
        $courses = Course::with([
                'currentTranslation'
            ])->whereNull('deleted_by')
            ->get();

            return view('Users::list-class-matriculation.index', compact('lectiveYears', 'lectiveYearSelected','courses'));
        }
        
        catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    
    public function PegarDisciplina($id_curso,$anoCurricular)
    
    {
            //Disciplina --com plano de estudo
            return $dadosD=DB::table('study_plans as std_p')
            ->join('study_plans_has_disciplines as stp_d','stp_d.study_plans_id','std_p.id')
            ->join('study_plan_editions as stp_ed','stp_ed.study_plans_id','std_p.id')
            ->join('disciplines as disc','disc.id','stp_d.disciplines_id')
            ->join('disciplines_translations as dt', function ($join) {
                $join->on('dt.discipline_id', '=', 'disc.id');
                $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dt.active', '=', DB::raw(true));
            })
            ->select(['disc.id','dt.display_name','disc.code'])
            ->where('std_p.courses_id',$id_curso)
            ->where('stp_d.years',$anoCurricular)
            ->where('stp_ed.course_year',$anoCurricular)
            ->whereNull('disc.deleted_by')
            ->whereNull('disc.deleted_at')
            ->distinct()
            ->get();


    }



  public function ajaxUserDataPDF(Request $request) {   
  
  try{
      
     if(empty($request->classe)){
            Toastr::error(__('Verifique se selecionou uma turma antes de gerar o PDF.'), __('toastr.error'));
            return redirect()->back() ;
      } 
     $courses=DB::table('courses as curso')
        ->join('courses_translations as ct', function ($join) {
            $join->on('ct.courses_id', '=', 'curso.id');
            $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
            $join->on('ct.active', '=', DB::raw(true));
        })
        ->where('curso.id',$request->course)
        ->get();


        //Consulta do Ano Lectivo
        $lectiveYearSelectedP = DB::table('lective_years')
        ->where('id',$request->AnoLectivo)
        ->get();


        //Vai ser a consulta geral
  $model=DB::table('matriculation_classes as mat_class')
        ->join("matriculations as mat",'mat.id','mat_class.matriculation_id')
        ->join("matriculation_disciplines as mat_disc",'mat.id','mat_disc.matriculation_id')
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
 
      ->leftJoin('user_parameters as up_bi', function ($join) {
        $join->on('user.id','=','up_bi.users_id')
      ->where('up_bi.parameters_id', 14);
       })
       
       //Plano de estudo disciplina
       ->join("study_plans_has_disciplines as st_has_d", "st_has_d.disciplines_id", "=", "mat_disc.discipline_id")
       //Os que pagaram os emolumentos de confirmação de matricula e pré-matricula
      ->join("article_requests as user_emolumento",'user_emolumento.user_id','user.id')
      ->join("articles as article_emolumento",'user_emolumento.article_id','article_emolumento.id')
      ->join("code_developer as code_dev",'code_dev.id','article_emolumento.id_code_dev')
 
      ->whereIn('code_dev.code', ["confirm","p_matricula"])
      ->where('user_emolumento.status', "total")
      ->whereBetween('article_emolumento.created_at', [$lectiveYearSelectedP[0]->start_date, $lectiveYearSelectedP[0]->end_date])
      //fim dos pagos 
   
        ->select([
            'user_emolumento.status as pago',
            'article_emolumento.id as id_article',
            'article_emolumento.code as code_article',
            'turma.display_name as turma',
            'user.email',
            'mat.code',
            'up_meca.value as matricula',
            'up_bi.value as n_bi',
            'u_p.value as student',
            'turma.lective_year_id as id_anoLectivo',
            'mat_disc.matriculation_id',
            'mat_disc.exam_only',
            'mat.id as mat',
            'mat.course_year'

            ])

        ->orderBy('student','ASC')
        ->distinct(['up_bi.value','mat.code','u_p.value'])
        // ->whereBetween('mat.created_at', [$lectiveYearSelectedP[0]->start_date, $lectiveYearSelectedP[0]->end_date])
        ->where('mat.lective_year', $lectiveYearSelectedP[0]->id)
      
        ->where("turma.lective_year_id",$request->AnoLectivo)    
        ->where("turma.id",$request->classe)    
        ->whereNull('mat.deleted_at')   
        ->where("mat_disc.exam_only",$request->regime)    
        ->where("st_has_d.years",$request->curricular_year) 
        ->get();
        
       
        if($model->isEmpty()){
            Toastr::error(__('Não foram encontrado(s) aluno(s) matriculados na turma selecionada.'), __('toastr.error'));
            return redirect()->back(); 
         }

        $model = $model->filter(function($item)use($request){
            return $item->course_year == $request->curricular_year;
        });
       
        // return $model;
    
        //Validação se for vazio a lista de alunos
       if($model->isEmpty()){
           Toastr::error(__('Não foram encontrado(s) aluno(s) matriculados na turma selecionada.'), __('toastr.error'));
           return redirect()->back(); 
        }
        
        if(isset($request->status) && ($request->status=="0")){

          $model = collect($model)->map(function($item,$key){    
            $dividas = $this->get_payments($item->id_anoLectivo,$item->mat);
            if(isset($dividas) && ($dividas>0)){
                //verificar se é bolseiro
                if(
                    DB::table('scholarship_holder as hold')
                    ->where('hold.user_id',$item->id)
                    ->where('are_scholarship_holder',1)
                    ->join('scholarship_entity as ent',function($join){
                            $join->on('ent.id','hold.scholarship_entity_id')
                            ->where('type', 'BOLSA');
                            
                    })
                    ->exists()
                )
                return $item;

              
            }else{
                return $item;
            }
        });
      } 
        
        
         $regime = isset($request->regime)?$request->regime:0;
         $classe = DB::table("classes")
        ->where("id",$request->classe)
        ->select(["display_name"])
        ->first();
        
        
        $turmaC=$classe->display_name;
        $curso=$courses[0]->display_name;
       
       
          $data = [
                'regime'=>$regime,
                'model' => $model,
                'turmaC'=>$turmaC,
                'curso'=>$curso,
                'ano'=>$request->curricular_year
            ];    
        $ano=$request->curricular_year;

       $lectiveYears = LectiveYear::with(['currentTranslation'])
       ->where('id',$request->AnoLectivo)
       ->get();
        $anoLectivo = $lectiveYears[0]->currentTranslation->display_name;
        // view("Users::list-disciplines-matriculations.pdf_lista")->with($id_discipline);

        $institution = Institution::latest()->first();
        $titulo_documento = "LISTA DE MATRICULADOS POR TURMA";
        $anoLectivo_documento = "Ano Lectivo :";
        $documentoGerado_documento = "Documento gerado a";
        $documentoCode_documento = 1;

       $pdf = PDF::loadView("Users::list-class-matriculation.pdf_lista", compact(
             'model',
             'regime',
             'turmaC',
             'curso',
             'lectiveYears',
             'ano',
             'institution',
             'titulo_documento',
             'anoLectivo_documento',
             'documentoGerado_documento',
             'documentoCode_documento'
            ));
        
        
        $pdf->setOption('margin-top', '1mm');
        $pdf->setOption('margin-left', '1mm');
        $pdf->setOption('margin-bottom', '12mm');
        $pdf->setOption('margin-right', '1mm');
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('debug-javascript', true);
        $pdf->setOption('javascript-delay', 1000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);
        $pdf->setPaper('a4', 'landscape');
        
        $pdf_name="LdM_"."_".$anoLectivo.'_'.$courses[0]->code."_".$request->curricular_yea."_".$turmaC;
        // $footer_html = view()->make('Users::users.partials.pdf_footer', compact('institution'))->render();
        $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
        $pdf->setOption('footer-html', $footer_html);
        return $pdf->stream($pdf_name.'.pdf');
  }catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
       
      

  }




    public function turma($curso,$id_anoLectivo,$anoCurricular)
    {
        // return $curso. $id_anoLectivo .$anoCurricular;
   
        $turma=DB::table('classes as class')
        ->select(['class.id as id','class.display_name as turma'])
        ->where('class.courses_id',$curso)
        ->where('class.year',$anoCurricular)
        ->where('class.lective_year_id',$id_anoLectivo)
        ->whereNull('class.deleted_by')
        ->whereNull('class.deleted_at')
        ->get();


      return $turma;
        

    }



    public function store()
    {
      return $request;


    }
    
    
    public static function get_payments($lective_year,$matriculations){

      $payments = DB::table("matriculations as mat")
     ->join("article_requests as ar",'ar.user_id','mat.user_id')
     ->join("articles as art","art.id","ar.article_id")
     ->where("mat.id", $matriculations)
     ->where("art.anoLectivo",$lective_year)
     ->where("ar.status","pending")
     ->whereNotNull("ar.month")
     ->whereNotNull("ar.year")
     ->whereNull("art.deleted_at")
     ->whereNull("ar.deleted_at")
     ->select(["ar.base_value","ar.status","ar.month","ar.year"])
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
        
        return $item;
     });

      $config_divida = DB::table("config_divida_instituicao")
     ->where("status","ativo")
     ->whereNull("deleted_at")
     ->select(["qtd_divida","dias_exececao"])
     ->first();
     
    
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
           
           return isset($dividas["pending"])?$dividas["pending"]:null;

  }


    }

