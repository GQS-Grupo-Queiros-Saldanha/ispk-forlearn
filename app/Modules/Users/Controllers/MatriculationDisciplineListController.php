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
use App\Modules\Avaliations\Models\PlanoEstudoAvaliacao;

class MatriculationDisciplineListController extends Controller
{

  public function index()
  {
    try {
      $lectiveYears = LectiveYear::with(['currentTranslation'])
        ->get();
      $currentData = Carbon::now();
      $lectiveYearSelected = DB::table('lective_years')
        ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
        ->first();
      $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
      //Curso 
      $courses = Course::with([
        'currentTranslation'
      ])->whereNull('deleted_by')
        ->get();

      return view('Users::list-disciplines-matriculations.index', compact('lectiveYears', 'lectiveYearSelected', 'courses'));
    } catch (Exception | Throwable $e) {
      logError($e);
      return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
    }
  }



  public function PegarDisciplina($id_curso, $anoCurricular)

  {
    //Disciplina --com plano de estudo
    return $dadosD = DB::table('study_plans as std_p')
      ->join('study_plans_has_disciplines as stp_d', 'stp_d.study_plans_id', 'std_p.id')
      ->join('study_plan_editions as stp_ed', 'stp_ed.study_plans_id', 'std_p.id')
      ->join('disciplines as disc', 'disc.id', 'stp_d.disciplines_id')
      ->join('disciplines_translations as dt', function ($join) {
        $join->on('dt.discipline_id', '=', 'disc.id');
        $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
        $join->on('dt.active', '=', DB::raw(true));
      })
      ->select(['disc.id', 'dt.display_name', 'disc.code'])
      ->where('std_p.courses_id', $id_curso)
      ->where('stp_d.years', $anoCurricular)
      ->where('stp_ed.course_year', $anoCurricular)
      ->whereNull('disc.deleted_by')
      ->whereNull('disc.deleted_at')
      ->distinct()
      ->get();



    // return $disciplina=DB::table('disciplines as disc')
    // ->join('disciplines_translations as dt', function ($join) {
    //     $join->on('dt.discipline_id', '=', 'disc.id');
    //     $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
    //     $join->on('dt.active', '=', DB::raw(true));
    // })
    // ->join('discipline_has_areas as pivot_area','pivot_area.discipline_id','disc.id')
    // ->join('discipline_areas as area','pivot_area.discipline_area_id','area.id')
    // ->join('matriculation_disciplines as disc_mat','disc_mat.discipline_id','disc.id')



    // ->select(['disc.id','dt.display_name','disc.code','area.code as area','area.id as id_exame','disc_mat.discipline_id as disciplina_matri'])
    // // ->where('std_p.courses_id',$id_curso)
    // ->whereNull('disc.deleted_by')
    // ->whereNull('disc.deleted_at')
    // ->where('area.id','!=',18)
    // ->distinct()
    // ->get();

  }



