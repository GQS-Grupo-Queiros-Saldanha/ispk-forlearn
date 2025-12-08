<?php

/**
 *	Avaliations Helper
 */




use App\Modules\Avaliations\Models\Avaliacao;
use App\Modules\Avaliations\Models\AvaliacaoAluno;
use App\Modules\Avaliations\Models\AvaliacaoAlunoHistorico;
use App\Modules\Avaliations\Models\Avaliations;
use App\Modules\Avaliations\Models\Metrica;
use App\Modules\Avaliations\Models\PlanoEstudoAvaliacao;
use App\Modules\Avaliations\Models\TipoAvaliacao;
use App\Modules\Avaliations\Models\TipoMetrica;
use App\Modules\GA\Models\Classes;
use App\Modules\GA\Models\Course;
use App\Modules\GA\Models\Discipline;
use App\Modules\GA\Models\StudyPlan;
use App\Modules\GA\Models\StudyPlanEdition;
use App\Modules\Payments\Models\ArticleRequest;
use App\Modules\Users\Models\Matriculation;
use App\Modules\Users\Models\User;
use App\Helpers\LanguageHelper;

//emarq
function isValidPautaLink($path)
{
    preg_match('/\/storage\/(pautas-[^\/]+)/', $path, $matches);
    // $validTypes = ['pautas-frequencia', 'pautas-exame', 'pautas-recurso', 'pautas-final', 'pautas-exame-especial', 'pautas-exame-oral', 'pautas-seminario', 'pautas_tfc'];
    $validTypes = ['pautas-frequencia', 'pautas-recurso', 'pautas-final', 'pautas-mac','pautas-exame-extraordinario'];

    return in_array($matches[1] ?? '', $validTypes) ? true : false;
}

function link_storage($link)
{
    if (isValidPautaLink($link)) {
        return basename($link);
    } else {
        return str_replace("/storage", "/storage/app/public", $link);
    }
}

function BoletimNotas_Student($id_anoLectivo, $id_curso, $mat_id, $id_disciplina = null, $id_turma = null)
{

    $aluno_disciplinas_notas = boletim_notas_alunos($id_anoLectivo, $id_curso, $mat_id, $id_disciplina, $id_turma);

    $collection = collect($aluno_disciplinas_notas);
    $dados = $collection->groupBy('code_disciplina', function ($item) {
        return ($item);
    });

    return $dados;
}





