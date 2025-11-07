<?php

namespace App\Modules\Users\Controllers;

use App\Helpers\LanguageHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\Classes;
use App\Modules\GA\Models\Course;
use App\Modules\GA\Models\StudyPlan;
use App\Modules\Payments\Models\Article;
use App\Modules\Payments\Models\DisciplineArticle;
use App\Modules\Users\Models\Matriculation;
use App\Modules\Users\Models\UserCandidate;
use App\Modules\Users\Models\User;
use App\Modules\Users\Models\UserState;
use App\Modules\Users\Models\UserStateHistoric;
use App\Modules\Users\Requests\MatriculationRequest;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;
use PDF;
use App\Modules\GA\Models\LectiveYear;
use App\Model\Institution;


class MatriculationClasseListController extends Controller
{
  
    public function index()
    {
        try {
         
           $lectiveYears = LectiveYear::with(['currentTranslation'])
             ->get();
            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                            ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
                            ->first();
            $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
            //Curso 
        $courses = Course::with([
                'currentTranslation'
            ])->whereNull('deleted_by')
            ->get();

            return view('Users::list-class-matriculation.index', compact('lectiveYears', 'lectiveYearSelected','courses'));
        }
        
        catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    
    public function PegarDisciplina($id_curso,$anoCurricular)
    
    {
            //Disciplina --com plano de estudo
            return $dadosD=DB::table('study_plans as std_p')
            ->join('study_plans_has_disciplines as stp_d','stp_d.study_plans_id','std_p.id')
            ->join('study_plan_editions as stp_ed','stp_ed.study_plans_id','std_p.id')
            ->join('disciplines as disc','disc.id','stp_d.disciplines_id')
            ->join('disciplines_translations as dt', function ($join) {
                $join->on('dt.discipline_id', '=', 'disc.id');
                $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dt.active', '=', DB::raw(true));
            })
            ->select(['disc.id','dt.display_name','disc.code'])
            ->where('std_p.courses_id',$id_curso)
            ->where('stp_d.years',$anoCurricular)
            ->where('stp_ed.course_year',$anoCurricular)
            ->whereNull('disc.deleted_by')
            ->whereNull('disc.deleted_at')
            ->distinct()
            ->get();


    }



 public function ajaxUserDataPDF(Request $request){
    try {
        
        if (empty($request->classe)) {
            Toastr::error(__('Verifique se selecionou uma turma antes de gerar o PDF.'), __('toastr.error'));
            return redirect()->back();
        }

        // 🔹 Buscar curso
        $courses = DB::table('courses as curso')
            ->join('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'curso.id')
                     ->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()))
                     ->on('ct.active', '=', DB::raw(true));
            })
            ->where('curso.id', $request->course)
            ->get();

        // 🔹 Ano lectivo
        $lectiveYear = DB::table('lective_years')
            ->where('id', $request->AnoLectivo)
            ->first();

        if (!$lectiveYear) {
            Toastr::error(__('Ano lectivo inválido.'), __('toastr.error'));
            return redirect()->back();
        }

        // 🔹 Consulta de alunos (sem duplicação)
        $model = DB::table('matriculations as mat')
            ->join('matriculation_classes as mc', 'mc.matriculation_id', '=', 'mat.id')
            ->join('classes as turma', 'mc.class_id', '=', 'turma.id')
            ->join('users as user', 'mat.user_id', '=', 'user.id')

            // Parâmetros do utilizador
            ->leftJoin('user_parameters as u_p', function ($join) {
                $join->on('user.id', '=', 'u_p.users_id')
                     ->where('u_p.parameters_id', 1);
            })
            ->leftJoin('user_parameters as up_meca', function ($join) {
                $join->on('user.id', '=', 'up_meca.users_id')
                     ->where('up_meca.parameters_id', 19);
            })
            ->leftJoin('user_parameters as up_bi', function ($join) {
                $join->on('user.id', '=', 'up_bi.users_id')
                     ->where('up_bi.parameters_id', 14);
            })

            // Apenas verificar se o aluno tem disciplinas válidas
            ->whereExists(function ($q) use ($request) {
                $q->select(DB::raw(1))
                  ->from('matriculation_disciplines as md')
                  ->join('study_plans_has_disciplines as st', 'st.disciplines_id', '=', 'md.discipline_id')
                  ->whereRaw('md.matriculation_id = mat.id')
                  ->where('md.exam_only', $request->regime ?? 0)
                  ->where('st.years', $request->curricular_year);
            })