  public function ajaxUserDataPDF(Request $request){
    
 
    try {


      if (isset($request->disciplina)) {

        if (empty($request->classe) || empty($request->disciplina)) {
          Toastr::warning(__('Verifique se selecionou uma disciplina ou uma turma antes de gerar o PDF.'), __('toastr.warning'));
          return redirect()->back();
        }



        $request->discipline = $request->disciplina;


        $classes = DB::table("classes")
          ->select(["courses_id", "year"])
          ->where("id", $request->classe)
          ->first();

        // "course": "11",
        // "curricular_year": "1",
        // "discipline": "153",
        // "classe": "374",
        // "AnoLectivo": "9"

        $course = $classes->courses_id;
        $curricular_year = $classes->year;
        $discipline = explode(",", $request->disciplina)[2];
        $classe = $request->classe;
        $AnoLectivo = $request->AnoLectivo;
      } else {

        if (empty($request->classe) || empty($request->discipline)) {
          Toastr::warning(__('Verifique se selecionou uma disciplina ou uma turma antes de gerar o PDF.'), __('toastr.warning'));
          return redirect()->back();
        }

        $course = $request->course;
        $curricular_year = $request->curricular_year;
        $discipline = $request->discipline;
        $classe = $request->classe;
        $AnoLectivo = $request->AnoLectivo;
      }



      $courses = DB::table('courses as curso')
        ->join('courses_translations as ct', function ($join) {
          $join->on('ct.courses_id', '=', 'curso.id');
          $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
          $join->on('ct.active', '=', DB::raw(true));
        })
        ->where('curso.id', $course)
        ->get();


      //Consulta do Ano Lectivo
      $lectiveYearSelectedP = DB::table('lective_years')
        ->where('id', $AnoLectivo)
        ->get();


      //Vai ser a consulta geral
      $model = DB::table('matriculation_disciplines as mat_disc')
        ->join("matriculations as mat", 'mat.id', 'mat_disc.matriculation_id')
        ->join("matriculation_classes as mat_class", 'mat.id', 'mat_class.matriculation_id')
        ->join("classes as turma", 'mat_class.class_id', 'turma.id')
        ->join("users as user", 'mat.user_id', 'user.id')
        ->join("disciplines as disc", 'disc.id', 'mat_disc.discipline_id')
        ->join('disciplines_translations as dt', function ($join) {
          $join->on('dt.discipline_id', '=', 'disc.id');
          $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
          $join->on('dt.active', '=', DB::raw(true));
        })
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
        //Os que pagaram os emolumentos de confirmação de matricula e pré-matricula
        ->join("article_requests as user_emolumento", 'user_emolumento.user_id', 'user.id')
        ->join("articles as article_emolumento", 'user_emolumento.article_id', 'article_emolumento.id')
        ->join("code_developer as code_dev", 'code_dev.id', 'article_emolumento.id_code_dev')
        ->whereIn('code_dev.code', ["confirm", "p_matricula", "pedido_t_entrada"])
        ->where('user_emolumento.status', "total")
        ->whereBetween('article_emolumento.created_at', [$lectiveYearSelectedP[0]->start_date, $lectiveYearSelectedP[0]->end_date])
        ->select([
          'disc.id as id_disciplina',
          'disc.code as disciplina',
          'user_emolumento.status as pago',
          'article_emolumento.id as id_article',
          'article_emolumento.code as code_article',
          'turma.display_name as turma',
          'user.email',
          'mat.code',
          'up_meca.value as matricula',
          'up_bi.value as n_bi',
          'mat_disc.exam_only as e_f',
          'u_p.value as student',
          'dt.display_name as nome_disciplina',
          'turma.lective_year_id as id_anoLectivo',
          'mat.id as mat',
          'user.id as id',
          'mat.course_year'
        ])

        ->orderBy('student', 'ASC')
        ->distinct(['disc.id', 'up_bi.value', 'mat.code', 'u_p.value'])
        //->whereBetween('mat.created_at', [$lectiveYearSelectedP[0]->start_date, $lectiveYearSelectedP[0]->end_date])
        ->where("mat_disc.discipline_id", $discipline)
        ->where("turma.lective_year_id", $AnoLectivo)
        //->where("turma.id", $classe)
        ->whereNull('mat.deleted_at')

        ->get();
      

      $model->each(function ($item) use ($curricular_year) {
        $item->cadeirante = false;
        if ($curricular_year != $item->course_year)
          $item->cadeirante = true;
      });


      if (isset($request->status) && ($request->status == "0")) {
        $model = collect($model)->map(function ($item, $key) {
          $dividas = $this->get_payments($item->id_anoLectivo, $item->mat);
          if (isset($dividas) && ($dividas > 0)) {
            //verificar se é bolseiro
            if (
              DB::table('scholarship_holder as hold')
              ->where('hold.user_id', $item->id)
              ->where('are_scholarship_holder', 1)
              ->join('scholarship_entity as ent', function ($join) {
                $join->on('ent.id', 'hold.scholarship_entity_id')
                  ->where('type', 'BOLSA');
              })
              ->exists()
            )
              return $item;
          } else {
            return $item;
          }
        });
      }

      //Validação se for vazio a lista de alunos
      if ($model->isEmpty()) {
        Toastr::error(__('Não foram encontrado(s) aluno(s) matriculados nesta disciplina no ano lectivo e na turma selecionada.'), __('toastr.error'));
        return redirect()->back();
      }

      $regime = isset($request->regime) ? $request->regime : 0;
      $classe = DB::table("classes")
        ->where("id", $request->classe)
        ->select(["display_name"])
        ->first();

      $disciplines = DB::table("disciplines_translations")
        ->where("active", 1)
        ->where("language_id", 1)
        ->where("discipline_id", $discipline)
        ->select(["display_name"])
        ->first();

      $turmaC = $classe->display_name;
      $curso = $courses[0]->display_name;

      $nome_disciplina = $disciplines->display_name;


      $ano = $curricular_year;

      $lectiveYears = LectiveYear::with(['currentTranslation'])
        ->where('id', $request->AnoLectivo)
        ->get();
      $anoLectivo = $lectiveYears[0]->currentTranslation->display_name;
      // view("Users::list-disciplines-matriculations.pdf_lista")->with($id_discipline);

      $institution = Institution::latest()->first();
      $titulo_documento = "LISTA DE MATRICULADOS";
      $anoLectivo_documento = "Ano Lectivo :";
      $documentoGerado_documento = "Documento gerado a";
      $documentoCode_documento = 1;

      $model = $model->filter(function ($item) {
        return isset($item);
      });
      $metrica = $request->metrica;
      $pdf = PDF::loadView("Users::list-disciplines-matriculations.pdf_lista", compact(
        'model',
        'regime',
        'turmaC',
        'nome_disciplina',
        'curso',
        'lectiveYears',
        'ano',
        'institution',
        'titulo_documento',
        'anoLectivo_documento',
        'documentoGerado_documento',
        'documentoCode_documento',
        'metrica'
      ));


      $pdf->setOption('margin-top', '1mm');
      $pdf->setOption('margin-left', '1mm');
      $pdf->setOption('margin-bottom', '12mm');
      $pdf->setOption('margin-right', '1mm');
      $pdf->setOption('enable-javascript', true);
      $pdf->setOption('debug-javascript', true);
      $pdf->setOption('javascript-delay', 1000);
      $pdf->setOption('enable-smart-shrinking', true);
      $pdf->setOption('no-stop-slow-scripts', true);
      $pdf->setPaper('a4', 'landscape');

      $pdf_name = "LdM_" . "_" . $anoLectivo . '_' . $courses[0]->code . "_" . $request->curricular_yea . "_" . $turmaC . "_" . $nome_disciplina;
      // $footer_html = view()->make('Users::users.partials.pdf_footer', compact('institution'))->render();
      $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
      $pdf->setOption('footer-html', $footer_html);
      return $pdf->stream($pdf_name . '.pdf');
    } catch (Exception | Throwable $e) {
      return $e;
      logError($e);
      return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
    }
  }







