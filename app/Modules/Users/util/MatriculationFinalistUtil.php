<?php
namespace App\Modules\Users\util;
use Illuminate\Http\Request;
use DB;

 class MatriculationFinalistUtil {
    private $idAnoLectivo;
    private $emolumentoConfirmaPrematricula;

    function __construct($idAnoLectivo, $emolumentoConfirmaPrematricula){
        $this->idAnoLectivo = $idAnoLectivo;
        $this->emolumentoConfirmaPrematricula = $emolumentoConfirmaPrematricula;
    }

    private function distinct($students){
        $array = [];
        $keys = [];
        foreach($students as $student){
            if(!in_array($student->id_matriculation_finalist, $keys)){
                array_push($keys,$student->id_matriculation_finalist);
                array_push($array,$student);
            }
        }   
        return $array;
    }

    private function defaultQuery(){
        $emolumento_confirma_prematricula = $this->emolumentoConfirmaPrematricula;
        return DB::table('matriculation_finalist as matricula_finalist')
        ->join('users as use','use.id','=','matricula_finalist.user_id')
        ->join('courses_translations as corse_translation',function ($q)
        {
             $q->on('corse_translation.courses_id','=','matricula_finalist.id_curso')
             ->where('corse_translation.language_id',1)
             ->where('corse_translation.active',1);
        })
        ->leftJoin('user_parameters as name_full',function($q){
           $q->on('name_full.users_id','=','use.id')
           ->where('name_full.parameters_id',1); 
        })
        ->leftJoin('user_parameters as name_full_creat',function($q){
            $q->on('name_full_creat.users_id','=','matricula_finalist.created_by')
            ->where('name_full_creat.parameters_id',1); 
         })
        ->leftJoin('user_parameters as matricula',function($q){
           $q->on('matricula.users_id','=','use.id')
           ->where('matricula.parameters_id',19); 
        })
        ->leftJoin('user_parameters as num_bi',function($q){
            $q->on('num_bi.users_id','=','use.id')
            ->where('num_bi.parameters_id',14); 
         })
         ->leftJoin('article_requests as art_requests',function ($join) use($emolumento_confirma_prematricula)
         {
             $join->on('art_requests.user_id','=','use.id')
             ->whereIn('art_requests.article_id', $emolumento_confirma_prematricula)
             ->orderBy('art_requests.created_at','DESC')
             ->whereNull('art_requests.deleted_by') 
             ->whereNull('art_requests.deleted_at');
         })
        ->select([
            'art_requests.status as state',
            'matricula_finalist.id as id_matriculation_finalist',
            'matricula_finalist.num_confirmaMatricula as num_confirmaMatricula',
            'matricula_finalist.year_curso as year_curso',
            'matricula_finalist.created_at as created_at',
            'matricula_finalist.updated_at as updated_at',
            'matricula.value as matricula',
            'name_full.value as name_full',
            'name_full_creat.value as name_full_creat',
            'num_bi.value as num_bi',
            'use.email as email',
            'use.id as user_id',
            'corse_translation.display_name as display_name'
        ])
        //->distinct('matricula_finalist.id')
        ->where('matricula_finalist.year_lectivo',$this->idAnoLectivo)
        ->orderBy('matricula_finalist.created_at','DESC')
        ->whereNull('matricula_finalist.deleted_by')
        ->whereNull('matricula_finalist.deleted_at');
    }

    public function getStundetsFinalist(){
        $students = $this->defaultQuery()->get();
        return $this->distinct($students);
    }

    private function getStundetsFinalistByCurso($cursoId){
        $students = $this->defaultQuery()->where('matricula_finalist.id_curso',$cursoId)->get();
        return $this->distinct($students);
    }

    public function requestGetStundetsFinalist(Request $request){
        if(isset($request->cursoBy) && $request->cursoBy != 0)
            return $this->getStundetsFinalistByCurso($request->cursoBy);
        return $this->getStundetsFinalist();
    }

 }