            // Verificar se existe pagamento confirm/p_matricula (ou nenhum)
            ->where(function ($q) use ($lectiveYear) {
                $q->whereExists(function ($sub) use ($lectiveYear) {
                    $sub->select(DB::raw(1))
                        ->from('article_requests as ar')
                        ->join('articles as art', 'art.id', '=', 'ar.article_id')
                        ->join('code_developer as cd', 'cd.id', '=', 'art.id_code_dev')
                        ->whereRaw('ar.user_id = user.id')
                        ->whereIn('cd.code', ['confirm', 'p_matricula'])
                        ->where('ar.status', 'total')
                        ->whereBetween('art.created_at', [
                            $lectiveYear->start_date,
                            $lectiveYear->end_date
                        ]);
                })
                ->orWhereRaw('NOT EXISTS (SELECT 1 FROM article_requests WHERE user_id = user.id)');
            })

            ->where('mat.lective_year', $lectiveYear->id)
            ->where('turma.lective_year_id', $request->AnoLectivo)
            ->where('turma.id', $request->classe)
            ->whereNull('mat.deleted_at')

            ->select([
                'user.id as user_id',
                'u_p.value as student',
                'up_bi.value as n_bi',
                'up_meca.value as matricula',
                'user.email',
                'mat.id as mat_id',
                'mat.code as code_matricula',
                'mat.course_year',
                'turma.display_name as turma',
                'turma.lective_year_id as id_anoLectivo'
            ])
            ->groupBy(
                'user.id',
                'u_p.value',
                'up_bi.value',
                'up_meca.value',
                'user.email',
                'mat.id',
                'mat.code',
                'mat.course_year',
                'turma.display_name',
                'turma.lective_year_id'
            )
            ->orderBy('student', 'ASC')
            ->get();

        if ($model->isEmpty()) {
            Toastr::error(__('Não foram encontrados alunos matriculados na turma selecionada.'), __('toastr.error'));
            return redirect()->back();
        }

        // 💰 Filtro de dívidas e bolsas (mantido)
        if (isset($request->status) && $request->status == "0") {
            $model = $model->filter(function ($item) {
                $dividas = $this->get_payments($item->id_anoLectivo, $item->mat_id);
                if ($dividas > 0) {
                    $isBolseiro = DB::table('scholarship_holder as hold')
                        ->join('scholarship_entity as ent', 'ent.id', '=', 'hold.scholarship_entity_id')
                        ->where('hold.user_id', $item->user_id)
                        ->where('hold.are_scholarship_holder', 1)
                        ->where('ent.type', 'BOLSA')
                        ->exists();
                    return $isBolseiro;
                }
                return true;
            });
        }

        // 🖨️ Preparar PDF
        $classe = DB::table('classes')->where('id', $request->classe)->first();
        $turmaC = $classe->display_name ?? 'Turma sem nome';
        $curso = $courses[0]->display_name ?? 'Curso sem nome';
        $regime = $request->regime ?? 0;
        $ano = $request->curricular_year;

        $lectiveYears = LectiveYear::with(['currentTranslation'])
            ->where('id', $request->AnoLectivo)
            ->get();

        $anoLectivo = $lectiveYears[0]->currentTranslation->display_name ?? 'Ano Lectivo';
        $institution = Institution::latest()->first();

        $pdf = PDF::loadView("Users::list-class-matriculation.pdf_lista", compact(
            'model',
            'regime',
            'turmaC',
            'curso',
            'lectiveYears',
            'ano',
            'institution'
        ));

        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption('margin-top', '1mm');
        $pdf->setOption('margin-bottom', '12mm');
        $pdf->setOption('footer-html', view('Reports::pdf_model.pdf_footer', compact('institution'))->render());

        $pdf_name = "LdM_" . $anoLectivo . '_' . $courses[0]->code . "_" . $ano . "_" . $turmaC;
        return $pdf->stream($pdf_name . '.pdf');

    } catch (Exception | Throwable $e) {
        logError($e);
        return request()->ajax()
            ? response()->json($e->getMessage(), 500)
            : abort(500);
    }
}



    }