  public function turma($curso, $id_anoLectivo, $anoCurricular)
  {
    // return $curso. $id_anoLectivo .$anoCurricular;

    $turma = DB::table('classes as class')
      ->select(['class.id as id', 'class.display_name as turma'])
      ->where('class.courses_id', $curso)
      ->where('class.year', $anoCurricular)
      ->where('class.lective_year_id', $id_anoLectivo)
      ->whereNull('class.deleted_by')
      ->whereNull('class.deleted_at')
      ->get();


    return $turma;
  }



  public function store()
  {
    return $request;
  }


  public static function get_payments($lective_year, $matriculations)
  {

    $payments = DB::table("matriculations as mat")
      ->join("article_requests as ar", 'ar.user_id', 'mat.user_id')
      ->join("articles as art", "art.id", "ar.article_id")
      ->where("mat.id", $matriculations)
      ->where("art.anoLectivo", $lective_year)
      ->where("ar.status", "pending")
      ->whereNotNull("ar.month")
      ->whereNotNull("ar.year")
      ->whereNull("art.deleted_at")
      ->whereNull("ar.deleted_at")
      ->select(["ar.base_value", "ar.status", "ar.month", "ar.year"])
      ->get();


    $payments = collect($payments)->map(function ($item, $key) {
      $month = [
        "",
        "Janeiro",
        "Fevereiro",
        "Março",
        "Abril",
        "Maio",
        "Junho",
        "Julho",
        "Agosto",
        "Setembro",
        "Outubro",
        "Novembro",
        "Dezembro"
      ];

      if (isset($item->month) && ($item->month > 0)) {

        $item->mes = " (" . $month[(int) $item->month] . " " . $item->year . ")";
      }

      return $item;
    });

    $config_divida = DB::table("config_divida_instituicao")
      ->where("status", "ativo")
      ->whereNull("deleted_at")
      ->select(["qtd_divida", "dias_exececao"])
      ->first();


    $dividas = collect($payments)->groupBy("status")->map(function ($item, $key) use ($config_divida) {

      $i = null;

      if ($key == "pending") {
        foreach ($item as $mensalidade) {
          if (isset($mensalidade->year) && ($mensalidade->year > 0)) {
            $hoje = Carbon::create(date("Y-m-d"));
            $limite = Carbon::create($mensalidade->year . "-" . $mensalidade->month . "-" . $config_divida->dias_exececao);
            if ($hoje >= $limite) {
              ++$i;
            }
          }
        }
      }

      if ($config_divida->qtd_divida < $i) {
        return $i;
      }
    });

    return isset($dividas["pending"]) ? $dividas["pending"] : null;
  }

