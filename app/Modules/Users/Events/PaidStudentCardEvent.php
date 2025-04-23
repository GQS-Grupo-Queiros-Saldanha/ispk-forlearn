<?php

namespace App\Modules\Users\Events;


class PaidStudentCardEvent
{
  
     // Propriedades para os dados do evento
     public $usuario;
 
     // Construtor para inicializar os dados
     public function __construct($usuario)
     {
         $this->usuario = $usuario;
     }
    
 
    }

