<?php

use App\Helpers\LanguageHelper;
use App\Http\Controllers\Controller;
use Log;

class SpecialCourseGrades extends Controller{


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

            $courses = DB::table('special_courses')
                            ->whereNull('deleted_at')
                            ->whereNull('deleted_by')
                            ->get();


           $data = [
                      'courses' => $courses,
                      'lectiveYearSelected'=>$lectiveYearSelected,
                      'lectiveYears'=>$lectiveYears
                   ];

            return view('Grades::special-course-grades.index')->with($data);
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
                        ->where('$course_id',$course_id)
                        ->whereNull('deleted_at')
                        ->whereNull('deleted_by')
                        ->get();

    return json_encode(["editions"=>$editions]);
        
    }       
    catch(Exception $e){
        Log::error($e);
        return response()->json($e->getMessage(), 500);
    }
}

}




}