  public function list_s_chamada()
  {
    try {
      $lectiveYears = LectiveYear::with(['currentTranslation'])
        ->get();
      $currentData = Carbon::now();
      $lectiveYearSelected = DB::table('lective_years')
        ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
        ->first();
      $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
      //Curso 
      $courses = Course::with([
        'currentTranslation'
      ])->whereNull('deleted_by')
        ->get();

      return view('Users::list-disciplines-matriculations.list-s-chamada', compact('lectiveYears', 'lectiveYearSelected', 'courses'));
    } catch (Exception | Throwable $e) {
      logError($e);
      return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
    }
  }


  public function s_chamada_pdf(Request $request)

  {

    try {


      if (isset($request->disciplina)) {

        if (empty($request->classe) || empty($request->disciplina)) {
          Toastr::warning(__('Verifique se selecionou uma disciplina ou uma turma antes de gerar o PDF.'), __('toastr.warning'));
          return redirect()->back();
        }



        $request->discipline = $request->disciplina;


        $classes = DB::table("classes")
          ->select(["courses_id", "year"])
          ->where("id", $request->classe)
          ->first();



        $course = $classes->courses_id;
        $curricular_year = $classes->year;
        $discipline = explode(",", $request->disciplina)[2];
        $classe = $request->classe;
        $AnoLectivo = $request->AnoLectivo;
      } else {

        if (empty($request->classe) || empty($request->discipline)) {
          Toastr::warning(__('Verifique se selecionou uma disciplina ou uma turma antes de gerar o PDF.'), __('toastr.warning'));
          return redirect()->back();
        }

        $course = $request->course;
        $curricular_year = $request->curricular_year;
        $discipline = $request->discipline;
        $classe = $request->classe;
        $AnoLectivo = $request->AnoLectivo;
      }



      $courses = DB::table('courses as curso')
        ->join('courses_translations as ct', function ($join) {
          $join->on('ct.courses_id', '=', 'curso.id');
          $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
          $join->on('ct.active', '=', DB::raw(true));
        })
        ->where('curso.id', $course)
        ->get();


      //Consulta do Ano Lectivo
      $lectiveYearSelectedP = DB::table('lective_years')
        ->where('id', $AnoLectivo)
        ->get();


      //Vai ser a consulta geral
      $model = DB::table("matriculations as mat")

        ->join("matriculation_classes as mat_class", 'mat.id', 'mat_class.matriculation_id')
        ->join("classes as turma", 'mat_class.class_id', 'turma.id')
        ->join("users as user", 'mat.user_id', 'user.id')

        ->join("tb_segunda_chamada_prova_parcelar as sc", 'sc.matriculation_id', 'mat.id')
        ->join("article_requests as user_emolumento", 'user_emolumento.user_id', 'user.id')
        ->join("articles as article_emolumento", 'user_emolumento.article_id', 'article_emolumento.id')
        ->join("code_developer as code_dev", 'code_dev.id', 'article_emolumento.id_code_dev')
        ->join("disciplines as disc", 'disc.id', 'sc.discipline_id')
        ->join('disciplines_translations as dt', function ($join) {
          $join->on('dt.discipline_id', '=', 'disc.id');
          $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
          $join->on('dt.active', '=', DB::raw(true));
        })
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
        //Os que pagaram os emolumentos de confirmação de matricula e pré-matricula
        ->whereIn('code_dev.code', ["prova_parcelar"])
        ->where('user_emolumento.status', "total")
        ->whereBetween('article_emolumento.created_at', [$lectiveYearSelectedP[0]->start_date, $lectiveYearSelectedP[0]->end_date])
        //fim dos pagos 


        ->select([
          'disc.id as id_disciplina',
          'disc.code as disciplina',
          'user_emolumento.status as pago',
          'article_emolumento.id as id_article',
          'article_emolumento.code as code_article',
          'turma.display_name as turma',
          'user.email',
          'mat.code',
          'up_meca.value as matricula',
          'up_bi.value as n_bi',

          'u_p.value as student',
          'dt.display_name as nome_disciplina',
          'turma.lective_year_id as id_anoLectivo',
          'mat.id as mat',
          'user.id as id'
        ])

        ->orderBy('student', 'ASC')
        ->distinct(['disc.id', 'up_bi.value', 'mat.code', 'u_p.value'])
        ->whereBetween('mat.created_at', [$lectiveYearSelectedP[0]->start_date, $lectiveYearSelectedP[0]->end_date])
        ->where("disc.id", $discipline)
        ->where("turma.lective_year_id", $AnoLectivo)
        ->where("turma.id", $classe)
        ->whereNull('mat.deleted_at')

        ->get();



      //Validação se for vazio a lista de alunos
      if ($model->isEmpty()) {
        Toastr::error(__('Não foram encontrado(s) aluno(s) nesta disciplina no ano lectivo e na turma selecionada.'), __('toastr.error'));
        return redirect()->back();
      }

      $regime = isset($request->regime) ? $request->regime : 0;
      $classe = DB::table("classes")
        ->where("id", $request->classe)
        ->select(["display_name"])
        ->first();

      $disciplines = DB::table("disciplines_translations")
        ->where("active", 1)
        ->where("language_id", 1)
        ->where("discipline_id", $discipline)
        ->select(["display_name"])
        ->first();

      $turmaC = $classe->display_name;
      $curso = $courses[0]->display_name;

      $nome_disciplina = $disciplines->display_name;


      $ano = $curricular_year;

      $lectiveYears = LectiveYear::with(['currentTranslation'])
        ->where('id', $request->AnoLectivo)
        ->get();
      $anoLectivo = $lectiveYears[0]->currentTranslation->display_name;
      // view("Users::list-disciplines-matriculations.pdf_lista")->with($id_discipline);

      $institution = Institution::latest()->first();
      $titulo_documento = "LISTA DE MATRICULADOS";
      $anoLectivo_documento = "Ano Lectivo :";
      $documentoGerado_documento = "Documento gerado a";
      $documentoCode_documento = 1;

      $pdf = PDF::loadView("Users::list-disciplines-matriculations.pdf_s_chamada", compact(
        'model',
        'regime',
        'turmaC',
        'nome_disciplina',
        'curso',
        'lectiveYears',
        'ano',
        'institution',
        'titulo_documento',
        'anoLectivo_documento',
        'documentoGerado_documento',
        'documentoCode_documento'
      ));


      $pdf->setOption('margin-top', '1mm');
      $pdf->setOption('margin-left', '1mm');
      $pdf->setOption('margin-bottom', '12mm');
      $pdf->setOption('margin-right', '1mm');
      $pdf->setOption('enable-javascript', true);
      $pdf->setOption('debug-javascript', true);
      $pdf->setOption('javascript-delay', 1000);
      $pdf->setOption('enable-smart-shrinking', true);
      $pdf->setOption('no-stop-slow-scripts', true);
      $pdf->setPaper('a4', 'landscape');

      $pdf_name = "LdM_" . "_" . $anoLectivo . '_' . $courses[0]->code . "_" . $request->curricular_yea . "_" . $turmaC . "_" . $nome_disciplina;
      // $footer_html = view()->make('Users::users.partials.pdf_footer', compact('institution'))->render();
      $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
      $pdf->setOption('footer-html', $footer_html);
      return $pdf->stream($pdf_name . '.pdf');
    } catch (Exception | Throwable $e) {

      logError($e);
      return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
    }
  }

