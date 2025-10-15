<?php

namespace App\Modules\Grades\Controllers;

use App\Helpers\LanguageHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Modules\GA\Models\LectiveYear;
use Carbon\Carbon;
use DB;
use Exception;
use App\Modules\Users\Enum\ParameterEnum;
use Illuminate\Http\Request;
use Toastr;
use App\Model\Institution;
use PDF;
class SpecialCourseGradesController extends Controller{


    public function index(){
        try{
           //Pegar o ano lectivo na select
           $lectiveYears = LectiveYear::with(['currentTranslation'])
           ->get();
           $currentData = Carbon::now();
           $lectiveYearSelected = DB::table('lective_years')
               ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
               ->first(); 
           $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
           //-----------------------------------------------------------------------//
            $user = auth()->user();
            $courses =  DB::table('coordinator_special_course')
            ->join('special_courses as sc','sc.id', '=', 'coordinator_special_course.courses_id')
            ->where('user_id', $user->id)
            ->whereNull('sc.deleted_at')
            ->select('sc.*')
            ->get();


           $data = [
                      'courses' => $courses,
                      'lectiveYearSelected'=>$lectiveYearSelected,
                      'lectiveYears'=>$lectiveYears
                   ];

            return view('Grades::special-courses-grades.index')->with($data);
        }
        catch(Exception $e){
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }

public function getEditions($course_id,$lective_year){
    try{
        $editions = DB::table('special_course_edition')
                        ->where('lective_year_id',$lective_year)
                        ->where('special_course_id',$course_id)
                        ->whereNull('deleted_at')
                        ->whereNull('deleted_by')
                        ->select(['number','id'])
                        ->get();

    return json_encode(["editions"=>$editions]);
        
    }       
    catch(Exception $e){
        Log::error($e);
        return response()->json($e->getMessage(), 500);
    }
}

public function getStudentsGrades($edition_id){
    try{
        $model = DB::table('students_special_course as spc')
        ->leftJoin('students_special_course_grades as grades', 'grades.student_special_course_id', '=', 'spc.id')
        ->join('user_parameters as name', 'name.users_id', '=', 'spc.user_id')
        ->where('spc.special_course_edition_id', $edition_id)
        ->whereNull('spc.deleted_at')
        ->whereNull('spc.deleted_by')
        ->where('name.parameters_id', ParameterEnum::NOME)
        ->select([
            'spc.code',
            'spc.id',
            'grades.grade',
            'name.value as student_name'
        ])
        ->get();
    

    return json_encode(["data"=>$model]);
        
    }       
    catch(Exception $e){
        Log::error($e);
        return response()->json($e->getMessage(), 500);
    }
}

    public function store(Request $request){
        try{

           

            $grades = $request->get('grades');
            $students = $request->get('students');

            DB::beginTransaction();

            for($i = 0; $i < count($students); $i++)
                {

                    $notaMaior= $grades[$i] >100 ? 1 : 0;
                    if($notaMaior==1){
                    Toastr::warning(__('Atenção! não foi possivel atribuir as notas porque o detetamos que a nota inserida para a um determinado estudante foi superior a 100 valores, verifica os campos ao atribuir as notas.'), __('toastr.success'));
                    return back();
                    }

                    DB::table('students_special_course_grades')
                    ->updateOrInsert([
                        'student_special_course_id' => $students[$i],                     
                    ],
                [
                        'grade' =>$grades[$i],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                        'created_by' => auth()->user()->id,
                        'updated_by' => auth()->user()->id
                ]);
                }
                DB::commit();
                Toastr::success(__('Notas guardadas com sucesso!'), __('toastr.success'));
                return redirect()->back();
            }
        catch(Exception $e){
            DB::rollback();

            Log::error($e);

            return response()->json($e->getMessage(), 500);
        }
    }


    public function generatePDF($edition_id,Request $request){
    try{
       
        $content = DB::table('students_special_course as spc')
        ->join('students_special_course_grades as grades', 'grades.student_special_course_id', '=', 'spc.id')
        ->join('user_parameters as name', 'name.users_id', '=', 'spc.user_id')
        ->where('spc.special_course_edition_id', $edition_id)
        ->whereNull('spc.deleted_at')
        ->whereNull('spc.deleted_by')
        ->where('name.parameters_id', ParameterEnum::NOME)
        ->select([
            'spc.code',
            'spc.id',
            'grades.grade',
            'name.value as student_name'
        ])
        ->get();

        if($content->isEmpty()) {
            Toastr::warning(__('Não há notas lançadas nesta pauta'),__('toastr.warning'));
            return back();
        }

        $edition = DB::table('special_course_edition')
        ->where('special_course_edition.id', $edition_id)
        ->join('special_courses','special_courses.id','special_course_edition.special_course_id')
        ->select([
            'special_course_edition.*',
            'special_courses.display_name as course'
        ])
        ->first();

        $lectiveYears = LectiveYear::where('id',$edition->lective_year_id)
                    ->with('currentTranslation')
                    ->first();
            
        $institution = Institution::latest()->first();  
         
        $pdf = PDF::loadView("Grades::special-courses-grades.pdf", compact(
            'lectiveYears',
            'edition',
            'institution',
           'content'
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
        $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
            $pdf->setOption('footer-html', $footer_html);
        
        $pdf_name="pdf_notas_cp";
        //$footer_html = view()->make('Users::users.partials.pdf_footer')->render();
        //$pdf->setOption('footer-html', $footer_html);
        return $pdf->stream($pdf_name.'.pdf');

    }       
    catch(Exception $e){
        Log::error($e);
        return response()->json($e->getMessage(), 500);
    }
}

}




