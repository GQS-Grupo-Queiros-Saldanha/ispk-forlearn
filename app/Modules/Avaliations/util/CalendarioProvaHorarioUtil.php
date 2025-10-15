<?php

namespace App\Modules\Avaliations\util;

use App\Modules\Avaliations\Models\CalendarioProvaHorarioJuris;
use App\Modules\Avaliations\Models\CalendarioProvaHorario;
use DateTime;
use DB;

class CalendarioProvaHorarioUtil
{
    public static function getMesAtual() {
        $mesAtual = date('n');
        $meses = array(
            1 => 'Janeiro',2 => 'Fevereiro',3 => 'Março',4 => 'Abril',5 => 'Maio', 6 => 'Junho',
            7 => 'Julho', 8 => 'Agosto',9 => 'Setembro',10 => 'Outubro',11 => 'Novembro',12 => 'Dezembro'
        );
        return $meses[$mesAtual];
    }

    public static function describeNumberRomano($num) {
        $numbers = array(1 => 'I',2 => 'II',3 => 'III',4 => 'IV');
        return $numbers[$num] ?? $num;
    }

    public static function describePeriodo($periodo){
        switch($periodo){
            case "MANHA":
                return "MANHÃ";
            default:
                return $periodo;
        }
    }

    public static function describeDataMarcada($dataMarcada){
        $data = new DateTime($dataMarcada);
        return ($data->format('w') + 1)."ª FEIRA";
    }

    private static function  extractFirstAndLastName($fullName) {
        $names = explode(' ', $fullName);
        $firstName = $names[0];
        $lastName = end($names);
        
        return $firstName.' '.$lastName;
    }

    public static function describeJuris($calendario_horario_id, $periodo = ""){
        $juris = CalendarioProvaHorarioUtil::juris($calendario_horario_id, $periodo);
        $names = $juris->map(function($e){
            return CalendarioProvaHorarioUtil::extractFirstAndLastName($e->name);
        })->all();
        return implode(' / ', $names);
    }

    public static function horaInteval($request, $horaVerificar)
    {
        return CalendarioProvaHorario::join('disciplines_translations', 'discipline_id', '=', 'disciplina_id')
            ->where('data_prova_marcada', '=', $request->data_prova_marcada)
            ->where('turma_id', '=', $request->turma_id)
            ->where('calendario_prova_id', '=', $request->calendario_prova_id)
            ->whereTime('hora_comeco', '<=', $horaVerificar)
            ->whereTime('hora_termino', '>=', $horaVerificar)
            ->whereNull('calendario_horario.deleted_at')
            ->whereNull('calendario_horario.deleted_by')
            ->where('disciplines_translations.active', 1)
            ->orderBy('id', 'DESC')
            ->select("display_name as discipline", "calendario_horario.*")
            ->first();
    }

    public static function juris($prova_horario, $periodo = "")
    {
        return CalendarioProvaHorarioJuris::join("users", "user_id", "=", "users.id")
            ->join('calendario_horario as ch','ch.id','calendario_horario_juris.calendario_horario_id')
            ->where('calendario_horario_juris.calendario_horario_id', $prova_horario)
            ->where('ch.periodo',$periodo)
            ->whereNull('calendario_horario_juris.deleted_at')
            ->whereNull('calendario_horario_juris.deleted_by')
            ->select("calendario_horario_juris.id as juri_id", "users.name", "users.email")
            ->get();
    }

    public static function getRecursoProva($disciplina_id, $lectiveYear, $periodo = ""){
        return  CalendarioProvaHorario::join('calendario_prova','calendario_prova.id','=','calendario_prova_id')
        ->join('discipline_periods as pr', 'pr.id', '=', 'calendario_prova.simestre')
        ->where('calendario_horario.disciplina_id', $disciplina_id)
        ->where('calendario_prova.lectiveYear', $lectiveYear)
        ->where('calendario_horario.periodo', $periodo)
        ->whereNull('calendario_horario.deleted_at')
        ->whereNull('calendario_horario.deleted_by')
        ->where('calendario_prova.display_name','=','Recursos')
        ->select(
            "calendario_horario.data_prova_marcada as data_marcada",
            "calendario_prova.simestre",
            DB::raw("CONCAT(DATE_FORMAT(hora_comeco, '%HH:%i'),'-',DATE_FORMAT(hora_termino, '%HH:%i')) as intervalo")
        )->first();
    }

