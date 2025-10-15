<?php

namespace App\Modules\Avaliations\Controllers;

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

class CalendarioProvaController extends Controller
{
    public function index()
    {
        $lectiveYears = LectiveYear::with(['currentTranslation'])
         ->get();
        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
         ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
         ->first();
         
        $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
        $Avaliacao= Avaliacao::all();
        return view('Avaliations::calendario-prova.index', compact('lectiveYears','lectiveYearSelected','Avaliacao'));

    }


    public function getCreate($id_anolectivo)
    {
     
        try {
           
        

        $PERIODO= DB::table('discipline_periods as period')
            ->leftJoin('discipline_period_translations as dt', function ($join) {
            $join->on('dt.discipline_periods_id', '=', 'period.id');
            $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
            $join->on('dt.active', '=', DB::raw(true));
            })
            ->select(['dt.display_name','period.id'])
            ->get();




             
          $Avaliacao= Avaliacao::join('users as u1', 'u1.id', '=', 'avaliacaos.created_by')
          ->leftJoin('users as u2', 'u2.id', '=', 'avaliacaos.updated_by')
          ->leftJoin('users as u3', 'u3.id', '=', 'avaliacaos.deleted_by')
        //   ->leftJoin('calendario_prova as calend', 'calend.id_avaliacao', '=', 'avaliacaos.id')
          ->join('tipo_avaliacaos as ta', 'ta.id', '=', 'avaliacaos.tipo_avaliacaos_id')
          ->select([
              'avaliacaos.id as avaliacao_id',
              'avaliacaos.lock as avaliacao_lock',
              'avaliacaos.nome',
              'u1.name as created_by',
              'u2.name as updated_by',
              'ta.nome as ta_nome',
              'avaliacaos.created_at as created_at',
              'avaliacaos.updated_at as updated_at'
            //   'calend.id_avaliacao as calend_id_avaliacao',
            //   'calend.deleted_at as deleted_at'
          ])
          ->where('avaliacaos.anoLectivo',$id_anolectivo)
          ->where('avaliacaos.deleted_by',null)
          ->orderBy('avaliacaos.nome')
          ->get();
           
            $data = [
                'action' => 'create',
                'Avaliacao' =>  $Avaliacao,
                'periodo'=>$PERIODO
            ];

           return view('Avaliations::calendario-prova.calendario')->with($data);
           
        } catch (Exception | Throwable $e) {
            Log::error($e);
            Toastr::error($e->getMessage(), __('toastr.error'));
            return redirect()->back();
        }
        
    }

    public function create()
    {
     
        try {
           
        $PERIODO= DB::table('discipline_periods as period')
            ->leftJoin('discipline_period_translations as dt', function ($join) {
            $join->on('dt.discipline_periods_id', '=', 'period.id');
            $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
            $join->on('dt.active', '=', DB::raw(true));
            })
            ->select(['dt.display_name','period.id'])
            ->get();




             
          $Avaliacao= Avaliacao::join('users as u1', 'u1.id', '=', 'avaliacaos.created_by')
          ->leftJoin('users as u2', 'u2.id', '=', 'avaliacaos.updated_by')
          ->leftJoin('users as u3', 'u3.id', '=', 'avaliacaos.deleted_by')
          ->leftJoin('calendario_prova as calend', 'calend.id_avaliacao', '=', 'avaliacaos.id')
          ->join('tipo_avaliacaos as ta', 'ta.id', '=', 'avaliacaos.tipo_avaliacaos_id')
          ->select([
              'avaliacaos.id as avaliacao_id',
              'avaliacaos.lock as avaliacao_lock',
              'avaliacaos.nome',
              'u1.name as created_by',
              'u2.name as updated_by',
              'ta.nome as ta_nome',
              'avaliacaos.created_at as created_at',
              'avaliacaos.updated_at as updated_at',
              'calend.id_avaliacao as calend_id_avaliacao',
              'calend.deleted_at as deleted_at'
          ])
          
          ->get();
           
            $data = [
                'action' => 'create',
                'Avaliacao' =>  $Avaliacao,
                'periodo'=>$PERIODO
            ];

           return view('Avaliations::calendario-prova.calendario')->with($data);
           
        } catch (Exception | Throwable $e) {
            Log::error($e);
            Toastr::error($e->getMessage(), __('toastr.error'));
            return redirect()->back();
        }
        
    }


