<?php
 
namespace App\Modules\Avaliations\Listeners;
 
use App\Modules\Avaliations\Events\GeneratePdfAvaliationEvent;
use App\Helpers\LanguageHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Avaliations\Models\Avaliacao;
use App\Modules\Avaliations\Models\AvaliacaoAluno;
use App\Modules\Avaliations\Models\AvaliacaoConfig;
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
use App\Modules\Users\Models\UserState;
use App\Modules\Users\Models\UserStateHistoric;
use App\NotaEstudante;
use Illuminate\Support\Str;
use Carbon\Carbon;
//use Barryvdh\DomPDF\PDF;
use App\Modules\GA\Models\LectiveYear;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Toastr;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;
use Yajra\DataTables\Facades\DataTables;
use PDF;
use App\Model\Institution;
use App\Modules\Avaliations\util\PautaGeralAvaliacoesUtil;
use App\Modules\Cms\Controllers\mainController;
use Log;
use App\Modules\Users\Controllers\MatriculationDisciplineListController;

class GeneratePdfAvaliationListener
{
 
 
    /**
     * Handle the event.
     *
     * @param  App\Modules\Avaliations\Events\GeneratePdfAvaliationEvent $event
     * @return void
     */
    public function handle(GeneratePdfAvaliationEvent $event)
    {
         
        try{

            $id = $event->id ?? null;
            $metrica_id = $event->metrica_id ?? null;
            $study_plan_id = $event->study_plan_id ?? null;
            $avaliacao_id = $event->avaliacao_id ?? null;
            $class_id = $event->class_id ?? null;
            $id_anoLectivo = $event->id_anoLectivo ?? null;
            $segunda_chamada = $event->segunda_chamada ?? false;
            $version = $event->version ?? 1;
       

        $comAcentos = array('à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'O', 'Ù', 'Ü', 'Ú');
      
        $semAcentos = array('a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U');

       
        //Pegar a turma e o ano Lectivo
        $turna_anoLectivo = DB::table('classes as turma')
            ->join('lective_years as ano', 'ano.id', 'turma.lective_year_id')
            ->leftJoin('lective_year_translations as Lectivo', function ($join) {
                $join->on('Lectivo.lective_years_id', '=', 'ano.id');
                $join->on('Lectivo.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('Lectivo.active', '=', DB::raw(true));
            })
            ->select(['turma.display_name as turma', 'turma.id as id_turma', 'turma.year as Anocurricular', 'Lectivo.display_name as anoLetivo'])
            ->where(['turma.id' => $class_id, 'turma.lective_year_id' => $id_anoLectivo])
            ->get();


            $m = Metrica::where('id',$metrica_id)->first();

       
            $MetricasCOde_dev = [$m->code_dev];
        
       
                    $students = AvaliacaoAluno::leftJoin('plano_estudo_avaliacaos as pea', 'pea.id', '=', 'avaliacao_alunos.plano_estudo_avaliacaos_id')
                    ->leftJoin('matriculations as mt', 'mt.user_id', '=', 'avaliacao_alunos.users_id')
                    ->when($segunda_chamada,function($join){
                     
                        $join->where('avaliacao_alunos.segunda_chamada',1);
                    })
                    ->when(!$segunda_chamada,function($join){
                     
                        $join->where('avaliacao_alunos.segunda_chamada',null);
                    })
                    ->leftJoin('matriculation_classes as mc', 'mc.matriculation_id', '=', 'mt.id')
                    ->leftJoin('matriculation_disciplines as mat_disc', 'mat_disc.matriculation_id', '=', 'mt.id')
                    ->leftJoin('user_parameters as u_p', function ($join) {
                     $join->on('mt.user_id', '=', 'u_p.users_id')
                        ->where('u_p.parameters_id', 1);
                        })  
                        ->leftJoin('user_parameters as u_p1', function ($join) {
                     $join->on('mt.user_id', '=', 'u_p1.users_id')
                        ->where('u_p1.parameters_id', 19);
                        })
                        ->leftJoin('user_parameters as sexo', function ($join) {
                            $join->on('mt.user_id', '=', 'sexo.users_id')
                               ->where('sexo.parameters_id', 2);
                               })
                        ->leftJoin('parameter_option_translations as sexo_value', function ($join) {
                                $join->on('sexo_value.parameter_options_id', '=', 'sexo.value');
                        })
                        
                     ->select(
                        
                        'avaliacao_alunos.nota as grade',
                        'u_p.value as nome',
                        'u_p1.value as mat',
                        'sexo_value.display_name as sexo'
                       )
                    // Aqui não seria o ID do Plano Estudo Avaliacaos?
                    ->where('pea.study_plan_editions_id', $study_plan_id)
                    ->where('avaliacao_alunos.metricas_id', $metrica_id)
                    ->where('pea.disciplines_id', $id)
                    ->where('mc.class_id', $class_id)
                    ->where('avaliacao_alunos.id_turma', $class_id)
                    ->orderBy('nome', 'ASC')
                    ->distinct() 
                    ->get();


        //pegar os utilizadores que lançaram as notas 
        $utilizadores = DB::table('avaliacao_alunos as avl')
        ->when($segunda_chamada,function($join){
            return $join->where('avl.segunda_chamada',1);
           
        })
        ->when(!$segunda_chamada,function($join){
            return $join->where('avl.segunda_chamada',null);
           
        })
            ->join('metricas as mt', 'mt.id', 'avl.metricas_id')
            ->leftJoin('user_parameters as u_p9', function ($q) {
                $q->on('avl.created_by', '=', 'u_p9.users_id')
                    ->where('u_p9.parameters_id', 1);
            })
            ->join('plano_estudo_avaliacaos as plano', 'plano.id', 'avl.plano_estudo_avaliacaos_id')
            ->select(['avl.created_by as criado_por','avl.created_at as criado_a', 'mt.nome as metricas', 'u_p9.value as criador_fullname','plano.disciplines_id as disciplina'])

            ->where('avl.id_turma',$class_id)
            ->whereIn('mt.code_dev', $MetricasCOde_dev)
            ->where('plano.disciplines_id',$id)
            ->distinct('avl.metricas_id')
            ->orderBy('avl.created_at', 'asc')
            ->get()
            ->unique('criado_por');

            //pegar os utilizadores que lançaram as notas 
        $coordenadores = DB::table('avaliacao_alunos as avl')
        ->join('model_has_roles as mr','mr.model_id','avl.updated_by')
       
        ->when($segunda_chamada,function($join){
            return $join->where('avl.segunda_chamada',1);
           
        })
        ->when(!$segunda_chamada,function($join){
            return $join->where('avl.segunda_chamada',null);
           
        })
            ->join('metricas as mt', 'mt.id', 'avl.metricas_id')
            ->leftJoin('user_parameters as u_p9', function ($q) {
                $q->on('avl.updated_by', '=', 'u_p9.users_id')
                    ->where('u_p9.parameters_id', 1);
            })
            ->join('plano_estudo_avaliacaos as plano', 'plano.id', 'avl.plano_estudo_avaliacaos_id')
            ->select(['avl.updated_by as actualizado_por','avl.updated_at as actualizado_a', 'mt.nome as metricas', 'u_p9.value as actualizador_fullname','plano.disciplines_id as disciplina'])

            ->where('avl.id_turma',$class_id)
            ->whereIn('mt.code_dev', $MetricasCOde_dev)
            ->where('plano.disciplines_id',$id)
            ->where('mr.role_id',12)
            ->distinct('avl.metricas_id')
            ->orderBy('avl.updated_at', 'asc')
            ->get();

        //Pegar a disciplina 
        $disciplina = DB::table('disciplines as disc')
            ->leftJoin('disciplines_translations as trans', function ($join) {
                $join->on('trans.discipline_id', '=', 'disc.id');
                $join->on('trans.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('trans.active', '=', DB::raw(true));
            })

            ->select(['disc.code as codigo', 'trans.display_name as disciplina'])
            ->where(['disc.id' => $id])
            ->get();
        $nova_disciplina = str_replace($comAcentos, $semAcentos, $disciplina[0]->disciplina);
        //Pegar área , regime e
        $regime = substr($disciplina[0]->codigo, -3, 1);
        $regimeFinal = "";
        if ($regime == "1" || $regime == "2") {
            $regimeFinal = $regime . 'º ' . "Semestre";
        } else if ($regime == "A") {
            $regimeFinal = "Anual";
        }
        
        $curso_id = DB::table('study_plan_editions as spe')
                        ->where('spe.id',$study_plan_id)
                    ->join('study_plans as sp','sp.id','spe.study_plans_id')
                   ->select('sp.courses_id')->first()->courses_id;
        //Dados do curso
       $course = DB::table('courses')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'courses.id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })
            ->select(['ct.display_name'])
            ->where('courses.id', $curso_id)
            ->first();



        //dados da instituição
        $institution = Institution::latest()->first();
        //Logotipo
        $Logotipo_instituicao = "https://" . $_SERVER['HTTP_HOST'] . "/storage/" . $institution->logotipo;
        // $titulo_documento = "Pauta de";
        // $documentoGerado_documento = "Documento gerado a";
        $documentoCode_documento = 10;

        $coordenador_id = Auth::user()->id;

        //Dados do chefe do gabinente
        $gabinete_chefe = User::whereHas('roles', function ($q) {
            $q->whereIn('id', [47]);
        })->leftJoin('user_parameters as u_p9', function ($q) {
            $q->on('users.id', '=', 'u_p9.users_id')
                ->where('u_p9.parameters_id', 1);
        })->first();
        //Coordenador

        $coordenador = DB::table('users')->leftJoin('user_parameters as u_p9', function ($q) {
            $q->on('users.id', '=', 'u_p9.users_id')
                ->where('u_p9.parameters_id', 1);
        })
            ->where('users.id', $coordenador_id)
            ->first();

            $e = null;

           
        $data = [
            'turma' => $turna_anoLectivo[0]->turma,
            'lectiveYear' => $turna_anoLectivo[0]->anoLetivo,
            'discipline_code' => $disciplina[0]->codigo . ' - ' . $disciplina[0]->disciplina,
            'discipline_name' => $disciplina[0]->disciplina,
            'regimeFinal' => $regimeFinal,
            'curso' => $course->display_name,
            'ano_curricular' => $turna_anoLectivo[0]->Anocurricular,
            
            'institution' => $institution,
        
            'logotipo' => $Logotipo_instituicao,
            'utilizadores' => $utilizadores,
            'documentoCode_documento' => $documentoCode_documento,
            'code_dev' => $m->code_dev,
            'students' => $students,
            'segunda_chamada' => $segunda_chamada,
            'coordenadores' => $coordenadores,
            'estatistica_tabela' => $e
        ];

       
       
            $pdf = PDF::loadView("Avaliations::avaliacao-aluno.pauta_grades.pdf.pautaMac", $data);
       
       
        $pdf->setOption('margin-top', '2mm');
        $pdf->setOption('margin-left', '2mm');
        $pdf->setOption('margin-bottom', '13mm');
        $pdf->setOption('margin-right', '2mm');
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('debug-javascript', true);
        $pdf->setOption('javascript-delay', 1000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);

        $pdf->setPaper('a4');
        $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
        $pdf->setOption('footer-html', $footer_html);
        //VERIFICA A REQUISÃO PARA SABER O TIPO DE PAUTA A SER ARMAZENADA
        //10->PAuta de Recurso | 20 -> Pauta de Exame | 30 -> Pauta Final

        $nome =$m->code_dev;
       
           
            $comAcentos = array(' ','à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'O', 'Ù', 'Ü', 'Ú');

            $semAcentos = array('_','a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U');
           
            $nova_disciplina = str_replace($comAcentos,$semAcentos,$disciplina[0]->codigo);
            $turma = str_replace($comAcentos,$semAcentos,$turna_anoLectivo[0]->turma);
            $name = str_replace($comAcentos,$semAcentos,$m->nome);

            $chamada = $segunda_chamada ? '2c' : '1c';
            $version = $version .'v';
            $fileName =  $nova_disciplina.'-'.$turma.'-'.$name.'-'.$chamada.'-'.$version .'.pdf';
           
            $path = '/storage/pautas-mac/' . $fileName;

            $sc = $segunda_chamada ? 1 : null;

            
            $file = storage_path('app/public/pautas-mac/') . $fileName;
            

            if(!file_exists($file)){

                DB::table('lancar_pauta')
                ->where('id_disciplina',$id)
                ->where('id_turma',$class_id)
                ->where('pauta_tipo',$nome)
                ->where('id_ano_lectivo',$id_anoLectivo)
                ->where('segunda_chamada',$sc)
                ->where('version',$version)
                ->update([
                    'path' => $path
                ]);

                $pdf->save($file);

            }
            else{
                DB::table('lancar_pauta')
                ->where('id_disciplina',$id)
                ->where('id_turma',$class_id)
                ->where('pauta_tipo',$nome)
                ->where('id_ano_lectivo',$id_anoLectivo)
                ->where('segunda_chamada',$sc)
                ->where('version',$version)
                ->update([
                    'path' => $path
                ]);
            }

           
            


            
            
       } catch (Exception | Throwable $e) {
            logError($e);
           dd($e);
        }
    }
}