    public static function getMarcacao($class_id, $year, $periodo, $simestre)
    {
        $query = CalendarioProvaHorario::join('disciplines_translations', 'discipline_id', '=', 'calendario_horario.disciplina_id')
            ->join('classes', 'classes.id', '=', 'calendario_horario.turma_id')
            ->join('calendario_prova','calendario_prova.id','=','calendario_prova_id')
            ->leftjoin('avaliacaos','calendario_prova.id_avaliacao','=','avaliacaos.id')
            ->leftjoin('room_translations as rt','classes.room_id','=','rt.room_id')
            ->where('classes.year', $year)
            ->where("disciplines_translations.active",1)
            ->where('rt.active',1)
            ->whereNull('calendario_horario.deleted_at')
            ->whereNull('calendario_horario.deleted_by')
            ->where('avaliacaos.nome','!=','Recursos')
            ->where('classes.id',$class_id)
            ->where('calendario_horario.periodo', $periodo)
            ->select(
                "classes.display_name as turma", "disciplines_translations.display_name as disciplina",
                "calendario_horario.data_prova_marcada as data_marcada",
                "calendario_horario.id as calendario_horario_id",
                "calendario_prova.lectiveYear",
                "calendario_horario.disciplina_id",
                "classes.display_name as turma",
                "calendario_horario.periodo",
                'rt.display_name as sala',
                DB::raw("CONCAT(DATE_FORMAT(hora_comeco, '%HH:%i'),'-',DATE_FORMAT(hora_termino, '%HH:%i')) as intervalo")
            )->distinct();

        if(isset($simestre))
            $query = $query->where('calendario_prova.simestre', $simestre);
    
        return $query->get(); 
    }      

    public static function notificationJuri($calenderProva, $disciplina,$cursoAndTurma, $juri, $periodo){
        $subject = "Seleção de Juri para controlar avaliação";
        $body = "Caro(a) utilizador(a) foi adicionado como juri para controlar a avaliação <strong>{$calenderProva->display_name}</strong> da disciplina <strong>{$disciplina->display_name}</strong> no curso <strong>{$cursoAndTurma->curso}</strong> na turma <strong>{$cursoAndTurma->turma}</strong>";
        $file = CalendarioProvaHorarioUtil::fileRedirect($calenderProva,$cursoAndTurma,$periodo);
        notification("fas fa-bell",$subject,$body,[$juri],$file,null);
    }
   

    public static function notificationMatriculationClass($calenderProva, $disciplina,$cursoAndTurma, $periodo){
        $subject = "Marcação de prova";
        $body = "Caro(a) utilizador(a) foi adicionado a disciplina <strong>{$disciplina->display_name}</strong> na avaliação <strong>{$calenderProva->display_name}</strong> no curso <strong>{$cursoAndTurma->curso}</strong> na turma <strong>{$cursoAndTurma->turma}</strong>";
        $userInClass = DB::table('matriculation_classes as mc')
                        ->join('matriculations as m','m.id','=','mc.matriculation_id')
                        ->select('m.user_id')
                        ->where('mc.class_id', $cursoAndTurma->turma_id)
                        ->distinct('m.user_id')
                        ->get();
        $file = CalendarioProvaHorarioUtil::fileRedirect($calenderProva,$cursoAndTurma,$periodo);
        foreach($userInClass as $student){
            notification("fas fa-bell",$subject,$body,[$student->user_id],$file,null);
        }
    }    
    
    public static function notificationMatriculationDisciplinaClass($calenderProva, $disciplina,$cursoAndTurma, $periodo){
        $subject = "Marcação de prova";
        $body = "Caro(a) utilizador(a) foi adicionado a disciplina <strong>{$disciplina->display_name}</strong> na avaliação <strong>{$calenderProva->display_name}</strong> no curso <strong>{$cursoAndTurma->curso}</strong> na turma <strong>{$cursoAndTurma->turma}</strong>";
        $userInClass = DB::table('matriculation_disciplines as md')
                        ->join('matriculations as m','m.id','=','md.matriculation_id')
                        ->where('md.discipline_id','', $disciplina->discipline_id)
                        ->select('m.user_id')
                        ->distinct('m.user_id')
                        ->get();
        $file = CalendarioProvaHorarioUtil::fileRedirect($calenderProva,$cursoAndTurma,$periodo);
        foreach($userInClass as $student){
            notification("fas fa-bell",$subject,$body,[$student->user_id],$file,null);
        }
    }   
    
    private static function fileRedirect($calenderProva, $cursoAndTurma, $periodo){
        $turmaJoin = $cursoAndTurma->turma_id."@".$cursoAndTurma->curso_id;
        $simestre = $calenderProva->simestre;                        
        return route('calendario_prova_horario.search.post')."?turma={$turmaJoin}&periodo={$periodo}&year_course={$cursoAndTurma->year}&simestre={$simestre}";
    }

}
