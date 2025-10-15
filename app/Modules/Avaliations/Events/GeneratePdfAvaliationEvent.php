<?php

namespace App\Modules\Avaliations\Events;


class GeneratePdfAvaliationEvent
{
  
     // Propriedades para os dados do evento
     public $id;
     public $metrica_id;
     public $study_plan_id;
     public $avaliacao_id;
     public $class_id;
     public $id_anoLectivo;
     public $segunda_chamada;
     public $version;
     // Construtor para inicializar os dados
     public function __construct($id, $metrica_id, $study_plan_id, $avaliacao_id, $class_id,$id_anoLectivo,$segunda_chamada,$version)
     {
         $this->id = $id;
         $this->metrica_id = $metrica_id;
         $this->study_plan_id = $study_plan_id;
         $this->class_id = $class_id;
         $this->id_anoLectivo = $id_anoLectivo;
         $this->segunda_chamada = $segunda_chamada;
         $this->version = $version;
         $this->avaliacao_id = $avaliacao_id;
     }
    
 
    }