function boletim_notas_alunos($id_anoLectivo, $id_curso, $mat_id, $id_disciplina, $id_turma)
{

    //dd($id_anoLectivo, $id_curso, $mat_id, $id_disciplina, $id_turma);
    // PEGA O LIMITE DE PAGAMENTO DA PROPINA
    $validacao_proprina = DB::table('pauta_avaliation_student_shows')
        ->where('lective_year_id', $id_anoLectivo)
        ->first();

    $lectiveYearSelected = DB::table('lective_years')
        ->where('id', $id_anoLectivo)
        ->first();

    // IMPRMIR
    $mesActual = date('m') > 9 ? date('m') : date('m')[1];
    $diaActual = date('d');

    if ($validacao_proprina != null) {
        if ($validacao_proprina->quantidade_mes > 1) {
            $mesActual = $mesActual - $validacao_proprina->quantidade_mes;
        } else {
            $mesActual = $diaActual > $validacao_proprina->quatidade_day ? $mesActual : $mesActual - $validacao_proprina->quantidade_mes;
        }
    }
    //ESTÁ FUNCIONANDO - SÓ FALTA VALIDAR
    try {

        $avaliacaos_student = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
            ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
            ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'crs.id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })
            ->leftJoin('disciplines as dp', 'dp.id', '=', 'plano_estudo_avaliacaos.disciplines_id')
            ->leftJoin('disciplines_translations as dt', function ($join) {
                $join->on('dt.discipline_id', '=', 'dp.id');
                $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dt.active', '=', DB::raw(true));
            })
            ->leftJoin('avaliacaos as avl', 'avl.id', '=', 'plano_estudo_avaliacaos.avaliacaos_id')
            ->leftJoin('metricas as mt', 'mt.avaliacaos_id', '=', 'avl.id')
            ->leftJoin('avaliacao_alunos as avl_aluno', function ($join) {
                $join->on('avl_aluno.metricas_id', '=', 'mt.id');
                $join->on('avl_aluno.plano_estudo_avaliacaos_id', '=', 'plano_estudo_avaliacaos.id');
            })
            ->leftJoin('user_parameters as full_name', function ($join) {
                $join->on('avl_aluno.users_id', '=', 'full_name.users_id')
                    ->where('full_name.parameters_id', 1);
            })
            ->leftJoin('user_parameters as sexo', function ($join) {
                $join->on('avl_aluno.users_id', '=', 'sexo.users_id')
                    ->where('sexo.parameters_id', 2);
            })
            ->leftJoin('parameter_options as sexo_value', 'sexo_value.id', '=', 'sexo.value')
            ->leftJoin('user_parameters as up_meca', function ($join) {
                $join->on('avl_aluno.users_id', '=', 'up_meca.users_id')
                    ->where('up_meca.parameters_id', 19);
            })
            ->leftJoin('matriculations as matricula', 'matricula.user_id', '=', 'avl_aluno.users_id')
            ->leftJoin('matriculation_disciplines as matricula_disci', function ($join) {
                $join->on('matricula_disci.matriculation_id', '=', 'matricula.id');
                $join->on('matricula_disci.discipline_id', '=', 'dp.id');
            })
            ->leftJoin('matriculation_classes as mc', 'mc.matriculation_id', '=', 'matricula.id')

            ->select([
                'sexo_value.code as sexo',
                'avl.nome as Avaliacao_nome',
                'full_name.value as full_name',
                'avl_aluno.nota as nota_anluno',
                'up_meca.value as code_matricula',
                'avl_aluno.id as Avaliacao_aluno_id',
                'avl_aluno.id_turma as Avaliacao_aluno_turma',
                'avl_aluno.metricas_id as Avaliacao_aluno_Metrica',
                'avl_aluno.plano_estudo_avaliacaos_id as Avaliacao_PEA',
                'mt.id as Metrica_id',
                'avl_aluno.users_id as user_id',
                'dp.id as Disciplia_id',
                'dp.code as code_disciplina',
                'dt.display_name as display_name',
                'mt.nome as Metrica_nome',
                'mt.percentagem as percentagem_metrica',
                'stpeid.course_year as ano_curricular',
                'matricula_disci.exam_only as exam_only',
                'matricula_disci.matriculation_id as matriculation_id',
                'matricula.id as id_mat',
                // 'at.display_name as article_name',
                // 'artR.status as estado_do_mes',
                // 'artR.month as mes',
                'mt.code_dev as MT_CodeDV',
                'mc.matriculation_id as matricula_id',
                'mc.class_id',
                'stpeid.lective_years_id',
                'avl_aluno.segunda_chamada as segunda_chamada',
                'avl_aluno.presence as presence'
            ])
            ->where('stp.courses_id', $id_curso)
            ->where('stpeid.lective_years_id', $id_anoLectivo)
            ->when(isset($id_disciplina), function ($query) use ($id_disciplina) {
                $query->where('dp.id', $id_disciplina);
            })
            ->when(isset($id_turma), function ($query) use ($id_turma) {
                $query->where('mc.class_id', $id_turma);
            })
            //->where('code_dev.code', "propina")
            //->where('artR.month', $mesActual)
            ->where('mc.matriculation_id', $mat_id)
            //->whereNull('artR.deleted_at')    
            //->whereBetween('matricula.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
            //->whereBetween('artR.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])  
            ->orderBy('dp.code', 'asc')
            ->orderBy('full_name.value', 'asc')
            ->distinct()
            ->get();
    
        return $avaliacaos_student;
    } catch (\Exception $e) {
        dd($e);
    }
}

function macCalculate($pf1, $pf1_percentagem, $pf2, $pf2_percentagem, $oa, $oa_percentagem)
{
    return (int) round((($pf1 * $pf1_percentagem) + ($pf2 * $pf2_percentagem) + ($oa * $oa_percentagem)));
}

function macExameCalculate($calculo_mac, $mac_percentagem, $neen_percentagem, $neen)
{
    return (int) round(((float) $calculo_mac * $mac_percentagem + (float) $neen * $neen_percentagem));
}
function get_melhoria_notas($user_id, $lective_year, $type)
{
    return DB::table('melhoria_notas')
        ->where('user_id', $user_id)
        ->where('lective_year', $lective_year)
        ->where('finalist', $type)
        ->distinct('discipline_id')
        ->get();
}
