<?php

namespace App\Modules\Users\util;

use App\Modules\GA\Models\LectiveYear;
use App\Helpers\LanguageHelper;
use Carbon\Carbon;
use DB;

class GraduadoCandidaturaUtil{

    public static function graduado($lective_year_id = null, $disciplina_id = null){
        $result = [
            'sexo_value.code as sexo',
            'user.id as is_user',
            'full_name.value as nome_completo',
            'Percurso.grade as nota',
            'Percurso.lective_year as AnoLectivo',
            'up_meca.value as matricula',
            'dc.code as codigo_disciplina',
            'ct.display_name as disciplina',
            'ctt.display_name as curso',
            'user.email',
            'user.is_duplicate'
        ];

        $query = DB::table('new_old_grades as Percurso') ->leftJoin('users as user', 'user.id', '=', 'Percurso.user_id')
        ->leftJoin('user_parameters as full_name', function ($join) {
            $join->on('user.id', '=', 'full_name.users_id')
                 ->where('full_name.parameters_id', 1);
        })
        ->leftJoin('user_parameters as up_meca', function ($join) {
            $join->on('user.id','=','up_meca.users_id')
                 ->where('up_meca.parameters_id', 19);
        }) 
        ->leftJoin('user_parameters as sexo', function ($join) {
            $join->on('user.id', '=', 'sexo.users_id')
                 ->where('sexo.parameters_id', 2);
        })
        ->leftJoin('parameter_options as sexo_value', 'sexo_value.id', '=', 'sexo.value') 
        ->leftJoin('disciplines as dc', 'dc.id', '=', 'Percurso.discipline_id')
        ->leftJoin('disciplines_translations as ct', function ($join) {
            $join->on('ct.discipline_id', '=', 'Percurso.discipline_id');
            $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
            $join->on('ct.active', '=', DB::raw(true));
            $join->where('ct.abbreviation','=','TFC');
        })
        ->join('user_courses as uc','uc.users_id','user.id')
        ->join('courses_translations as ctt','ctt.courses_id','uc.courses_id')
        ->where('Percurso.grade','>',9)
        ->where('ctt.active',1)
        ->whereNotNull('ct.display_name')
        ->orderBy('nota','ASC')
        ->distinct('matricula','is_user');

        if(isset($lective_year_id)){
            $lectiveYears = LectiveYear::with(['currentTranslation'])->where('id',$lective_year_id)->first();
            $anoLectivoDsiplay_name= $lectiveYears['currentTranslation']->display_name;
            $query = $query->where('Percurso.lective_year',$anoLectivoDsiplay_name);
        }

        if(isset($disciplina_id)){            
            array_push($result);
            $query = $query->when($disciplina_id, function ($query, $disciplina_id) {
                $query->where('Percurso.discipline_id', $disciplina_id);
            }); 
        }



        return $query->select($result)->get();
    }

    public static function generatorCode(){
        $latestsCandidate = DB::table('user_candidate')->orderBy('id', 'DESC')->first();
        return FinalistaCandidaturaUtil::createNewCode($latestsCandidate);
    }

    private static function createNewCode($latestsCandidate)
    {
        if ($latestsCandidate && Carbon::parse($latestsCandidate->created_at)->year === Carbon::now()->year) {
            $nextCode = 'CE' . ((int) ltrim($latestsCandidate->code, 'CE') + 1);
        } else {
            $nextCode = 'CE' . substr(Carbon::now()->format('Y'), -2) . '0001';
        }
        return $nextCode;
    }    

}