<?php

namespace App\Modules\GA\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use DB;
use Illuminate\Http\Request;
use Toastr;
use Carbon\Carbon;
use Log;
use Exception;
use App\Modules\GA\Models\LectiveYear;

class SpecialCourseEditionsController extends Controller {

    public function index()
    {
        return response()->json(['message' => 'Listando edições especiais de cursos']);
    }

    public function show($id)
    {
        return response()->json(['message' => "Exibindo curso com ID $id"]);
    }

    public function store(Request $request)
    {
        return response()->json(['message' => 'Criando uma nova edição especial de curso']);
    }

    public function update(Request $request, $id)
    {
        return response()->json(['message' => "Atualizando curso com ID $id"]);
    }



    public function list($course_id) {

        $exists = DB::table('special_course_edition')
          ->where('special_course_id',$course_id)
          ->whereNull('deleted_by')
          ->whereNull('deleted_at')
          ->exists();
       
        if(!$exists){
         
            Toastr::warning(__('Nenhuma edição encontrada para este curso!'), __('toastr.warning'));
            return redirect()->back();
        }
     
        $lectiveYears = LectiveYear::with(['currentTranslation'])
        ->get();

        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
          ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
          ->first();

          $lectiveYearSelected = $lectiveYearSelected->id ?? 9;

          $course = DB::table('special_courses')
          ->find($course_id);
        return view('GA::special-course-editions.index',compact([
            'lectiveYears',
            'lectiveYearSelected',
            'course'
        ]));
    }

 


    public function ajax($course_id, $lective_year) {

        try{

          $model= DB::table('special_course_edition')
                ->where('special_course_edition.special_course_id',$course_id)
                ->where('special_course_edition.lective_year_id',$lective_year)
                ->leftJoin('users as u1','u1.id','special_course_edition.created_by')
                ->leftJoin('users as u2','u2.id','special_course_edition.updated_by')
                ->whereNull('special_course_edition.deleted_at')
                ->whereNull('special_course_edition.deleted_by')
                ->select(['u1.name as created_por','u2.name as updated_por','special_course_edition.*'])
                ->get();

           
        
            return DataTables::of($model)
            ->addColumn('actions', function ($item) {
                 return view('GA::special-course-editions.datatables.actions')->with('item', $item);
                         })
             ->addIndexColumn()   
            ->rawColumns(['actions'])
            ->toJson();
         
        }
        catch(Exception | Throwable $e)
        {
         
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }

    
    }

 
    public function storeEdition(Request $request){
      
        if(strtotime($request->get('start_date')) > strtotime($request->get('end_date'))){
            return response()->json(['error'=>'Data inicial inválida! digite uma data menor ou igual que a data final! ']);
          
        }
        else{
            $last = DB::table('special_course_edition')
            ->where('special_course_id',$request->get('course_id'))
            ->where('lective_year_id',$request->get('lective_year'))
            ->orderBy('id','DESC')
            ->first();

if(strtotime($request->get('start_date')) <= strtotime($last->end_date))
{
    return response()->json(['error'=>"Data inválida! a data inicial tem que superior a ".$last->end_date]);
}
else{
    DB::table('special_course_edition')
    ->insert(
        [
        'number' => $request->get('number'),
        'special_course_id' => $request->get('course_id'),
        'lective_year_id' => $request->get('lective_year'),
        'start_date' => $request->get('start_date'),
        'end_date' => $request->get('end_date')
    ]);

     // Success message
    return response()->json(['success'=>"Edição adicionada com sucesso"]);

         
        }
    
 
       
    }

}

    public function updateEdition($id, Request $request){
      

            if(strtotime($request->get('start_date')) > strtotime($request->get('end_date'))){
                return response()->json(['error'=>'Data inicial inválida! digite uma data menor ou igual que a data final! ']);
              
            }
            else{
             
             DB::table('special_course_edition')
                        ->where('id', $id)
                        ->update([
                           
                            'start_date' => $request->get('start_date'),
                            'end_date' => $request->get('end_date'),
                            'updated_by' => auth()->user()->id,
                            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                        ]); 

                        return response()->json(['success'=>"Edição actualizada com sucesso"]);
                    
        }
     

    }


    public function destroy($id) {

        try{
     
            DB::table('special_course_edition')
            ->where('id', $id)
            ->update(
              [
                'deleted_by' => auth()->user()->id,
                'deleted_at' => Carbon::now()->format('Y-m-d H:i')
              ]);
    
          // Success message
          Toastr::success('Success');
          return redirect()->route('special-course-editions.index');
       
      }
     catch (Exception | Throwable $e) {
     
        Toastr::error($e->getMessage(), __('toastr.error'));
        Log::error($e);
        return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
       }
    }







}