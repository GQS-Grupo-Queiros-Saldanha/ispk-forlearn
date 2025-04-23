<?php

namespace App\Modules\Users\util;

use DB;
use Carbon;
use App\Helpers\LanguageHelper;
use App\Modules\Avaliations\Models\PlanoEstudoAvaliacao;
use App\Modules\GA\Models\Discipline;

class VerificarDisciplina
{
    private $disciplinaId;
    private $user_type;

    function __construct($disciplinaId)
    {
        $this->disciplinaId = $disciplinaId;
        $this->user_type = "";
    }

    function __get($name)
    {
        return $this->$name;
    }

    function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function get_disciplina_with_curso(){
        return Discipline::with('course')->find($this->disciplinaId);
    }

    public function verifyIsCoordernador($user_id): bool{
        $disciplinaWithCourse = $this->get_disciplina_with_curso();
        $coordernador =  DB::table('coordinator_course')
        ->where('user_id',$user_id)
        ->where('courses_id',$disciplinaWithCourse->course->id)
        ->first();
        return isset($coordernador->id);
    }   


}
