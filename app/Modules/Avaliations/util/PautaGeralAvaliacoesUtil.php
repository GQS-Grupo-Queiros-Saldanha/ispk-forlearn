<?php

namespace App\Modules\Avaliations\util;

use DB;

class PautaGeralAvaliacoesUtil
{

    public static function study($id_curso, $id_anoLectivo = null)
    {
        $data = [];
        $data['dt.active'] = 1;
        $data['sp.courses_id'] = $id_curso;
        $data['dt.abbreviation'] = 'TFC';
        if($id_anoLectivo){
            $data['study_plan_edition.lective_years_id'] = $id_anoLectivo;
        }
        return DB::table('study_plans as sp')->join('study_plans_has_disciplines as sphd', 'sphd.study_plans_id', 'sp.id')
            ->join('study_plan_editions as study_plan_edition', 'study_plan_edition.study_plans_id', '=', 'sp.id')
            ->join('study_plan_edition_disciplines as study_plan_edition_discipline', 'study_plan_edition_discipline.study_plan_edition_id', '=', 'study_plan_edition.id')
            ->join('disciplines_translations as dt', 'dt.discipline_id', 'sphd.disciplines_id')
            ->join('disciplines', 'disciplines.id', 'dt.discipline_id')
            ->where($data)
            ->orderBy('years', 'DESC')->orderBy('discipline_periods_id')
            ->select('dt.discipline_id', 'dt.display_name as disciplina', 'disciplines.code', 'years', 'sp.courses_id')
            ->first();
    }
    
    public static function usersMatriculationNotAnulate($id_turma){
        $sql = DB::table('matriculations')
                    ->join('anulate_matriculation as anulate_m', 'anulate_m.id_matricula', '=', 'matriculations.id')
                    ->join('matriculation_classes as matriculation_c','matriculation_c.matriculation_id','=','matriculations.id')
                    ->where('matriculation_c.class_id', $id_turma)
                    ->whereNull('anulate_m.deleted_at')
                    ->select('*','anulate_m.id as anulate_m_id')
                    ->get();
        $matriculasAnuladas = collect($sql)->map(function($e){
            return $e->id_matricula;
        })->all();
        $response = DB::table('matriculation_classes')
            ->whereNotIn('matriculation_id', $matriculasAnuladas)
            ->where('class_id',$id_turma)
            ->select('matriculation_id')
            ->get();
        return $response->map(function($e){
            return $e->matriculation_id;
        })->all();
    }    
    
    
    public static function userIsFinaLista($user_id, $id_curso, $lective_year)
    {
        $getStudy_plan = DB::table('study_plans as study_plan')
            ->join('study_plan_editions as study_plan_edition', 'study_plan_edition.study_plans_id', '=', 'study_plan.id')
            ->join('study_plan_edition_disciplines as study_plan_edition_discipline', 'study_plan_edition_discipline.study_plan_edition_id', '=', 'study_plan_edition.id')
            ->where('study_plan.courses_id', $id_curso)
            ->where('study_plan_edition.lective_years_id', $lective_year)
            //->where('study_plan_edition.course_year', )
            ->select([
                'study_plan_edition_discipline.discipline_id as  discipline_id'
            ])
            ->distinct('study_plan_edition_discipline.discipline_id')
            ->orderBy('study_plan_edition_discipline.discipline_id', 'ASC')
            ->get();
        $DisplionaPercuso = [];
        foreach ($getStudy_plan as $key => $item) {
            $DisplionaPercuso[] = $item->discipline_id;
        }
        $getNotasPercusso = DB::table('new_old_grades')
            ->where('new_old_grades.user_id', $user_id)
            ->whereIn('new_old_grades.discipline_id', $DisplionaPercuso)
            ->where('new_old_grades.grade', '>', 9)
            ->select([
                'new_old_grades.discipline_id as  discipline_id',
                'new_old_grades.grade as  grade'
            ])
            ->orderBy('new_old_grades.discipline_id', 'ASC')
            ->get();
        return count($getNotasPercusso) == count($getStudy_plan);
    }
    
}
