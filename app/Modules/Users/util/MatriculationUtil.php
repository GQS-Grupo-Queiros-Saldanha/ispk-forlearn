<?php
namespace App\Modules\Users\util;
use App\Modules\Users\Models\Matriculation;
use DB;


 class MatriculationUtil {

    public static function verificarAnoCurricularBloqueado($item) : bool{
        $obj = DB::table('course_curricular_block')
               ->where('id_lective_year',$item->lective_year)
               ->where('id_course',$item->id_course)
               ->where('curricular_year',$item->course_year)
               ->where('state',1)
               ->first();
        return isset($obj->id);
    }  
    
    public static function verificarCursoBloquedo($id_lective,$id_curso,$id_course_year) : bool{
        return MatriculationUtil::verificarAnoCurricularBloqueado((object)[
            "lective_year" => $id_lective, "id_course" => $id_curso, "course_year" => $id_course_year
        ]);
    }
    
    public static function verificarCursoBloquedoPorAnosCurriculares($id_lective,$id_curso,$id_course_year = []) : bool{
        foreach($id_course_year as $course) 
            if(MatriculationUtil::verificarCursoBloquedo($id_lective,$id_curso,$course))
                return true;
        return false;
    } 
      public static function verificarEdicaoPlanoEstudo($id_lective,$id_curso) : bool{

                $Edition_plain = DB::table('study_plan_editions as edp')
                ->join('study_plans as sp','sp.id','edp.study_plans_id')
                ->where('edp.lective_years_id',$id_lective)
                ->where('sp.courses_id',$id_curso)
                ->whereNull('sp.deleted_by')
                ->whereNull('sp.deleted_at')
                ->get();
                $tam = sizeof($Edition_plain);
                if ($tam > 0) {
                    return false;
                } else {
                    return true;
                }
    } 
    
    public static function verificarPrimeiroAnoCurricularBoletim($matriculation) : bool{
        $matriculations = Matriculation::where('user_id',$matriculation->user_id)->orderBy('created_at')
                                       ->whereNull('deleted_by')
                                       ->whereNull('deleted_at')
                                       ->get();
        $tam = sizeof($matriculations);
        
        if($tam == 1) return true;
        
        foreach($matriculations as $mat)
            if($mat->lective_year != $matriculation->lective_year) return false;
        
        return true;
    }

 }