    public function getCalendarieYear($lectiveYear)
    {
        try {
            
          
            $currentData = Carbon::now();
            // $lectiveYear = DB::table('lective_years')
            // ->where('id',$lectiveYear)
            // ->first();

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
                    'cl.id as id_calendario',
                    'cl.code as code',
                    'dt.display_name as simestre',
                    'cl.display_name as name',
                    'cl.date_start as data_inicio',
                    'cl.data_end as date_fim',
                    'u1.name as us_created_by',
                    'u2.name as us_updated_by'
        
                 ])
                 ->where('cl.lectiveYear',$lectiveYear)
                 ->where('cl.deleted_at',null)
                 ->where('cl.deleted_by',null)
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
            return $e;
            Log::error($e);
            return response()->json($e->getMessage(), 500);
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
                 ->where('cl.lectiveYear',$lectiveYear->id)
                 ->whereNull('cl.deleted_by')
                 ->whereNull('cl.deleted_at')
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
        
        $calendario = DB::table('calendario_prova')
        ->where('calendario_prova.deleted_by',null)
        // ->where('calendario_prova.deleted_at',null)
        ->where('id',$id)->get();
        $action = 'show';
        $Avaliacao= Avaliacao::all();
        
        $data = [
            'action' => $action,
            'calendario' => $calendario ,
            'Avaliacao' =>$Avaliacao
        ];

        return view('Avaliations::calendario-prova.calendario')->with($data);
        
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
           
            // return $request;
            $consult_calendario=DB::table('calendario_prova')
            ->where('calendario_prova.id_avaliacao',$id)->get();

            $consult_last_calendario=DB::table('calendario_prova')
                ->where('calendario_prova.deleted_at',null)
                ->where('calendario_prova.deleted_by',null)
                ->where('calendario_prova.simestre',$request->simestre_prova)
                ->get()->max('data_end');
               
                foreach ($consult_calendario as $item ) {  
                }
               
                if(strtotime($request->data_start)>=strtotime($item->date_start)  and strtotime($request->data_start)<=strtotime($item->data_end)) {
                  if (strtotime($request->data_end)>=strtotime($item->date_start)  and strtotime($request->data_end)<=strtotime($item->data_end)) {
                    if(strtotime($request->data_start)>strtotime($request->data_end)){
                        Toastr::error(__('Calendário não foi actualizado, o intervalo de datas inserido não é válido.'), __('toastr.error'));
                        return redirect()->route('school-exam-calendar.index');
                         }
                        $id_user=Auth::user()->id;
                        $currentData = Carbon::now(); 
                        DB::table('calendario_prova')
                         ->where('id_avaliacao', $id)
                         ->where('calendario_prova.simestre',$request->simestre_prova)
                             ->update(
                          [
                           'code' => $request->codigo,
                           'display_name' => $request->nome,
                           'date_start' => $request->data_start,
                           'data_end' => $request->data_end,
                           'updated_by' => $id_user,
                           'updated_at' => $currentData,
                           'deleted_at' => null
                          ]         
                      );            
                    Toastr::success(__('Calendário editado com sucesso'), __('toastr.success'));
                    return redirect()->route('school-exam-calendar.index');
                  }
                     
                }
                elseif (strtotime($request->data_end)==strtotime($consult_last_calendario)) {
                    if(strtotime($request->data_start)>strtotime($request->data_end)){
                        Toastr::error(__('Calendário não foi actualizado, o intervalo de datas inserido não é válido.'), __('toastr.error'));
                        return redirect()->route('school-exam-calendar.index');
                         }
                        $id_user=Auth::user()->id;
                        $currentData = Carbon::now(); 
                        DB::table('calendario_prova')
                         ->where('id_avaliacao', $id)
                         ->where('calendario_prova.simestre',$request->simestre_prova)
                             ->update(
                          [
                           'code' => $request->codigo,
                           'display_name' => $request->nome,
                           'date_start' => $request->data_start,
                           'data_end' => $request->data_end,
                           'updated_by' => $id_user,
                           'updated_at' => $currentData,
                           'deleted_at' => null,
                           'deleted_by' => null
                          ]         
                      );            
                    Toastr::success(__('Calendário editado com sucesso'), __('toastr.success'));
                    return redirect()->route('school-exam-calendar.index'); 
                 } //sed
                