  public function studentEvaluationList($type)
  {
    try {


      $lectiveYears = LectiveYear::with(['currentTranslation'])
        ->get();
      $currentData = Carbon::now();
      $lectiveYearSelected = DB::table('lective_years')
        ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
        ->first();
      $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
      //Curso 
      $courses = Course::with([
        'currentTranslation'
      ])->whereNull('deleted_by')
        ->get();





      return view('Users::list-disciplines-matriculations.list-avaliation', compact('lectiveYears', 'lectiveYearSelected', 'courses', 'type'));
    } catch (Exception | Throwable $e) {
      logError($e);
      return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
    }
  }


  public function studentEvaluationListPdf(Request $request, $type)
  {

    try {


      if (isset($request->disciplina)) {

        if ((empty($request->classe) || empty($request->discipline)) && !(empty($request->classe) && $type == 'melhoria_nota')) {

          Toastr::warning(__('Verifique se selecionou uma disciplina ou uma turma antes de gerar o PDF.'), __('toastr.warning'));
          return redirect()->back();
        }


        $request->discipline = $request->disciplina;


        $classes = DB::table("classes")
          ->select(["courses_id", "year"])
          ->where("id", $request->classe)
          ->first();



        $course = $classes->courses_id;
        $curricular_year = $classes->year;
        $discipline = explode(",", $request->disciplina)[2];
        $classe = $request->classe;
        $AnoLectivo = $request->AnoLectivo;
      } else {

        if ((empty($request->classe) || empty($request->discipline)) && !(empty($request->classe) && $type == 'melhoria_nota')) {

          Toastr::warning(__('Verifique se selecionou uma disciplina ou uma turma antes de gerar o PDF.'), __('toastr.warning'));
          return redirect()->back();
        }

        $course = $request->course;
        $curricular_year = $request->curricular_year;
        $discipline = $request->discipline;
        $classe = $request->classe;
        $AnoLectivo = $request->AnoLectivo;
      }



      $courses = DB::table('courses as curso')
        ->join('courses_translations as ct', function ($join) {
          $join->on('ct.courses_id', '=', 'curso.id');
          $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
          $join->on('ct.active', '=', DB::raw(true));
        })
        ->where('curso.id', $course)
        ->get();


      //Consulta do Ano Lectivo
      $lectiveYearSelectedP = DB::table('lective_years')
        ->where('id', $AnoLectivo)
        ->get();

      $metric = null;
      if ($request->metric != null) {
        $metric = DB::table('metricas')
          ->where('id', $request->metric)
          ->first()->nome;
      }



      switch ($type) {
        case 'segunda_chamada':
          $codev = ["prova_parcelar"];
          break;
        case 'recurso':
          $codev = ["exame_recurso"];
          break;
        case 'exame_especial':
          $codev = ["exame_especial"];
          break;
        case 'melhoria_nota':
          $codev = ["melhoria_nota"];
          break;
        case 'exame_extraordinario':
          $codev = ["exame_extraordinario"];
          break;
      }
      //devedor a implemetar
      /*$dividas = $this->get_payments($item->id_anoLectivo, $item->mat);
      if (isset($dividas) && ($dividas > 0)){
        // devedor 
      }*/

      //Vai ser a consulta geral
      $model = DB::table("matriculations as mat")

        ->leftJoin("matriculation_classes as mat_class", 'mat.id', 'mat_class.matriculation_id')
        ->leftJoin("classes as turma", 'mat_class.class_id', 'turma.id')
        ->join("users as user", 'mat.user_id', 'user.id')
        ->when($type == 'segunda_chamada', function ($query) {
          return $query->join("tb_segunda_chamada_prova_parcelar as sc", 'sc.matriculation_id', 'mat.id');
        })
        ->when($type == 'recurso', function ($query) {
          return $query->join("tb_recurso_student as sc", 'sc.matriculation_id', 'mat.id');
        })
        ->when($type == 'exame_especial', function ($query) {
          return $query->join("tb_exame_studant as sc", 'sc.id_user', 'user.id');
        })
        ->when($type == 'melhoria_nota', function ($query) {
          return $query->join("tb_exame_melhoria_nota as sc", 'sc.id_user', 'user.id')
            ->where('finalist', 0);
        })
        ->when($type == 'exame_extraordinario', function ($query) {
          return $query->join("tb_exame_melhoria_nota as sc", 'sc.id_user', 'user.id')
            ->where('finalist', 1);
        })
        ->join("article_requests as user_emolumento", 'user_emolumento.user_id', 'user.id')
        ->join("articles as article_emolumento", 'user_emolumento.article_id', 'article_emolumento.id')
        ->join("code_developer as code_dev", 'code_dev.id', 'article_emolumento.id_code_dev')
        ->when($type != 'exame_especial' && $type != 'melhoria_nota' && $type != 'exame_extraordinario', function ($query) {
          return $query->join("disciplines as disc1", 'disc1.id', 'sc.discipline_id')
            ->join('disciplines_translations as dt', function ($join) {
              $join->on('dt.discipline_id', '=', 'disc1.id');
              $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
              $join->on('dt.active', '=', DB::raw(true));
            });
        })
        ->when($type == 'exame_especial' || $type == 'melhoria_nota' || $type == 'exame_extraordinario', function ($query) {
          return $query->join("disciplines as disc2", 'disc2.id', 'sc.id_discipline')
            ->join('disciplines_translations as dt', function ($join) {
              $join->on('dt.discipline_id', '=', 'disc2.id');
              $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
              $join->on('dt.active', '=', DB::raw(true));
            });
        })

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
        //Os que pagaram os emolumentos de confirmação de matricula e pré-matricula
        ->whereIn('code_dev.code', $codev)
        ->where('user_emolumento.status', "total")
        ->whereBetween('article_emolumento.created_at', [$lectiveYearSelectedP[0]->start_date, $lectiveYearSelectedP[0]->end_date])
        //fim dos pagos 


        ->select([
          $type == 'exame_especial' || $type == 'melhoria_nota' || $type == 'exame_extraordinario' ? 'disc2.id as id_disciplina' : 'disc1.id as id_disciplina',
          $type == 'exame_especial' || $type == 'melhoria_nota' || $type == 'exame_extraordinario' ? 'disc2.code as disciplina' : 'disc1.code as disciplina',
          'user_emolumento.status as pago',
          'article_emolumento.id as id_article',
          'article_emolumento.code as code_article',
          'turma.display_name as turma',
          'user.email',
          'mat.code',
          'up_meca.value as matricula',
          'up_bi.value as n_bi',

          'u_p.value as student',
          'dt.display_name as nome_disciplina',
          'turma.lective_year_id as id_anoLectivo',
          'mat.id as mat',
          'user.id as id'
        ])

        ->orderBy('student', 'ASC')
        ->distinct([
          $type == 'exame_especial' || $type == 'melhoria_nota' || $type == 'exame_extraordinario' ? 'disc2.id' : 'disc1.id',
          'up_bi.value',
          'mat.code',
          'u_p.value'
        ])
        ->whereBetween('mat.created_at', [$lectiveYearSelectedP[0]->start_date, $lectiveYearSelectedP[0]->end_date])
        ->where(
          $type == 'exame_especial' || $type == 'melhoria_nota' || $type == 'exame_extraordinario' ? 'disc2.id' : 'disc1.id',
          $discipline
        )
        ->whereNull('mat.deleted_at');





      $model = $type == 'exame_especial' || $type == 'melhoria_nota' ?
        $model->get() :
        $model->where("turma.lective_year_id", $AnoLectivo)
        ->where("turma.id", $classe)
        ->get();

      if ($type == 'recurso') {
          $model = $model->reject(function ($item) {
            $dividas = $this->get_payments($item->id_anoLectivo, $item->mat);

            $bolseiro = DB::table('scholarship_holder as hold')
              ->where('hold.user_id', $item->id)
              ->where('are_scholarship_holder', 1)
              ->join('scholarship_entity as ent', function ($join) {
                $join->on('ent.id', 'hold.scholarship_entity_id')
                  ->where('type', 'BOLSA');
              })
              ->exists();

            return isset($dividas) && ($dividas > 0) && !$bolseiro;
          });
      }


      if ($type == 'segunda_chamada') {

        $model = $model->reject(function ($item) {

          $dividas = $this->get_payments($item->id_anoLectivo, $item->mat);

          $bolseiro = DB::table('scholarship_holder as hold')
            ->where('hold.user_id', $item->id)
            ->where('are_scholarship_holder', 1)
            ->join('scholarship_entity as ent', function ($join) {
              $join->on('ent.id', 'hold.scholarship_entity_id')
                ->where('type', 'BOLSA');
            })
            ->exists();

          return isset($dividas) && ($dividas > 0) && !$bolseiro;
        });
      }


      // Validação se for vazio a lista de alunos
      if ($model->isEmpty()) {
        $text = 'Não foram encontrado(s) aluno(s) nesta disciplina e turma no ano lectivo selecionado.';
        $text = $type == 'melhoria_nota' ? str_replace(' e turma', '', $text) : $text;
        Toastr::error(__($text), __('toastr.error'));
        return redirect()->back();
      }

      $regime = isset($request->regime) ? $request->regime : 0;

      if ($request->classe != null) {

        $classe = DB::table("classes")
          ->where("id", $request->classe)
          ->select(["display_name"])
          ->first();

        $turmaC = $classe->display_name;
      } else {
        $turmaC = null;
      }

      $disciplines = DB::table("disciplines_translations")
        ->where("active", 1)
        ->where("language_id", 1)
        ->where("discipline_id", $discipline)
        ->select(["display_name"])
        ->first();

      $curso = $courses[0]->display_name;

      $nome_disciplina = $disciplines->display_name;


      $ano = $curricular_year;

      $lectiveYears = LectiveYear::with(['currentTranslation'])
        ->where('id', $request->AnoLectivo)
        ->get();
      $anoLectivo = $lectiveYears[0]->currentTranslation->display_name;
      // view("Users::list-disciplines-matriculations.pdf_lista")->with($id_discipline);

      $institution = Institution::latest()->first();

      $anoLectivo_documento = "Ano Lectivo :";
      $documentoGerado_documento = "Documento gerado a";
      $documentoCode_documento = 1;



      $pdf = PDF::loadView("Users::list-disciplines-matriculations.pdf-list-avaliation", compact(
        'model',
        'regime',
        'turmaC',
        'nome_disciplina',
        'curso',
        'lectiveYears',
        'ano',
        'institution',
        'type',
        'anoLectivo_documento',
        'documentoGerado_documento',
        'documentoCode_documento',
        'metric'
      ));


      $pdf->setOption('margin-top', '1mm');
      $pdf->setOption('margin-left', '1mm');
      $pdf->setOption('margin-bottom', '12mm');
      $pdf->setOption('margin-right', '1mm');
      $pdf->setOption('enable-javascript', true);
      $pdf->setOption('debug-javascript', true);
      $pdf->setOption('javascript-delay', 1000);
      $pdf->setOption('enable-smart-shrinking', true);
      $pdf->setOption('no-stop-slow-scripts', true);
      $pdf->setPaper('a4', 'landscape');

      switch ($type) {
        case 'segunda_chamada':
          $doc_name = $metric . '_segunda_chamada';
          break;
        case 'recurso':
          $doc_name = 'recurso';
          break;
        case 'exame_especial':
          $doc_name = 'exame_especial';
          break;
        case 'melhoria_nota':
          $doc_name = 'melhoria_nota';
          break;
        case 'exame_extraordinario':
          $doc_name = 'exame_extraordinario';
          break;
      }

      $pdf_name = $doc_name  . "_" . "_" . $anoLectivo . '_' . $courses[0]->code . "_" . $request->curricular_yea . "_" . $turmaC . "_" . $nome_disciplina;
      // $footer_html = view()->make('Users::users.partials.pdf_footer', compact('institution'))->render();
      $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
      $pdf->setOption('footer-html', $footer_html);
      return $pdf->stream($pdf_name . '.pdf');
    } catch (Exception | Throwable $e) {

      logError($e);
      return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
    }
  }


