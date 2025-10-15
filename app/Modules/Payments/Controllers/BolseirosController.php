<?php

namespace App\Modules\Payments\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Payments\Models\CurrentAccountObservations;
use App\Modules\Users\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;
use App\Helpers\LanguageHelper;
use Log;
use DataTables;
use Toastr;
use PDF;
use Carbon\Carbon;
use App\Model\Institution;
use App\Modules\GA\Models\LectiveYear;


class BolseirosController extends Controller
{
    public function reembolsos()
    {
        $lectiveYears = LectiveYear::with(['currentTranslation'])
        ->get();

            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();
            $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
            $data = [
                "students" => $this->ajax_bolseiros(),
                "lectiveYears" => $lectiveYears,
                "lectiveYearSelected" => $lectiveYearSelected
            ]; 
       return view("Payments::bolseiros.reembolso")->with($data);
    }


    private function gerar_codigo($repayment){
        
        $exist = DB::table('repayment')
        ->where('id', "=", $repayment)->select(["code"])->first();
        
        

        if($exist->code>0){
            return "Existe";
        }

        $last_code = DB::table('repayment')
        ->where('id','!=', $repayment)
        ->whereNotNull('code') 
        ->select("code","year")
        ->orderBy("id","desc")
        ->first();   
        
        
        
        if(isset($last_code->code) && ($last_code->year == date("Y"))){
            $code = 1+intval($last_code->code);
        }else{
            $code = 1;
        }
        
        DB::table('repayment')
        ->where('id', "=", $repayment)
        ->update([
            "code" => $code,
            "year" => date("Y")
        ]);
                    
        return $code;

    }


    public function report()
    {
    
        $lectiveYears = LectiveYear::with(['currentTranslation'])
        ->get();

            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();
            $lectiveYearSelected = $lectiveYearSelected->id ?? 6;

            return view('Payments::bolseiros.report', compact('lectiveYears', 'lectiveYearSelected'));

    }