                elseif (strtotime($request->data_start) < strtotime($consult_last_calendario)) {
                   
                    Toastr::error(__('O intervalo de data inserido não é válido. cadastra um intervalo superior'), __('toastr.error'));
                    return redirect()->route('school-exam-calendar.index');
                 } 
                 else{
                    
                    if(strtotime($request->data_start)>strtotime($request->data_end)){
                        Toastr::error(__('Calendário não foi actualizado, o intervalo de datas inserido não é válido.'), __('toastr.error'));
                        return redirect()->route('school-exam-calendar.index');
                         }
                        $id_user=Auth::user()->id;
                        $currentData = Carbon::now(); 
                        DB::table('calendario_prova')
                         ->where('id_avaliacao', $id)
                         ->where('calendario_prova.simestre',$request->simestre_prova)
                             ->update(
                          [
                           'code' => $request->codigo,
                           'display_name' => $request->nome,
                           'date_start' => $request->data_start,
                           'data_end' => $request->data_end,
                           'updated_by' => $id_user,
                           'updated_at' => $currentData,
                           'deleted_at' => null,
                           'deleted_by' => null
                          ]         
                      );            
                    Toastr::success(__('Calendário editado com sucesso'), __('toastr.success'));
                    return redirect()->route('school-exam-calendar.index'); 
                }
                 
                
         } catch (Exception | Throwable $e) {
            Log::error($e);
            dd($e->getMessage());
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
        
    }

    public function store(Request $request)
    {
      try{  
    // return $request;
       $consult_calendario=DB::table('calendario_prova')
        ->where('calendario_prova.deleted_at',null)
        ->where("simestre","!=",$request->simestre_prova)
        ->get();
        
        $id=Auth::user()->id;
       
        $currentData = Carbon::now(); 
        $id_avaliacao = explode("," ,$request->avalicacao);
        $lectiveYears = LectiveYear::with(['currentTranslation'])
        ->get();
        $lectiveYearSelected = DB::table('lective_years')
       
        ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
        ->first();
        $lectiveYearSelected = $lectiveYearSelected->id ?? 6;

        if ($consult_calendario==true) {

         $consult_last_calendario=DB::table('calendario_prova')
            ->where('calendario_prova.deleted_at',null)
            ->where('calendario_prova.deleted_by',null)
            ->where('calendario_prova.lectiveYear',$request->ano_lectivo)
            ->where('calendario_prova.simestre',$request->simestre_prova)
            ->get()->max('data_end');
         if (strtotime($request->data_start)<strtotime($consult_last_calendario)) {
              Toastr::error(__('O intervalo de data inserido não é válido. cadastra um intervalo superior'), __('toastr.error'));
              return redirect()->route('school-exam-calendar.index');
          }
                
          else{
                    
            DB::table('calendario_prova')->updateOrInsert(
                        ['id_avaliacao' =>$id_avaliacao[0],
                         'simestre' =>$request->simestre_prova
                        
                        ]

                        ,[   
                        
                        'code' => $request->codigo,
                        'display_name' => $id_avaliacao[1],
                        'date_start' => $request->data_start,
                        'data_end' => $request->data_end,
                        'id_avaliacao' =>$id_avaliacao[0],
                        'simestre' =>$request->simestre_prova,
                        'lectiveYear' =>$lectiveYearSelected,
                        'created_by' => $id,
                        'updated_by' => $id,
                        'created_at' => $currentData,
                        'updated_at' => $currentData,
                        'deleted_by' => null,
                        'deleted_at' => null       
                    ]);
                    Toastr::success(__('Calendário cadastrado com sucesso'), __('toastr.success'));
                    return redirect()->route('school-exam-calendar.index');
                }
            }
               if(strtotime($request->data_start)>strtotime($request->data_end)){
                    Toastr::error(__('Calendário não foi cadastrado, o intervalo de datas inserido não é válido.'), __('toastr.error'));
                    return redirect()->route('school-exam-calendar.index');
                }
              
                    DB::table('calendario_prova')->updateOrInsert(
                        ['id_avaliacao' =>$id_avaliacao[0],
                         'simestre' =>$request->simestre_prova
                        ]
                        ,
                        [   
                        
                        'code' => $request->codigo,
                        'display_name' => $id_avaliacao[1],
                        'date_start' => $request->data_start,
                        'data_end' => $request->data_end,
                        'id_avaliacao' =>$id_avaliacao[0],
                        'simestre' =>$request->simestre_prova,
                        'lectiveYear' =>$lectiveYearSelected,
                        'created_by' => $id,
                        'updated_by' => $id,
                        'created_at' => $currentData,
                        'updated_at' => $currentData,
                        'deleted_by' => null,
                        'deleted_at' => null       
                    ]);
                    Toastr::success(__('Calendário cadastrado com sucesso'), __('toastr.success'));
                    return redirect()->route('school-exam-calendar.index');
        
         } catch (Exception | Throwable $e) {
            Log::error($e);
            dd($e->getMessage());
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
        
    }
   public function destroy($id)
    {
        try{

        $id_user=Auth::user()->id;
        $currentData = Carbon::now(); 

       DB::table('calendario_prova')
        ->where('id', $id)
        ->update(['deleted_by' => $id_user,'deleted_at'=>$currentData]); 

        Toastr::success(__('Calendário Eliminado com sucesso'), __('toastr.success'));
        return redirect()->route('school-exam-calendar.index');
     }
     catch (Exception | Throwable $e) {
        Log::error($e);
        dd($e->getMessage());
        return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
    }

    }
    
}