  public function avaliacoes($id_disciplina, $anoLectivo)
  {
    $avaliacaos = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
      ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
      ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
      ->leftJoin('courses_translations as ct', function ($join) {
        $join->on('ct.courses_id', '=', 'crs.id');
        $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
        $join->on('ct.active', '=', DB::raw(true));
      })->leftJoin('disciplines as dp', 'dp.id', '=', 'plano_estudo_avaliacaos.disciplines_id')
      ->leftJoin('disciplines_translations as dt', function ($join) {
        $join->on('dt.discipline_id', '=', 'dp.id');
        $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
        $join->on('dt.active', '=', DB::raw(true));
      })->leftJoin('avaliacaos as avl', 'avl.id', '=', 'plano_estudo_avaliacaos.avaliacaos_id')
      ->leftJoin('avaliacao_aluno_historicos', 'avaliacao_aluno_historicos.plano_estudo_avaliacaos_id', '=', 'plano_estudo_avaliacaos.id')
      ->join('calendario_prova as c_p', 'c_p.id_avaliacao', '=', 'avl.id')
      ->select(['avl.id as id', 'avl.nome as nome', 'dp.code as discipline_code', 'c_p.date_start as inicio', 'c_p.data_end as fim', 'c_p.simestre'])
      ->where('dp.id', $id_disciplina)
      ->where('c_p.deleted_by', null)
      ->where('c_p.lectiveYear', $anoLectivo)
      ->whereNotIn('avl.code_dev', ['recursos'])
      ->distinct('');

    //Periodo da disciplina (saber se é anual ou simestral)
    $period_disciplina = DB::table('disciplines')
      ->where('id', $id_disciplina)
      ->get();

    $Simestre = $period_disciplina->map(function ($item, $key) {
      $periodo = substr($item->code, -3, 1);
      if ($periodo == "1") {
        return 1;
      }
      if ($periodo == "2") {
        return 4;
      }
      if ($periodo == "A") {
        return 2;
      } else {
        return 0;
      }
    });

    $avaliacaos = $avaliacaos
      ->whereRaw('"' . date("Y-m-d") . '" between `date_start` and `data_end`')
      ->where('simestre', $Simestre)
      ->get();





    return $avaliacaos;
  }
}