    public function report_pdf(Request $request)
    {

                
                $DataInicio = $request->dataInicio;
                $DataFim = $request->dataFim;
                $date_from = Carbon::parse($DataInicio)->startOfDay();
                $date_to = Carbon::parse($DataFim)->endOfDay();
                $vetorCreditoAjuste = [];

                if ($DataInicio > $DataFim) {

                    Toastr::error(__('A data inicial não pode ser maior que a data final.'), __('Atenção!'));
                    return redirect()->route('listarRecibo');

                } elseif ($DataFim > date('Y-m-d') || $DataInicio > date('Y-m-d')) {

                    Toastr::error(__('As datas não podem ser maior que a data de hoje.'), __('Atenção!'));
                    return redirect()->route('listarRecibo');
                } elseif ($DataInicio == "" && $DataFim == "") {

                    Toastr::error(__('Os campos das datas não podem estar vazias.'), __('Atenção!'));
                    return redirect()->route('listarRecibo');

                } else {

                    $balance = DB::table('repayment')
                    ->leftjoin('users','repayment.user_id', '=', 'users.id')
                    ->leftjoin('users as u1','u1.id', '=', 'repayment.created_by')
                    ->leftjoin('user_parameters as u_p', function ($join) {
                       $join->on('repayment.user_id', '=', 'u_p.users_id')
                          ->where('u_p.parameters_id', 19);
                   }) 
                  ->leftJoin('user_parameters as u_p1', function ($join) {
                       $join->on('repayment.user_id', '=', 'u_p1.users_id')
                          ->where('u_p1.parameters_id', 1);
                   })
                   ->leftJoin('user_classes', 'user_classes.user_id', '=', 'users.id')
                   ->leftJoin('classes', 'classes.id', '=', 'user_classes.class_id')
                   ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'users.id')
                   ->leftJoin('courses_translations as ct', function ($join) {
                       $join->on('ct.courses_id', '=', 'uc.courses_id');
                       $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                       $join->on('ct.active', '=', DB::raw(true));
                   })
                   
                   ->where("repayment.lective_year", $request->lective_years)
                   ->select([
                       'users.id',
                       'users.name',
                       'users.email',
                       'u_p1.value as full_name',
                       'u_p.value as matriculation',
                       'ct.display_name as course',
                       'classes.code as turma',
                       'repayment.code',
                       'repayment.year',
                       'repayment.value',
                       'repayment.mode',
                       'repayment.iban',
                       'repayment.bank',
                       'repayment.date',
                       'repayment.created_at',
                       'u1.name as created_by',
                       'repayment.lective_year',
                       'repayment.observation',
                       ])
                   ->get();
                    
                     $tesoureiros = collect($balance)
                   ->groupBy('created_by')
                   ->map(function ($item, $key) {
                       $soma = $item->sum('value');
                       $count = count($item);
                       $resulatado = $count . "," . $soma;
                       return $resulatado;
                   });
                    //dados da instituição
                    $institution = Institution::latest()->first();
                    $titulo_documento = "FOLHA DE CAIXA [ Reembolsos ]";
                    $anoLectivo_documento = "Ano Lectivo :";
                    $documentoGerado_documento = "Documento gerado a";
                    $documentoCode_documento = 1;
                    //instaciando o PDF

                    $pdf = PDF::loadView("Payments::bolseiros.reembolso.pdf_reembolso", compact(
                        'balance',
                        'DataInicio',
                        'DataFim',
                        'institution',
                        'tesoureiros',
                        'titulo_documento',
                        'anoLectivo_documento',
                        'documentoGerado_documento',
                        'documentoCode_documento',
                    )
                    );
                    //Configuração
                    $pdf->setOption('margin-top', '1mm');
                    $pdf->setOption('margin-left', '1mm');
                    $pdf->setOption('margin-bottom', '12mm');
                    $pdf->setOption('margin-right', '1mm');
                    $pdf->setOption('enable-javascript', true);
                    $pdf->setOption('debug-javascript', true);
                    $pdf->setOption('javascript-delay', 1000);
                    $pdf->setOption('enable-smart-shrinking', true);
                    $pdf->setOption('no-stop-slow-scripts', true);
                    //  $pdf->setPaper('a4', 'portrait');  
                    $pdf->setPaper('a4', 'landscape');

                    //Nome do documento PDF
                    $pdf_name = "Folha de caixa [ Reembolsos ]";
                    //Rodapé do PDF
                    $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
                    $pdf->setOption('footer-html', $footer_html);
                    //Retornar o PDF
                    return $pdf->stream($pdf_name . '.pdf');


                }
           
    }
    public function create($id)
    {   
        
        
       $users = DB::table('users')
        ->leftJoin('user_parameters as u_p1', function ($join) {
            $join->on('users.id', '=', 'u_p1.users_id')
               ->where('u_p1.parameters_id', 1);
        }) 
        ->leftjoin('user_parameters as u_p', function ($join) {
            $join->on('users.id', '=', 'u_p.users_id')
               ->where('u_p.parameters_id', 19);
        })
        ->where("users.id", $id)
        ->select(['users.id','users.name','users.email','u_p1.value as full_name','u_p.value as matriculation','users.credit_balance'])
        ->first();

        if($users->credit_balance==0) {
            Toastr::warning(__('O estundate '.$users->name.' não possui saldo em carteira!'), __('toastr.warning'));
            return redirect()->back();
        }

       $data = [
           "id" => $id,
           "users" => $users,
       ];
       return view("Payments::bolseiros.create_reembolso")->with($data);
    }

   function ajax_bolseiros(){
    
        $bolseiros = DB::table("users")
        ->join('user_parameters as u_p', function ($join) {
             $join->on('users.id', '=', 'u_p.users_id')
                ->where('u_p.parameters_id', 19);
         })
        ->leftJoin('user_parameters as u_p1', function ($join) {
             $join->on('users.id', '=', 'u_p1.users_id')
                ->where('u_p1.parameters_id', 1);
         })
        ->join('user_courses', 'user_courses.users_id','=','users.id')
        ->leftJoin('courses as crs', 'crs.id', '=', 'user_courses.courses_id')
        ->leftJoin('courses_translations as ct', function ($join) {
            $join->on('ct.courses_id', '=', 'crs.id');
            $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
            $join->on('ct.active', '=', DB::raw(true));
        })  
        // ->where('scholarship_holder.are_scholarship_holder', 1)
        ->select('users.id','users.name','users.email','u_p1.value as full_name','u_p.value as matriculation')
        ->orderBy("users.name","asc")
        ->get();

        
        return $bolseiros;
         

    
    }
 
   public function ajax_reembolso($id)
   {
       try {
  
            $repayment = DB::table('repayment')
            ->leftjoin('users as u0', 'u0.id', "=", 'repayment.created_by')
            ->leftjoin('lective_year_translations as lt','lt.lective_years_id',"repayment.lective_year")
            ->whereNull('repayment.deleted_at')
            ->whereNull('repayment.deleted_by')
            ->whereNull('lt.deleted_at')
            ->where('lt.active',1)
            ->where('repayment.user_id',$id)
            ->select([
                "repayment.id",
                "repayment.code",
                "repayment.year",
                "repayment.value",
                "repayment.mode",
                "repayment.bank",
                "repayment.iban",
                "repayment.date",
                "repayment.credit_balance",
                "repayment.credit_balance_final",
                "repayment.observation",        
                'lt.display_name as lective_year',
                "repayment.created_at",
                "u0.name as created_by",
                ])
                ->orderBy("repayment.date","desc");
             


           return Datatables::queryBuilder($repayment)
               ->addColumn('actions', function ($item){
                   return view('Payments::bolseiros.datatable.actions', compact('item'));
               })
               ->addColumn('mode', function ($item){
                   return view('Payments::bolseiros.datatable.mode', compact('item'));
               })
               ->addColumn('code', function ($item){
                   return view('Payments::bolseiros.datatable.code', compact('item'));
               })
               ->addColumn('value', function ($value){
                   return view('Payments::bolseiros.datatable.money', compact('value'));
               })
               ->addColumn('credit_balance', function ($credit_balance){
                   return view('Payments::bolseiros.datatable.money', compact('credit_balance'));
               })
               ->addColumn('credit_balance_final', function ($credit_balance_final){
                   return view('Payments::bolseiros.datatable.money', compact('credit_balance_final'));
               })
               ->rawColumns(['actions','value','mode','code','credit_balance','credit_balance_final'])
               ->addIndexColumn()
               ->toJson();
       } catch (Exception | Throwable $e) {
           Log::error($e);
           return response()->json($e->getMessage(), 500);
       }
   }
   public function ajax_all($year)
   {
       try {
              
            $repayment = DB::table('repayment')
            ->leftjoin('users as u0', 'u0.id', "=", 'repayment.created_by')
            ->leftjoin('users', 'users.id', "=", 'repayment.user_id')
            ->leftjoin('lective_year_translations as lt','lt.lective_years_id',"repayment.lective_year")
            ->leftjoin('user_parameters as u_p', function ($join) {
                $join->on('repayment.user_id', '=', 'u_p.users_id')
                   ->where('u_p.parameters_id', 1);
            }) 
            ->whereNull('repayment.deleted_at')
            ->whereNull('repayment.deleted_by')
            ->whereNull('lt.deleted_at')
            ->where('lt.active',1)
            ->where('repayment.lective_year',$year)
            ->select([
                "repayment.id",
                "u_p.value as student",
                "users.email",
                "repayment.value",
                "repayment.code",
                "repayment.year",
                "repayment.mode",
                "repayment.bank",
                "repayment.iban",
                "repayment.date",
                "repayment.credit_balance",
                "repayment.credit_balance_final",
                "repayment.observation",        
                'lt.display_name as lective_year',
                "repayment.created_at",
                "u0.name as created_by",
                ])
                ->orderBy("repayment.date","desc");
             


           return Datatables::queryBuilder($repayment)
               ->addColumn('actions', function ($item){
                   return view('Payments::bolseiros.datatable.actions', compact('item'));
               })
               ->addColumn('mode', function ($item){
                   return view('Payments::bolseiros.datatable.mode', compact('item'));
               }) 
               ->addColumn('code', function ($item){
                   return view('Payments::bolseiros.datatable.code', compact('item'));
               })
               ->addColumn('value', function ($value){
                   return view('Payments::bolseiros.datatable.money', compact('value'));
               })
               ->addColumn('credit_balance', function ($credit_balance){
                   return view('Payments::bolseiros.datatable.money', compact('credit_balance'));
               })
               ->addColumn('credit_balance_final', function ($credit_balance_final){
                   return view('Payments::bolseiros.datatable.money', compact('credit_balance_final'));
               })
               ->rawColumns(['actions','value','mode','code','credit_balance','credit_balance_final'])
               ->addIndexColumn()
               ->toJson();
       } catch (Exception | Throwable $e) {
           Log::error($e);
           return response()->json($e->getMessage(), 500);
       }
   }
    public function reembolsos_store(Request $request)
    {
        try {
            
            
            $balance = DB::table('users')
            ->where("id", $request->get('users'))
            ->first();
            
            if ($balance->credit_balance < intval($request->get('value'))) {
            Toastr::warning(__('O saldo em carteira é inferior ao valor que se pretende reembolsar'), __('toastr.warning'));
            return redirect()->route('reembolsos.create', $request->get('users'));
            }

            $repayment = DB::table('repayment')->insertGetId(
                [
                    'user_id' => $request->get('users'),
                    'value' => $request->get('value'),
                    'mode' => $request->get('mode'),
                    'bank' => $request->get('bank'),
                    'credit_balance' => $balance->credit_balance,
                    'reference' => $request->reference,
                    'credit_balance_final' => ($balance->credit_balance - $request->get('value')),
                    'iban' => $request->get('iban'),
                    'date' => $request->get('date'),
                    'lective_year' => $this->get_lective()->lective_years_id,
                    'observation' => $request->get('observation'),
                    'created_by' => auth()->user()->id
                ]
            ); 
            $this->gerar_codigo($repayment); 
            
            $repayment = DB::table('users')
            ->where("id",$request->get('users'))
            ->update(
                [
                    'credit_balance' => ($balance->credit_balance - $request->get('value')),
                    'updated_by' => auth()->user()->id
                ]
            ); 

 
            $repayment_historic = DB::table('repayment_historic')->insertGetId(
                [ 
                    'value' => $request->get('value'),
                    'user_id' => $request->get('users'),
                    'created_by' => auth()->user()->id
                ]
            ); 
            
          
    
            Toastr::success(__('Reembolso criado com sucesso'), __('toastr.success'));
            return redirect()->route('bolseiros.reembolsos'); 
       
        } catch (Exception | Throwable $e) {
            Toastr::error($e->getMessage(), __('toastr.error'));
            Log::error($e);
            return $e;
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
    
    public function reembolsos_pdf($id)
    {
          
        
         $balance = DB::table('repayment')
         ->leftjoin('users','repayment.user_id', '=', 'users.id')
         ->leftjoin('users as u1','u1.id', '=', 'repayment.created_by')
         ->leftjoin('user_parameters as u_p', function ($join) {
            $join->on('repayment.user_id', '=', 'u_p.users_id')
               ->where('u_p.parameters_id', 19);
        }) 
       ->leftJoin('user_parameters as u_p1', function ($join) {
            $join->on('repayment.user_id', '=', 'u_p1.users_id')
               ->where('u_p1.parameters_id', 1);
        })
        ->leftJoin('user_classes', 'user_classes.user_id', '=', 'users.id')
        ->leftJoin('classes', 'classes.id', '=', 'user_classes.class_id')
        ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'users.id')
        ->leftJoin('courses_translations as ct', function ($join) {
            $join->on('ct.courses_id', '=', 'uc.courses_id');
            $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
            $join->on('ct.active', '=', DB::raw(true));
        })
        
        ->where("repayment.id", $id)
        ->select([
            'users.id',
            'users.name',
            'users.email',
            'u_p1.value as full_name',
            'u_p.value as matriculation',
            'ct.display_name as course',
            'classes.code as turma',
            'repayment.code',
            'repayment.year',
            'repayment.value',
            'repayment.credit_balance',
            'repayment.credit_balance_final',
            'repayment.mode',
            'repayment.reference',
            'repayment.iban',
            'repayment.bank',
            'repayment.date',
            'repayment.created_at',
            'u1.name as created_by',
            'repayment.lective_year',
            'repayment.observation',
            ])
        ->first();
       
        
        
        $lective = $this->get_lective($balance->lective_year)->display_name;

        $institution = Institution::latest()->first();
        $titulo_documento = "Remmbolsos";
        $documentoGerado_documento = "Documento gerado a ";
        $documentoCode_documento = 1;

        $funcionario = DB::table('users as usuario')
            ->leftjoin('user_parameters as u_p', 'usuario.id', '=', 'u_p.users_id')
            ->where('usuario.id',$balance->id)
            ->where('u_p.parameters_id', 1)
            ->whereNull('usuario.deleted_at')
            ->select([
                'usuario.id as id_usuario','u_p.value'
            ])
            ->first();

        //instaciando o PDF  

        $pdf = PDF::loadView("Payments::bolseiros.reembolso.pdf", compact(
            
            'institution',
            'balance',
            'funcionario',
            'titulo_documento',
            'documentoGerado_documento',
            'documentoCode_documento',
            'lective'
        ));
        //Configuração
        $pdf->setOption('margin-top', '1mm');
        $pdf->setOption('margin-left', '1mm');
        $pdf->setOption('margin-bottom', '12mm');
        $pdf->setOption('margin-right', '1mm');
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('debug-javascript', true);
        $pdf->setOption('javascript-delay', 1000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);
        $pdf->setPaper('a4', 'portrait');

        //Nome do documento PDF  

        $pdf_name = "Reembolsos";

        //Rodapé do PDF

        $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();

        $pdf->setOption('footer-html', $footer_html);

        //Retornar o PDF 

        return $pdf->stream($pdf_name . '.pdf');
    }

    public function report_reembolsos($id)
    {
          
        
         $balance = DB::table('repayment')
         ->leftjoin('users','repayment.user_id', '=', 'users.id')
         ->leftjoin('users as u1','u1.id', '=', 'repayment.created_by')
         ->leftjoin('user_parameters as u_p', function ($join) {
            $join->on('repayment.user_id', '=', 'u_p.users_id')
               ->where('u_p.parameters_id', 19);
        }) 
       ->leftJoin('user_parameters as u_p1', function ($join) {
            $join->on('repayment.user_id', '=', 'u_p1.users_id')
               ->where('u_p1.parameters_id', 1);
        })
        ->leftJoin('user_classes', 'user_classes.user_id', '=', 'users.id')
        ->leftJoin('classes', 'classes.id', '=', 'user_classes.class_id')
        ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'users.id')
        ->leftJoin('courses_translations as ct', function ($join) {
            $join->on('ct.courses_id', '=', 'uc.courses_id');
            $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
            $join->on('ct.active', '=', DB::raw(true));
        })
        
        ->where("repayment.id", $id)
        ->select([
            'users.id',
            'users.name',
            'users.email',
            'u_p1.value as full_name',
            'u_p.value as matriculation',
            'ct.display_name as course',
            'classes.code as turma',
            'repayment.id as code',
            'repayment.value',
            'repayment.credit_balance',
            'repayment.credit_balance_final',
            'repayment.mode',
            'repayment.iban',
            'repayment.bank',
            'repayment.date',
            'u1.name as created_by',
            'repayment.lective_year',
            'repayment.observation',
            ])
        ->first();
       
        
        
        $lective = $this->get_lective($balance->lective_year)->display_name;

        $institution = Institution::latest()->first();
        $titulo_documento = "Remmbolsos";
        $documentoGerado_documento = "Documento gerado a ";
        $documentoCode_documento = 1;

        $funcionario = DB::table('users as usuario')
            ->leftjoin('user_parameters as u_p', 'usuario.id', '=', 'u_p.users_id')
            ->where('usuario.id',auth()->user()->id)
            ->where('u_p.parameters_id', 1)
            ->whereNull('usuario.deleted_at')
            ->select([
                'usuario.id as id_usuario','u_p.value'
            ])
            ->first();

        //instaciando o PDF  

        $pdf = PDF::loadView("Payments::bolseiros.reembolso.pdf", compact(
            
            'institution',
            'balance',
            'funcionario',
            'titulo_documento',
            'documentoGerado_documento',
            'documentoCode_documento',
            'lective'
        ));
        //Configuração
        $pdf->setOption('margin-top', '1mm');
        $pdf->setOption('margin-left', '1mm');
        $pdf->setOption('margin-bottom', '12mm');
        $pdf->setOption('margin-right', '1mm');
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('debug-javascript', true);
        $pdf->setOption('javascript-delay', 1000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);
        $pdf->setPaper('a4', 'portrait');

        //Nome do documento PDF  

        $pdf_name = "Reembolsos";

        //Rodapé do PDF

        $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();

        $pdf->setOption('footer-html', $footer_html);

        //Retornar o PDF  

        return $pdf->stream($pdf_name . '.pdf');
    }

    public function depositos()
    {
        return view("Payments::bolseiros.deposito");
    }


    public function get_lective($id = null){
        $currentData = Carbon::now();
        
        if(isset($id)){
            return DB::table('lective_year_translations as lt')
            ->where('lt.lective_years_id',$id)
            ->whereNull('lt.deleted_at')
            ->where('lt.active',1)
            ->first();
        }else{
            $selected = DB::table('lective_years')
            ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
            ->first();
        }
        
       return $lective = DB::table('lective_year_translations as lt')
        ->where('lt.lective_years_id',$selected->id)
        ->whereNull('lt.deleted_at')
        ->where('lt.active',1)
        ->first();
    }

    public static function get_code_doc($code,$year){
            
            $institution = Institution::latest()->first();
            return substr($year, -2)."-".str_pad($code, 4, '0', STR_PAD_LEFT);
       
    }
}
