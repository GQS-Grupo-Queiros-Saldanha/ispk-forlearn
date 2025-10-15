<?php

namespace App\Modules\Users\Enum;

class CodevEnum {
    const CONFIRM = 1; // NULL
    const PROPINA = 2; // NULL
    const PRE_MATRICULA = 3; // NULL
    const INCRICAO_EXAME = 4; // inscrição por exame
    const INCRICAO_FREQUENCIA = 5; // inscrição por frequência
    const EXAME = 6; // exames
    const EXAME_RECURSO = 7; // Exame Recurso
    const INSS = 8; // imposto segurança social
    const IRT = 9; // imposto ao estado
    const TFC = 10; // TFC
    const GEE = 11; // GEE
    const COA = 12; // COA
    const ECO = 13; // ECO
    const CARTAO_ESTUDANTE = 14; // Cartão de estudante
    const EXAME_ESPECIAL = 15; // Exame especial
    const PEDIDO_TRANSFERENCIA_ENTRADA = 16; // pedido transferencia entrada
    const PEDIDO_TRANSFERENCIA_SAIDA = 17; // pedido transferencia saida
    const EQUIVALENCIA_DISCIPLINA = 18; // Equivalência disciplina
    const PROPINA_FINALISTA = 19; // propina finalista
    const TRABALHO_FIM_CURSO = 20; // trabalho_fim_curso
    const MUDANCA_CURSO = 21; // mudança de curso
    const ANULACAO_MATRICULA = 22; // anular matricula
    const MUDANCA_TURMA = 23; // mudança de turma
    const INSCRICAO_CURSO_ESPECIAL = 38;
    const TAXA_CURSO_ESPECIAL_INTERNO = 39;
    const TAXA_CURSO_ESPECIAL_EXTERNO = 40;
    const MELHORIA_NOTA = 41;
}