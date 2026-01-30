<?php

namespace App\Modules\Avaliations\Controllers;

use App\Helpers\LanguageHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
use LDAP\Result;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

use PDF;
use App\Model\Institution;
use App\Modules\Avaliations\Exports\GraduadosExport;
use App\Modules\Users\Enum\ParameterEnum;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class AvaliacaoEstatisticaController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
              //Pegar o ano lectivo na select
              $lectiveYears = LectiveYear::with(['currentTranslation'])
              ->get();
              $currentData = Carbon::now();
              $lectiveYearSelected = DB::table('lective_years')
                  ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
                  ->first();
              $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
              $courses = Course::with(['currentTranslation'])->get();

              $Pauta=DB::table('tb_estatistic_avaliation')
              ->select(['pautaType as PautaCode','descrition_type_p as NamePauta'])
              ->distinct()
              ->get();

              $data = [
                         'courses' => $courses,
                         'lectiveYearSelected'=>$lectiveYearSelected,
                         'lectiveYears'=>$lectiveYears,
                         'Pautas'=>$Pauta
                      ];



          return view("Avaliations::avaliacao-estatistica.index")->with($data);

        } catch (Exception | Throwable $e) {

          logError($e);
          return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

        public function candidato()
    {
        try {
            $courses = Course::with([
                'currentTranslation'
            ])
             ->where('id','!=',22)
             ->where('id','!=',18)
            ;

            //if (auth()->user()->hasRole('teacher')) {
            //$teacherCourses = auth()->user()->courses()->pluck('id')->all();
            //$courses = $courses->whereIn('id', $teacherCourses);
            //}

            $lectiveYears = LectiveYear::with(['currentTranslation'])
            ->get();

            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
                ->first();
            $lectiveYearSelected = $lectiveYearSelected->id ?? 6;

            $Pauta = DB::table('tb_estatistic_avaliation')
            ->select(['pautaType as PautaCode', 'descrition_type_p as NamePauta'])
            ->distinct()
            ->get();

           $data = [
                'courses' => $courses->get(),
                'lectiveYearSelected'=>$lectiveYearSelected,
                'lectiveYears'=>$lectiveYears,
                'lectiveYears'=>$lectiveYears,
                'Pautas'=> $Pauta
            ];




            return view("Avaliations::avaliacao-estatistica.estatistica_candidato")->with($data);
        } catch (Exception | Throwable $e) {

            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

       public function anual()
    {
        try {
            //Pegar o ano lectivo na select
            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();
            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();
            $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
            $courses = Course::with(['currentTranslation'])->get();

            $Pauta = DB::table('tb_estatistic_avaliation')
                ->select(['pautaType as PautaCode', 'descrition_type_p as NamePauta'])
                ->distinct()
                ->get();

            $data = [
                'courses' => $courses,
                'lectiveYearSelected' => $lectiveYearSelected,
                'lectiveYears' => $lectiveYears,
                'Pautas' => $Pauta
            ];



            return view("Avaliations::avaliacao-estatistica.estatistica_ano_curso")->with($data);
        } catch (Exception | Throwable $e) {

            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }



    public function graduado()
    {
        try {
              //Pegar o ano lectivo na select
              $lectiveYears = LectiveYear::with(['currentTranslation'])
              ->get();
              $currentData = Carbon::now();
              $lectiveYearSelected = DB::table('lective_years')
                  ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
                  ->first();
              $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
              $courses = Course::with(['currentTranslation'])->get();



              $AnoLectivo_Percurso=DB::table('new_old_grades')
                ->select(['lective_year as Ano'])
                ->distinct()
                ->orderBy("Ano","ASC")
                ->get();

                $data = [
                         'courses' => $courses,
                         'lectiveYears'=>$lectiveYears,
                         'lectiveYearSelected'=>$lectiveYearSelected,
                         'lectiveYearsP'=>$AnoLectivo_Percurso
                      ];



          return view("Avaliations::avaliacao-estatistica.graduado")->with($data);

        } catch (Exception | Throwable $e) {

          logError($e);
          return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }







    public function filter_Pauta()
    {
        try {
              //Pegar o ano lectivo na select
              $lectiveYears = LectiveYear::with(['currentTranslation'])
              ->get();
              $currentData = Carbon::now();
              $lectiveYearSelected = DB::table('lective_years')
                  ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
                  ->first();
              $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
              $courses = Course::with(['currentTranslation'])->get();



              $AnoLectivo_Percurso=DB::table('new_old_grades')
                ->select(['lective_year as Ano'])
                ->distinct()
                ->orderBy("Ano","ASC")
                ->get();

              $data = [
                         'courses' => $courses,
                         'lectiveYearSelected'=>$lectiveYearSelected,
                         'lectiveYearsP'=>$AnoLectivo_Percurso
                      ];



          return view("Avaliations::avaliacao-estatistica.filter-estatistica")->with($data);

        } catch (Exception | Throwable $e) {

          logError($e);
          return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }





    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {

            return view("Avaliations::avaliacao-aluno.create-avaliacao-aluno");

        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */



    public function store(Request $request)
    {

      //Bem no final de lançar as notas alguém tem que fechar elas.

        try {





      } catch (Exception | Throwable $e) {
            Toastr::error($e->getMessage(), __('toastr.error'));
            logError($e);
            return $e;
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }


    }










    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    public function show($id)
    {
        try {
            return view("Avaliations::avaliacao-aluno.show-avaliacao-aluno");
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
















    public function update(Request $request, $id)
    {
        return $id;
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */





    public function destroy($id)
    {
        try {
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    public function PegarAnoCurricular($id_cursos)
    {
        try {
            $id_cursos_c=explode(",",$id_cursos);
            $MaxYear=DB::table('courses')
                    ->whereIn('id',$id_cursos_c)
                    ->select("courses.*")
                    ->max('duration_value');
             return response()->json($MaxYear);
        } catch (Exception | Throwable $e) {
            logError($e);
            return $e;
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }






    public function PegarDisciplinaGraduado($id_cursos,$anoLectivo){
        try {

           $id_cursos_c=$id_cursos;
            //Duration curso
            $curso=DB::table('courses')
            ->where('id',$id_cursos_c)
            ->first();
            //FimDuration curso

            if($curso){

                $disciplina=DB::table('study_plan_editions as spd')
                ->leftJoin('study_plan_edition_disciplines as disc_spde','disc_spde.study_plan_edition_id','spd.id')
                ->leftJoin('study_plans as stdp','stdp.id','spd.study_plans_id')
                ->leftJoin('disciplines as disci','disc_spde.discipline_id','disci.id')

                ->leftJoin('disciplines_translations as dt', function ($join) {
                    $join->on('dt.discipline_id', '=', 'disci.id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                    })
                ->where('stdp.courses_id',$id_cursos_c)
                ->where('spd.course_year',$curso->duration_value)
                ->where('spd.lective_years_id',$anoLectivo)
                ->select([
                    'dt.discipline_id as id_disciplina',
                    'spd.course_year as Anocurricular',
                    'dt.display_name as nome_disciplina',
                    'spd.lective_years_id as anoLectivo',
                    'stdp.code as curso',
                    'disci.code as code_disciplina'
                ])->distinct('dt.display_name')
                ->whereIn('dt.display_name', function($query) use ($id_cursos_c, $anoLectivo, $curso){
                    $query->select(DB::raw('dt.display_name'))
                    ->from('study_plan_editions as spd')
                    ->leftJoin('study_plan_edition_disciplines as disc_spde','disc_spde.study_plan_edition_id','spd.id')
                    ->leftJoin('study_plans as stdp','stdp.id','spd.study_plans_id')
                    ->leftJoin('disciplines as disci','disc_spde.discipline_id','disci.id')
                    ->leftJoin('disciplines_translations as dt', function ($join) {
                        $join->on('dt.discipline_id', '=', 'disci.id');
                        $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('dt.active', '=', DB::raw(true));
                    })
                    ->where('stdp.courses_id',$id_cursos_c)
                    ->where('spd.course_year',$curso->duration_value)
                    ->where('spd.lective_years_id',$anoLectivo);
                })
                ->orderBy('spd.course_year','ASC')
                ->orderBy('stdp.code','ASC')
                ->first();

          return response()->json($disciplina);

        }else{

            return "curso não encontrado";
        }



        } catch (Exception | Throwable $e) {
            logError($e);
            return $e;
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }








    public function PegarDisciplina($id_cursos)
    {
        try {

           $id_cursos_c=$id_cursos;

           $disciplina=DB::table('disciplines as disci','disc_spde.discipline_id','disci.id')
           ->leftJoin('disciplines_translations as dt', function ($join) {
              $join->on('dt.discipline_id', '=', 'disci.id');
              $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
              $join->on('dt.active', '=', DB::raw(true));
          })
           ->where('disci.courses_id',$id_cursos_c)
           ->select(['dt.discipline_id as id_disciplina','dt.display_name as nome_disciplina','disci.code as code_disciplina'])
           ->distinct('dt.display_name')
           ->orderBy('disci.courses_id','ASC')
           ->get();



             return response()->json($disciplina);
        } catch (Exception | Throwable $e) {
            logError($e);
            return $e;
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    public function PegarDisciplinasAnoCurricular($id_cursos,$AnoCurricular,$anoLectivo)
    {
        try {
             $Anos_academico=explode(",",$AnoCurricular);
             $id_cursos_c=explode(",",$id_cursos);

             $disciplina=DB::table('study_plan_editions as spd')
             ->leftJoin('study_plan_edition_disciplines as disc_spde','disc_spde.study_plan_edition_id','spd.id')
             ->leftJoin('study_plans as stdp','stdp.id','spd.study_plans_id')
             ->leftJoin('disciplines as disci','disc_spde.discipline_id','disci.id')

             ->leftJoin('disciplines_translations as dt', function ($join) {
                $join->on('dt.discipline_id', '=', 'disci.id');
                $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dt.active', '=', DB::raw(true));
            })
             ->whereIn('stdp.courses_id',$id_cursos_c)
             ->whereIn('spd.course_year',$Anos_academico)
             ->where  ('spd.lective_years_id',$anoLectivo)
             ->select(['dt.discipline_id as id_disciplina','spd.course_year as Anocurricular','dt.display_name as nome_disciplina','spd.lective_years_id as anoLectivo','stdp.code as curso','disci.code as code_disciplina'])
             ->distinct('dt.display_name')
             ->orderBy('spd.course_year','ASC')
             ->orderBy('stdp.code','ASC')
             ->get();


             $Turmas=DB::table('classes')
             ->whereIn('courses_id',$id_cursos_c)
             ->whereIn('year',$Anos_academico)
             ->where('lective_year_id',$anoLectivo)
             ->get();

             return response()->json(["disciplina"=>$disciplina,"Turmas"=>$Turmas]);

        } catch (Exception | Throwable $e) {
            logError($e);
            return $e;
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    //Gerar Estatistica__Coordenar__tipo
    public function generateEstatistic(Request $request){
        try{
            //
            $Cursos=$request->id_curso;
            $Curricular=$request->AnoCurricular_id;
            $Disciplinas=$request->id_disciplina;
            $Turmas=$request->id_turma;
            $Escala=$request->id_escala_avaliacao;
            $AnoLectivo=$request->id_anoLectivo;

            $Pauta_Busca=isset($request->pauta_type)?$request->pauta_type:0; // 30 representa a Pauta Classificação final
            //estatistica_Busca-Pauta final
            if($Pauta_Busca==0){
                Toastr::warning(__('Não foi possivel gerar a estatística porque o critério do tipo de pauta, não foi encontrado.'), __('toastr.warning'));
                return back();
            }
            else{

                $Ui=explode(",",$request->pauta_type);
                $Pauta_Busca=$Ui[0];
                $Pauta_Name=$Ui[1];
            }

            if($request->id_curso){

              $estatistica=DB::table('tb_estatistic_avaliation as et')
                ->leftJoin('courses_translations as dp', 'dp.courses_id', '=', 'et.id_course')
                ->leftJoin('classes as turma', 'turma.id', '=', 'et.id_class')
                ->leftJoin('disciplines_translations as disc', 'disc.discipline_id', '=', 'et.id_discipline')
                ->where('disc.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()))
                ->where('disc.active', '=', DB::raw(true))
                ->where('dp.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()))
                ->where('dp.active', '=', DB::raw(true))
                ->whereIn('et.id_course',$Cursos)
                ->where('et.id_lective_year',$AnoLectivo)
                ->where('et.pautaType', $Pauta_Busca)


                ->when($Disciplinas, function ($query, $Disciplinas) {
                    $query->whereIn('et.id_discipline', $Disciplinas);
                })
                ->when($Curricular, function ($query, $Curricular) {
                    $query->whereIn('turma.year', $Curricular);
                })
                ->when($Turmas, function ($query, $Turmas) {
                    $query->whereIn('et.id_class', $Turmas);
                })
                ->when($Escala, function ($query, $Escala) {
                    $query->whereIn('et.scale', $Escala);
                })
                ->select(['et.*','dp.display_name as curso','turma.display_name as turma','turma.year as anoCurricular','disc.display_name as disciplina_name'])
                ->orderBy('anoCurricular')
                ->get();

    $dados=collect($estatistica)->all();

    $dados_ano_turma_disc=collect($estatistica)->unique(function ($item) {return $item->turma.$item->disciplina_name;});


    $curso=[];
    $cursos = $dados_ano_turma_disc->unique('curso')->map(function($item){  return  $item->curso; });

    $curso=[$cursos]; $curso_array=array(); foreach ($curso[0] as $a) { $curso_array[] = $a."";  }



    $cursos = implode(',',$curso_array);


    $mateuArray=[];
    foreach ($dados as $item){
     $mateuArray[]=$item->scale;
    }
     $scalaReprovado =collect($mateuArray)->unique() ->filter(function($item){ return $item=="first" ||  $item=="second";
    })->count();

     $scalaReprovadoName =collect($mateuArray)
    ->unique()
    ->filter(function($item){
        return $item=="first" ||  $item=="second";
    });

    $scalaAprovadoName =collect($mateuArray)
    ->unique()
    ->filter(function($item){
        return $item!="first" &&  $item!="second";
    });

    $scalaAprovado =collect($mateuArray)
    ->unique()
    ->filter(function($item){
        return $item!="first" &&  $item!="second";
    })->count();

         $total_m=collect($estatistica)->sum('masculine');
         $total_f=collect($estatistica)->sum('feminine');
         $count=$total_m+$total_f;

         $tabela_Total=[
             "M"=>$total_m,
             "Percent_M"=>$count!=0?(int) round(($total_m/$count)*100,0):0,
             "F"=> $total_f,
             "Percent_F"=>$count!=0?(int) round(($total_f/$count)*100,0):0,
          ];


        }else{

            Toastr::warning(__('Não foi possivel gerar a estatística porque o nenhum curso foi selecionado.'), __('toastr.warning'));
            return back();
        }

            //dados da instituição
            $institution = Institution::latest()->first();

            //$titulo_documento = "Pauta de";
            // $Logotipo_instituicao="https://".$_SERVER['HTTP_HOST']."/storage/".$institution->logotipo;
            $Logotipo_instituicao="https://".$_SERVER['HTTP_HOST']."/storage/".$institution->logotipo;
            //Dados do chefe do gabinente
            $gabinete_chefe = User::whereHas('roles', function ($q) {
                $q->whereIn('id', [47]);
            }) ->leftJoin('user_parameters as u_p9', function ($q) {
                $q->on('users.id', '=', 'u_p9.users_id')
                ->where('u_p9.parameters_id', 1);
            })->first();


            $documentoCode_documento=501;

            $data = [
                'documentoCode_documento'=>$documentoCode_documento,
                'institution' => $institution,
                'scalaReprovado' => $scalaReprovado,
                'scalaAprovado' => $scalaAprovado,
                'scalaReprovadoName' => $scalaReprovadoName,
                'scalaAprovadoName' => $scalaAprovadoName,
                'dados' => $dados,
                'dados_ano_turma_disc' => $dados_ano_turma_disc,
                'Pauta_Name' =>$Pauta_Name,
                'cursos' =>$cursos,
                'total' => $tabela_Total,
                'documentoCode_documento' => $documentoCode_documento,
                'chefe_gabinet' => $gabinete_chefe,
                'logotipo' => $Logotipo_instituicao,
                'disciplineHasMandatoryExam' => "uudj"
            ];
           //return view("Avaliations::avaliacao-estatistica.pdf.estatisticaTurma", $data);

           $pdf = PDF::loadView("Avaliations::avaliacao-estatistica.pdf.estatisticaTurma", $data);
           $pdf->setOption('margin-top', '2mm');
           $pdf->setOption('margin-left', '2mm');
           $pdf->setOption('margin-bottom', '13mm');
           $pdf->setOption('margin-right', '2mm');
           $pdf->setPaper('a4','landscape');

           $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
           $pdf->setOption('footer-html', $footer_html);

           return $pdf->stream('folha_de_estatistica'. '.pdf');

           } catch (Exception | Throwable $e) {
            logError($e);
            return $e;
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
           }
    }







    //Gerar Estatistica_PErcurso__tipo
    public function generateEstatistic_geral(Request $request){
        try{
            //
            $estado=$request->documento_set;
            $Cursos=$request->id_curso;
            $Curricular=$request->AnoCurricular_id;
            $Disciplinas=$request->id_disciplina;
            $Turmas=$request->id_turma;
            $Escala=$request->id_escala_avaliacao;
            $AnoLectivo=$request->id_anoLectivo;



            if($request->id_disciplina){

            $course=DB::table('courses')
                    ->leftJoin('courses_translations as ct', function ($join) {
                     $join->on('ct.courses_id', '=', 'courses.id');
                     $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                     $join->on('ct.active', '=', DB::raw(true));
            })
            ->select(['courses.code as codigo','ct.display_name as course_name'])
            ->where('courses.id',$Cursos)
            ->first();

            $ESTUDANTES=DB::table('new_old_grades as Percurso')
                ->leftJoin('users as user', 'user.id', '=', 'Percurso.user_id')
                ->leftJoin('user_parameters as full_name', function ($join) {
                    $join->on('user.id', '=', 'full_name.users_id')
                    ->where('full_name.parameters_id', 1);
                 })
                 ->leftJoin('user_parameters as up_meca', function ($join) {
                    $join->on('user.id','=','up_meca.users_id')
                    ->where('up_meca.parameters_id', 19);
                 })

                ->leftJoin('user_parameters as sexo', function ($join) {
                       $join->on('user.id', '=', 'sexo.users_id')
                       ->where('sexo.parameters_id', 2);
                 })


                ->leftJoin('parameter_options as sexo_value', 'sexo_value.id', '=', 'sexo.value')
                ->when($Disciplinas, function ($query, $Disciplinas) {
                    $query->where('Percurso.discipline_id', $Disciplinas);
                })
                ->leftJoin('disciplines as dc', 'dc.id', '=', 'Percurso.discipline_id')
                ->leftJoin('disciplines_translations as ct', function ($join) {
                    $join->on('ct.discipline_id', '=', 'Percurso.discipline_id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
               })
                ->select(['sexo_value.code as sexo','user.id as is_user','full_name.value as nome_completo','Percurso.grade as nota','Percurso.lective_year as AnoLectivo','ct.display_name as disciplina','dc.code as codigo_disciplina', 'up_meca.value as matricula',])
                ->where('Percurso.lective_year',$AnoLectivo)
                ->orderBy('nome_completo')
                ->distinct('matricula')
                ->get();

                 $escala_result=['first'=>0,'second'=>0,'thirst'=>0,'fourth'=>0,'fiveth'=>0,'sixth'=>0];
                 $dadosM=collect(['first'=>0,'second'=>0,'thirst'=>0,'fourth'=>0,'fiveth'=>0,'sixth'=>0]);
                 $dadosF=collect(['first'=>0,'second'=>0,'thirst'=>0,'fourth'=>0,'fiveth'=>0,'sixth'=>0]);
                 $total=["Total_m"=>0,"Total_f"=>0];

                 $Dados_estatistico=collect($ESTUDANTES)->filter(function($item) Use ($dadosM,$dadosF) {
                     if($item->sexo=="Masculino"){
                            if($item->nota>=0 && $item->nota<7){
                            $dadosM['first']=$dadosM['first']+1;
                            }
                            if($item->nota>6 && $item->nota<10){
                            $dadosM['second']=$dadosM['second']+1;
                            }
                            if($item->nota>9 && $item->nota<14){
                            $dadosM['thirst']=$dadosM['thirst']+1;
                            }
                            if($item->nota>13 && $item->nota<17){
                            $dadosM['fourth']=$dadosM['fourth']+1;
                            }
                            if($item->nota>16 && $item->nota<20){
                            $dadosM['fiveth']=$dadosM['fiveth']+1;
                            }
                            if($item->nota>19){
                            $dadosM['sixth']=$dadosM['sixth']+1;
                            }

                        }
                        else if($item->sexo=="Feminino"){
                            if($item->nota>=0 && $item->nota<7){
                                $dadosF['first']=$dadosF['first']+1;
                                }
                                if($item->nota>6 && $item->nota<10){
                                $dadosF['second']=$dadosF['second']+1;
                                }
                                if($item->nota>9 && $item->nota<14){
                                $dadosF['thirst']=$dadosF['thirst']+1;
                                }
                                if($item->nota>13 && $item->nota<17){
                                $dadosF['fourth']=$dadosF['fourth']+1;
                                }
                                if($item->nota>16 && $item->nota<20){
                                $dadosF['fiveth']=$dadosF['fiveth']+1;
                                }
                                if($item->nota>19){
                                $dadosF['sixth']=$dadosF['sixth']+1;
                                }
                        }
                   });


                foreach($escala_result as $key=> $item){
                    $estatistica[$key]=[
                        "M"=>$dadosM[$key],
                        "Percent_M"=>($dadosF[$key]+$dadosM[$key])!=0?(int) round(($dadosM[$key]/($dadosF[$key]+$dadosM[$key]))*100,0):0,
                        "F"=> $dadosF[$key],
                        "Percent_F"=>($dadosF[$key]+$dadosM[$key])!=0?(int) round(($dadosF[$key]/($dadosF[$key]+$dadosM[$key]))*100,0):0,
                    ];
                    //guardar total
                    $total["Total_m"]=$total["Total_m"]+$dadosM[$key];
                    $total["Total_f"]=$total["Total_f"]+$dadosF[$key];
                }




        }else{

            Toastr::warning(__('Não foi possivel gerar a estatística porque o nenhuma disciplina foi selecionado.'), __('toastr.warning'));
            return back();
             }
            //dados da instituição
            $institution = Institution::latest()->first();

            // $Logotipo_instituicao="https://".$_SERVER['HTTP_HOST']."/storage/".$institution->logotipo;
            $Logotipo_instituicao="https://".$_SERVER['HTTP_HOST']."/storage/".$institution->logotipo;
            //Dados do chefe do gabinente
            $gabinete_chefe = User::whereHas('roles', function ($q) {
                $q->whereIn('id', [47]);
            }) ->leftJoin('user_parameters as u_p9', function ($q) {
                $q->on('users.id', '=', 'u_p9.users_id')
                ->where('u_p9.parameters_id', 1);
            })->first();


            $documentoCode_documento=502;

            $data = [
                'documentoCode_documento'=>$documentoCode_documento,
                'institution' => $institution,
                'estatistica' => $estatistica,
                'estatistica_total'=>$total,
                'estudantes' => $ESTUDANTES,
                'estado' => $estado,
                'curso' => $course,
                'documentoCode_documento' => $documentoCode_documento,
                'chefe_gabinet' => $gabinete_chefe,
                'logotipo' => $Logotipo_instituicao
            ];
            //return view("Avaliations::avaliacao-estatistica.pdf.estatisticaDisciplina", $data);
            if($estado==1){
                //Quando for a tabela de estatistica
                $pdf = PDF::loadView("Avaliations::avaliacao-estatistica.pdf.estatisticaDisciplina", $data);
            } else{
                //Quando for a tabela de estudantes com nomes e notas
                $pdf = PDF::loadView("Avaliations::avaliacao-estatistica.pdf.estudantesListaEstatistica", $data);
             }

           $pdf->setOption('margin-top', '2mm');
           $pdf->setOption('margin-left', '2mm');
           $pdf->setOption('margin-bottom', '13mm');
           $pdf->setOption('margin-right', '2mm');
           $pdf->setPaper('a4','landscape');

           $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
           $pdf->setOption('footer-html', $footer_html);

           return $pdf->stream($estado==1?'folha_de_estatistica_por_disciplina':'folha_de_estatistica_lista_estudante'. '.pdf');

           } catch (Exception | Throwable $e) {
            logError($e);
            return $e;
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
           }
    }



    //Gerar Estatistica_Percurso__tipo
    public function generateEstatistic_graduado(Request $request){
        try{
            // return $request;
            $estado=$request->documento_set;
            $Cursos=$request->id_curso;
            $Curricular=$request->AnoCurricular_id;
            $Disciplinas=$request->id_disciplina;
            $Turmas=$request->id_turma;
            $Escala=$request->id_escala_avaliacao;
            $AnoLectivo=$request->id_anoLectivo;

           $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->where('id',$AnoLectivo)
                ->first();
           $anoLectivoDsiplay_name=$lectiveYears['currentTranslation']->display_name;


         if($request->id_disciplina){

            $course=DB::table('courses')
                        ->leftJoin('courses_translations as ct', function ($join) {
                        $join->on('ct.courses_id', '=', 'courses.id');
                        $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('ct.active', '=', DB::raw(true));
                })
                ->select(['courses.code as codigo','ct.display_name as course_name'])
                ->where('courses.id',$Cursos)
                ->first();


                $ESTUDANTES=DB::table('new_old_grades as Percurso')
                    ->leftJoin('users as user', 'user.id', '=', 'Percurso.user_id')
                    ->leftJoin('user_parameters as full_name', function ($join) {
                        $join->on('user.id', '=', 'full_name.users_id')
                        ->where('full_name.parameters_id', 1);
                    })
                    ->leftJoin('user_parameters as up_meca', function ($join) {
                        $join->on('user.id','=','up_meca.users_id')
                        ->where('up_meca.parameters_id', 19);
                    })

                    ->leftJoin('user_parameters as sexo', function ($join) {
                        $join->on('user.id', '=', 'sexo.users_id')
                        ->where('sexo.parameters_id', 2);
                    })


                    ->leftJoin('parameter_options as sexo_value', 'sexo_value.id', '=', 'sexo.value')
                    ->when($Disciplinas, function ($query, $Disciplinas) {
                        $query->where('Percurso.discipline_id', $Disciplinas);
                    })
                    ->leftJoin('disciplines as dc', 'dc.id', '=', 'Percurso.discipline_id')
                    ->leftJoin('disciplines_translations as ct', function ($join) {
                        $join->on('ct.discipline_id', '=', 'Percurso.discipline_id');
                        $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('ct.active', '=', DB::raw(true));
                })
                    ->select(['sexo_value.code as sexo','user.id as is_user','full_name.value as nome_completo','Percurso.grade as nota','Percurso.lective_year as AnoLectivo','ct.display_name as disciplina','dc.code as codigo_disciplina', 'up_meca.value as matricula',])
                    ->where('Percurso.lective_year',$anoLectivoDsiplay_name)
                    // ->where('Percurso.lective_year',2020)
                    ->where('Percurso.grade','>',9)
                    ->orderBy('nota','ASC')
                    ->distinct('matricula')
                    ->get();

                 if($ESTUDANTES->isEmpty()){
                    Toastr::warning(__('A forLEARN detectou que  não existe estudantes graduados com os critérios de busca selecionado.'), __('toastr.warning'));
                    return back();

                 }

                 $escala_result=['first'=>0,'second'=>0,'thirst'=>0,'fourth'=>0,'fiveth'=>0,'sixth'=>0];
                 $dadosM=collect(['first'=>0,'second'=>0,'thirst'=>0,'fourth'=>0,'fiveth'=>0,'sixth'=>0]);
                 $dadosF=collect(['first'=>0,'second'=>0,'thirst'=>0,'fourth'=>0,'fiveth'=>0,'sixth'=>0]);
                 $total=["Total_m"=>0,"Total_f"=>0];

                 $Dados_estatistico=collect($ESTUDANTES)->filter(function($item) Use ($dadosM,$dadosF) {

                    if($item->sexo=="Masculino"){
                            if($item->nota>=0 && $item->nota<7){
                            $dadosM['first']=$dadosM['first']+1;
                            }
                            if($item->nota>6 && $item->nota<10){
                            $dadosM['second']=$dadosM['second']+1;
                            }
                            if($item->nota>9 && $item->nota<14){
                            $dadosM['thirst']=$dadosM['thirst']+1;
                            }
                            if($item->nota>13 && $item->nota<17){
                            $dadosM['fourth']=$dadosM['fourth']+1;
                            }
                            if($item->nota>16 && $item->nota<20){
                            $dadosM['fiveth']=$dadosM['fiveth']+1;
                            }
                            if($item->nota>19){
                            $dadosM['sixth']=$dadosM['sixth']+1;
                            }

                        }
                        else if($item->sexo=="Feminino"){

                            if($item->nota>=0 && $item->nota<7){
                                $dadosF['first']=$dadosF['first']+1;
                                }
                                if($item->nota>6 && $item->nota<10){
                                $dadosF['second']=$dadosF['second']+1;
                                }
                                if($item->nota>9 && $item->nota<14){
                                $dadosF['thirst']=$dadosF['thirst']+1;
                                }
                                if($item->nota>13 && $item->nota<17){
                                $dadosF['fourth']=$dadosF['fourth']+1;
                                }
                                if($item->nota>16 && $item->nota<20){
                                $dadosF['fiveth']=$dadosF['fiveth']+1;
                                }
                                if($item->nota>19){
                                $dadosF['sixth']=$dadosF['sixth']+1;
                                }
                        }
                   });


                foreach($escala_result as $key=> $item){
                    $estatistica[$key]=[
                        "M"=>$dadosM[$key],
                        "Percent_M"=>($dadosF[$key]+$dadosM[$key])!=0?(int) round(($dadosM[$key]/($dadosF[$key]+$dadosM[$key]))*100,0):0,
                        "F"=> $dadosF[$key],
                        "Percent_F"=>($dadosF[$key]+$dadosM[$key])!=0?(int) round(($dadosF[$key]/($dadosF[$key]+$dadosM[$key]))*100,0):0,
                    ];
                    //guardar total
                    $total["Total_m"]=$total["Total_m"]+$dadosM[$key];
                    $total["Total_f"]=$total["Total_f"]+$dadosF[$key];
                }

        }

        else

        {

             Toastr::warning(__('Não foi possivel gerar a estatística dos graduados, possivelmente houve uma falha ao localizar a disciplina trabalho de fim de curso, verifica se o curso selecionado tem a disciplina "Trabalho de fim de curso" na edição de plano de estudo do ano lectivo selecionado.'), __('toastr.warning'));
            return back();

        }
            //dados da instituição
            $institution = Institution::latest()->first();

            // $Logotipo_instituicao="https://".$_SERVER['HTTP_HOST']."/storage/".$institution->logotipo;
            $Logotipo_instituicao="https://".$_SERVER['HTTP_HOST']."/storage/".$institution->logotipo;
            //Dados do chefe do gabinente
            $gabinete_chefe = User::whereHas('roles', function ($q) {
                $q->whereIn('id', [47]);
            }) ->leftJoin('user_parameters as u_p9', function ($q) {
                $q->on('users.id', '=', 'u_p9.users_id')
                ->where('u_p9.parameters_id', 1);
            })->first();


            $documentoCode_documento=502;

            $data = [
                'documentoCode_documento'=>$documentoCode_documento,
                'institution' =>$institution,
                'estatistica' =>$estatistica,
                'estatistica_total'=>$total,
                'estudantes' =>$ESTUDANTES,
                'estado' =>$estado,
                'curso' =>$course,
                'documentoCode_documento'=>$documentoCode_documento,
                'chefe_gabinet'=>$gabinete_chefe,
                'logotipo'=>$Logotipo_instituicao
            ];
            //return view("Avaliations::avaliacao-estatistica.pdf.estatisticaDisciplina", $data);
            if($estado==2){
                //Quando for a tabela de estatistica
                $pdf = PDF::loadView("Avaliations::avaliacao-estatistica.pdf.estatisticaDisciplina", $data);
            } else{
                //Quando for a tabela de estudantes com nomes e notas
                $pdf = PDF::loadView("Avaliations::avaliacao-estatistica.pdf.estudantesListaGraduados", $data);
            }

            $pdf->setOption('margin-top', '2mm');
            $pdf->setOption('margin-left', '2mm');
            $pdf->setOption('margin-bottom', '13mm');
            $pdf->setOption('margin-right', '2mm');
            $pdf->setPaper('a4','landscape');

           $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
           $pdf->setOption('footer-html', $footer_html);

           return $pdf->stream($estado==1?'folha_de_estatistica_por_disciplina':'folha_de_estatistica_lista_estudante'. '.pdf');

           } catch (Exception | Throwable $e) {
            logError($e);
            return $e;
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
           }
    }

        // ===========================================  Estatísticas anual ========================================


       public function generateEstatisticAnual(Request $request)
    {
        try {



            $Cursos = $request->id_curso;
            $Curricular = $request->AnoCurricular_id;
            $AnoLectivo = $request->id_anoLectivo;

            $p_lectivo = DB::table('lective_year_translations as anolectivo')
                ->where('anolectivo.lective_years_id', $AnoLectivo)
                ->where('anolectivo.active', 1)
                ->whereNull('anolectivo.deleted_at')
                ->select(['anolectivo.display_name as ano'])
                ->get();


            $matriculados = DB::table('users as estudante')
                ->join('matriculations as matricula', 'matricula.user_id', '=', 'estudante.id')
                ->join('user_courses as curso', 'curso.users_id', '=', 'matricula.user_id')
                ->Join('user_parameters as sexo', function ($join) {
                    $join->on('matricula.user_id', '=', 'sexo.users_id')
                        ->where('sexo.parameters_id', 2);
                })
                ->Join('parameter_options as sexo_value', 'sexo_value.id', '=', 'sexo.value')
                ->whereIn('matricula.course_year', $Curricular)
                ->where('matricula.lective_year', $AnoLectivo)
                ->where('curso.courses_id', $Cursos)
                ->whereNull('estudante.deleted_by')
                ->whereNull('matricula.deleted_at')
                ->select(['estudante.id as codigo', 'estudante.name as nome_completo', 'matricula.code as matricula', 'sexo_value.code as sexo', 'matricula.course_year as curricular', 'matricula.lective_year as lectivo'])
                ->groupBy('estudante.id')
                ->get();

             $disciplina = DB::table('study_plan_editions as spd')
                ->Join('study_plan_edition_disciplines as disc_spde', 'disc_spde.study_plan_edition_id', 'spd.id')
                ->Join('study_plans as stdp', 'stdp.id', 'spd.study_plans_id')
                ->Join('disciplines as disci', 'disc_spde.discipline_id', 'disci.id')

                ->Join('disciplines_translations as dt', function ($join) {
                    $join->on('dt.discipline_id', '=', 'disci.id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                })
                ->where('stdp.courses_id', $Cursos)
                ->whereIn('spd.course_year', $Curricular)
                ->where('spd.lective_years_id', $AnoLectivo)
                ->select(['dt.discipline_id as id_disciplina', 'stdp.courses_id as curso', 'spd.lective_years_id as anolectivo', 'spd.course_year as anocurricular', 'disci.code as codigo_disci'])
                ->distinct('dt.display_name')
                ->orderBy('spd.course_year', 'ASC')
                ->orderBy('stdp.code', 'ASC')
                ->get();

                $disci=array();

                foreach ($disciplina as $key => $value) {
                    array_push($disci,$value->id_disciplina);
                }



            $avaliados = DB::table('users as estudante')
                ->join('matriculations as matricula', 'matricula.user_id', '=', 'estudante.id')
                ->join('user_courses as curso', 'curso.users_id', '=', 'matricula.user_id')
                ->Join('user_parameters as sexo', function ($join) {
                    $join->on('matricula.user_id', '=', 'sexo.users_id')
                        ->where('sexo.parameters_id', 2);
                })
                ->Join('parameter_options as sexo_value', 'sexo_value.id', '=', 'sexo.value')
                ->Join('new_old_grades as Percurso', 'matricula.user_id', '=', 'Percurso.user_id')
                ->whereIn('matricula.course_year', $Curricular)
                ->where('matricula.lective_year', $AnoLectivo)
                ->where('curso.courses_id', $Cursos)
                ->where('Percurso.lective_year', $p_lectivo[0]->ano)
                ->whereIn('Percurso.discipline_id',  $disci)
                ->whereNull('estudante.deleted_by')
                ->whereNull('matricula.deleted_at')
                ->select(['estudante.id as codigo', 'estudante.name as nome_completo', 'matricula.code as matricula', 'sexo_value.code as sexo', 'matricula.course_year as curricular', 'matricula.lective_year as lectivo', 'Percurso.grade as nota', 'Percurso.discipline_id as disciplina'])
                ->orderBy('estudante.id')
                ->get();



            $avaliados_total = DB::table('users as estudante')
                ->join('matriculations as matricula', 'matricula.user_id', '=', 'estudante.id')
                ->join('user_courses as curso', 'curso.users_id', '=', 'matricula.user_id')
                ->Join('user_parameters as sexo', function ($join) {
                    $join->on('matricula.user_id', '=', 'sexo.users_id')
                        ->where('sexo.parameters_id', 2);
                })
                ->Join('parameter_options as sexo_value', 'sexo_value.id', '=', 'sexo.value')
                ->Join('new_old_grades as Percurso', 'matricula.user_id', '=', 'Percurso.user_id')
                ->whereIn('matricula.course_year', $Curricular)
                ->where('matricula.lective_year', $AnoLectivo)
                ->where('curso.courses_id', $Cursos)
                ->where('Percurso.lective_year', $p_lectivo[0]->ano)
                ->whereNull('estudante.deleted_by')
                ->whereNull('matricula.deleted_at')
                ->select(['estudante.id as codigo', 'estudante.name as nome_completo', 'matricula.code as matricula', 'sexo_value.code as sexo', 'matricula.course_year as curricular', 'matricula.lective_year as lectivo', 'Percurso.grade as nota'])
                ->groupBy('estudante.id')
                ->get();



            $total_matriculados = collect($matriculados)->groupBy('curricular')->map(function ($item, $key) {

                $matricula = ["total" => 0, "masculino" => 0, "femenino" => 0];

                foreach ($item as $estudante) {
                    $matricula["total"] = $matricula["total"] + 1;
                    if ($estudante->sexo == "Masculino") {
                        $matricula["masculino"] = $matricula["masculino"] + 1;
                    }
                    if ($estudante->sexo == "Feminino") {
                        $matricula["femenino"] = $matricula["femenino"] + 1;
                    }
                }
                return ["ano" => $item[0]->curricular, "masculino" => $matricula["masculino"], "femenino" => $matricula["femenino"], "total" => $matricula["total"]];
            });

            $total_avaliados = collect($avaliados_total)->groupBy('curricular')->map(function ($item, $key) {
                // $soma= $item->sum('valor_banco');
                // $count = count ($item);
                // $resulatado=$count.",".$soma;
                // return $resulatado;
                $avaliado = ["total" => 0, "masculino" => 0, "femenino" => 0];

                foreach ($item as $estudante) {
                    $avaliado["total"] = $avaliado["total"] + 1;
                    if ($estudante->sexo == "Masculino") {
                        $avaliado["masculino"] = $avaliado["masculino"] + 1;
                    }
                    if ($estudante->sexo == "Feminino") {
                        $avaliado["femenino"] = $avaliado["femenino"] + 1;
                    }
                }

                return ["ano" => $item[0]->curricular, "masculino" => $avaliado["masculino"], "femenino" => $avaliado["femenino"], "total" => $avaliado["total"]];
            });



            // Mapeamento do plano de estudo

            $plano = collect($disciplina)->groupBy('anocurricular')->map(function ($item, $key) {
                return $item;
            });


          $estudantes = collect($avaliados)->groupBy('codigo')->map(function ($item, $key) use ($plano, $Cursos) {


                $avaliacao = ["negativa" => 0, "positiva" => 0, "cadeira" => 0, "disciplina" => 0,"dn" => 0, "semestral" => 0, "anual" => 0,"pontos"=>0];

                $d = array();
                $d1 = array();

                foreach ($plano[$item[0]->curricular] as $item_disc) {
                    foreach ($item as $nota) {
                        $code = $item_disc->codigo_disci;

                        // switch ($Cursos) {
                        //     case 23:

                        //         // O estudantes só reprova se tiver 6 pontos em atraso




                        //         break;
                        //     case 25:
                        //         echo "CEE";
                        //         break;

                        //     default:



                        //         break;
                        // }

                         if ($nota->disciplina == $item_disc->id_disciplina) {

                            $avaliacao["disciplina"] = $avaliacao["disciplina"] + 1;

                              if (in_array($item_disc->id_disciplina, $d1, true)) {

                                }else{
                                    array_push($d1,$item_disc->id_disciplina);
                                }

                            if ($nota->nota < 10) {

                                switch ($Cursos) {
                                    case 23:
                                        switch ($code[3]) {
                                            case 'A':
                                                $avaliacao["anual"] = $avaliacao["anual"] + 1;
                                                break;

                                            default:
                                                $avaliacao["semestral"] = $avaliacao["semestral"] + 1;
                                                break;
                                        }
                                    break;

                                    default:
                                    switch ($code[4]) {
                                        case 'A':
                                            $avaliacao["anual"] = $avaliacao["anual"] + 1;
                                            break;

                                        default:
                                            $avaliacao["semestral"] = $avaliacao["semestral"] + 1;
                                            break;
                                    }
                                    break;
                                }

                            } else if ($nota->nota >= 10) {

                                $avaliacao["positiva"] = $avaliacao["positiva"] + 1;
                            }
                        } else {
                            // $avaliacao["dn"] = $avaliacao["dn"] + 1;

                                if (in_array($item_disc->id_disciplina, $d, true)) {
                                } else {
                                    array_push($d, $item_disc->id_disciplina);
                                }



                        }

                    }



                    $avaliacao["cadeira"] = $avaliacao["cadeira"] + 1;
                }


                    foreach ($d as $dados) {

                         if (in_array($dados, $d1, true)) {

                        }else{

                             foreach ($plano[$item[0]->curricular] as $item_disc) {
                                  if($item_disc->id_disciplina==$dados){

                                        $novo =   DB::table('new_old_grades as Percurso')
                                        ->where('Percurso.user_id', $item[0]->codigo)
                                        ->where('Percurso.discipline_id', $item_disc->id_disciplina)
                                        ->select(["Percurso.grade"])
                                        ->first();

                                        $verificar =  isset($novo->grade) ? $novo->grade : "Nada";

                                         if ( ($verificar !="Nada") && ($verificar >=10) ) {



                                         }else{


                                      $code = $item_disc->codigo_disci;
                                      $avaliacao["dn"] = $avaliacao["dn"] + 1;

                                       switch ($Cursos) {
                                        case 23:
                                            switch ($code[3]) {
                                            case 'A':
                                                $avaliacao["anual"] = $avaliacao["anual"] + 1;
                                                break;

                                            default:
                                                $avaliacao["semestral"] = $avaliacao["semestral"] + 1;
                                                break;
                                            }
                                        break;

                                        default:
                                                    switch ($code[4]) {
                                                    case 'A':
                                                        $avaliacao["anual"] = $avaliacao["anual"] + 1;
                                                        break;

                                                    default:
                                                        $avaliacao["semestral"] = $avaliacao["semestral"] + 1;
                                                        break;
                                                }
                                        break;
                                    } }

                                  }
                              }

                        }
                    }

                // Tiver mais de duas disciplinas

                if (count($item)>1) {

                    $avaliacao["negativa"] = ($avaliacao["semestral"]) + (($avaliacao["anual"])*2);

                    return [
                        "nome" => $item[0]->nome_completo,
                        "sexo" => $item[0]->sexo,
                        "ano" => $item[0]->curricular,
                        "semestral" => $avaliacao["semestral"],
                        "anual" => $avaliacao["anual"],
                        "positiva" => $avaliacao["positiva"],
                        "cadeira" => $avaliacao["cadeira"],
                        "disciplina" => $avaliacao["disciplina"],
                        "dn" => $avaliacao["dn"],
                        "negativa" => $avaliacao["negativa"]
                    ];

                }else{

                // Tiver uma disciplina apenas

                $avaliacao["negativa"] = $avaliacao["semestral"] + ($avaliacao["anual"]*2);
                return [
                    "nome" => $item[0]->nome_completo,
                    "sexo" => $item[0]->sexo,
                    "ano" => $item[0]->curricular,
                    "semestral" => $avaliacao["semestral"],
                    "anual" => $avaliacao["anual"],
                    "positiva" => $avaliacao["positiva"],
                    "cadeira" => $avaliacao["cadeira"],
                    "disciplina" => $avaliacao["disciplina"],
                    "dn" => $avaliacao["dn"],
                    "negativa" => $avaliacao["negativa"]
                ];
            }
            });




            $estudante_notas = collect($estudantes)->groupBy('ano')->map(function ($item, $key) {
                   return $item;
            });



            $curso = DB::table('courses as cursos')
                ->join('courses_translations as ct', 'ct.courses_id', '=', 'cursos.id')
                ->where('cursos.id', $Cursos)
                ->where('ct.active', 1)
                ->select(['cursos.id as codigo', 'cursos.code as cg', 'ct.display_name as nome'])
                ->get();

            $t_matriculados = count($matriculados);
            $t_avaliados = count($avaliados);
            $Pauta_Name = " anual";

            //dados da instituição
            $institution = Institution::latest()->first();

            //$titulo_documento = "Pauta de";
            // $Logotipo_instituicao="https://".$_SERVER['HTTP_HOST']."/storage/".$institution->logotipo;
            $Logotipo_instituicao = "https://" . $_SERVER['HTTP_HOST'] . "/storage/" . $institution->logotipo;
            //Dados do chefe do gabinente
            $gabinete_chefe = User::whereHas('roles', function ($q) {
                $q->whereIn('id', [47]);
            })->leftJoin('user_parameters as u_p9', function ($q) {
                $q->on('users.id', '=', 'u_p9.users_id')
                    ->where('u_p9.parameters_id', 1);
            })->first();


            $documentoCode_documento = 501;
            $total_AV = 0;

            foreach ($total_avaliados as $item) {
                $total_AV = $item["total"] +  $total_AV;
            }

            $data = [
                'documentoCode_documento' => $documentoCode_documento,
                'institution' => $institution,
                'documentoCode_documento' => $documentoCode_documento,
                'chefe_gabinet' => $gabinete_chefe,
                'logotipo' => $Logotipo_instituicao,
                'Pauta_Name' => $Pauta_Name,
                'curso' => $curso,
                'Cursos' => $Cursos,
                'ano' =>  $p_lectivo[0]->ano,
                'curricular' =>  $Curricular,
                'total_matriculados' =>  $total_matriculados,
                'total_avaliados' =>  $total_avaliados,
                't_matriculados' =>  $t_matriculados,
                't_avaliados' =>  $t_avaliados,
                'estudantes' =>  $estudantes,
                'estudante_notas' =>  $estudante_notas,
                'total_AV' =>  $total_AV,
            ];
            //return view("Avaliations::avaliacao-estatistica.pdf.estatisticaTurma", $data);

            $pdf = PDF::loadView("Avaliations::avaliacao-estatistica.pdf.estatisticaAnual", $data);
            $pdf->setOption('margin-top', '2mm');
            $pdf->setOption('margin-left', '2mm');
            $pdf->setOption('margin-bottom', '13mm');
            $pdf->setOption('margin-right', '2mm');
            $pdf->setPaper('a4', 'landscape');

            $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
            $pdf->setOption('footer-html', $footer_html);

            return $pdf->stream('folha_de_estatistica_anual' . '.pdf');
        } catch (Exception | Throwable $e) {
            logError($e);
            return $e;
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

         // ===========================================  Estatísticas Candidatos ========================================

     public function generateEstatistiCandidato(Request $request)
     {


         try {




             $cursos = $request->course;
             $disciplina = $request->discipline;
             $turmas = $request->classe;

             $fases =  explode(",", $request->fase);

             $fase =  $fases[0];
             $fase_nome =  $fases[1];


             $ano = $request->lective_year;

             $lectiveYear = LectiveYear::where('id', $ano)->first();

            // Pegar as turmas

             $disciplines = Discipline::leftJoin('disciplines_translations as dt', function ($join) {
                $join->on('dt.discipline_id', '=', 'disciplines.id');
                $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dt.active', '=', DB::raw(true));
            })
            ->leftJoin('discipline_has_areas', 'discipline_has_areas.discipline_id', '=', 'disciplines.id')
            ->leftJoin('discipline_areas', 'discipline_areas.id', '=', 'discipline_has_areas.discipline_area_id')
            ->where('disciplines.courses_id', $cursos)
            ->where('discipline_area_id', 18)
            ->select('disciplines.id as id_disciplina', 'disciplines.code as code_disciplina', 'dt.display_name as nome_disciplina')
            ->get();

            // Pegar as disciplinas

             $turma = DB::table('classes as turma')
           ->where( [

            ['turma.courses_id', '=', $cursos],
            ['turma.lective_year_id', '=', $ano],
            ['turma.year', '=', 1],
            ['turma.deleted_at', '=', null]

            ])->select([
                'turma.id as id_turma',
                'turma.code as code_turma',
                'turma.display_name as nome_turma',
                ])
            ->get();


            $candidatos = User::query()
            ->whereHas('roles', function ($q) {
                $q->whereIn('id', [15,6]);
            })
             ->join('user_candidate as uca','uca.user_id','=','users.id')
             ->Join('lective_candidate as lc',function($join) use ($fase){
                 $join->on('lc.id','=','uca.year_fase_id')
                 ->where('uca.year_fase_id', $fase)
                 ->limit(1);
             })
            ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'users.id')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'uc.courses_id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })
            ->Join('user_parameters as sexo', function ($join) {
                $join->on('uca.user_id', '=', 'sexo.users_id')
                    ->where('sexo.parameters_id', 2);
            })
            ->Join('parameter_options as sexo_value', 'sexo_value.id', '=', 'sexo.value')
            ->leftJoin('users as u2', 'u2.id', '=', 'users.updated_by')
            ->leftJoin('users as u3', 'u3.id', '=', 'users.deleted_by')
            ->leftJoin('users_states as us', 'users.id', '=', 'us.user_id')
            ->leftJoin('article_requests', 'article_requests.user_id', '=', 'users.id')
            ->leftJoin('user_parameters as candidate', function ($join) {
                $join->on('users.id', '=', 'candidate.users_id')
                    ->where('candidate.parameters_id', 311);
            })
            ->leftJoin('user_parameters as full_name', function ($join) {
                $join->on('users.id', '=', 'full_name.users_id')
                    ->where('full_name.parameters_id', 1);
            })
            ->leftJoin('lective_years', function ($join) {
                $join->whereRaw('users.created_at between `start_date` and `end_date`');
            })
            ->join('lective_year_translations as lyt', function ($join) {
                $join->on('lyt.lective_years_id', '=', 'lective_years.id');
                $join->on('lyt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('lyt.active', '=', DB::raw(true));
            })
            ->select([
                'uc.users_id as id_estudante',
                'full_name.value as name_name',
                'ct.display_name as nome_course',
                'ct.courses_id as id_curso',
                'lyt.display_name as lective_year_code',
                'sexo_value.code as sexo',
                 'lc.fase as fase'
            ])
            ->whereBetween('users.created_at', [$lectiveYear->start_date, $lectiveYear->end_date])
            ->whereNull('article_requests.deleted_at')
            ->where('ct.courses_id',$cursos)
            ->whereNotNull('candidate.value')
            ->groupBy('full_name.users_id')
            ->distinct('full_name.value')
            ->get();


           $total_candidatos = count($candidatos);

           $estudante = collect($candidatos)->groupBy('id_estudante')->map(function ($item, $key) use ($disciplines,$turma,$fase,$fase_nome) {

            $saida =  [
                "usuario_id" =>0,
                "turma_id" =>0,
                "curso_id" =>0,
                "disciplina_id"=>0,
                "nota" =>0,
                "fase"=>0,
                "sexo"=>0,
            ];




                $turma = DB::table('user_classes as turma')
                    ->leftJoin('grades as notas', 'notas.student_id', '=', 'turma.user_id')
                    ->where('turma.user_id', $item[0]->id_estudante)
                    ->where('notas.id_fase',$fase)
                    ->select([
                        'turma.class_id as id_turma',
                        'turma.user_id as usuario_id',
                        'notas.course_id as curso_id',
                        'notas.discipline_id as disciplina_id',
                        'notas.value as nota',
                        'notas.id_fase as fase',
                    ])
                    ->get();





                    if (count($turma)==0) {

                    }else{
                     $total_disciplina = count($disciplines);

                    foreach ($turma as $items) {

                        $saida["usuario_id"] = $items->usuario_id;
                        $saida["turma_id"] =  $items->id_turma;
                        $saida["curso_id"] =  $items->curso_id;
                        $saida["disciplina_id"] = $items->disciplina_id;
                        $saida["nota"] =  ($saida["nota"] + $items->nota);
                        $saida["fase"] =  $items->fase;
                        $saida["sexo"] = $item[0]->sexo;
                    }
                    $saida["nota"] = round(($saida["nota"])/$total_disciplina);
                    return $saida;
                }
            });




            $turmas = collect($estudante)->groupBy('turma_id')->map(function ($estudante_item, $key) {

            $escala_result = ['first' => 0, 'second' => 0, 'thirst' => 0, 'fourth' => 0, 'fiveth' => 0, 'sixth' => 0];
            $dadosM = collect(['first' => 0, 'second' => 0, 'thirst' => 0, 'fourth' => 0, 'fiveth' => 0, 'sixth' => 0, 'totalM' => 0]);
            $dadosF = collect(['first' => 0, 'second' => 0, 'thirst' => 0, 'fourth' => 0, 'fiveth' => 0, 'sixth' => 0, 'totalF' => 0]);
            $total = ["Total_m" => 0, "Total_f" => 0];


            foreach ($estudante_item as $item) {

                        if (!empty($item)) {


                        if ($item["sexo"] == "Masculino") {

                            if ($item["nota"] >= 0 && $item["nota"] < 7) {
                                $dadosM['first'] = $dadosM['first'] + 1;

                            }
                            if ($item["nota"] > 6 && $item["nota"] < 10) {
                                $dadosM['second'] = $dadosM['second'] + 1;

                            }
                            if ($item["nota"] > 9 && $item["nota"] < 14) {
                                $dadosM['thirst'] = $dadosM['thirst'] + 1;

                            }
                            if ($item["nota"] > 13 && $item["nota"] < 17) {
                                $dadosM['fourth'] = $dadosM['fourth'] + 1;

                            }
                            if ($item["nota"] > 16 && $item["nota"] < 20) {
                                $dadosM['fiveth'] = $dadosM['fiveth'] + 1;

                            }
                            if ($item["nota"] == 20) {
                                $dadosM['sixth'] = $dadosM['sixth'] + 1;

                            }

                            $dadosM['totalM'] = $dadosM['totalM'] + 1;

                        } else if ($item["sexo"] == "Feminino") {

                            if ($item["nota"] >= 0 && $item["nota"] < 7) {
                                $dadosF['first'] = $dadosF['first'] + 1;

                            }
                            if ($item["nota"] > 6 && $item["nota"] < 10) {
                                $dadosF['second'] = $dadosF['second'] + 1;

                            }
                            if ($item["nota"] > 9 && $item["nota"] < 14) {
                                $dadosF['thirst'] = $dadosF['thirst'] + 1;

                            }
                            if ($item["nota"] > 13 && $item["nota"] < 17) {
                                $dadosF['fourth'] = $dadosF['fourth'] + 1;

                            }
                            if ($item["nota"] > 16 && $item["nota"] < 20) {
                                $dadosF['fiveth'] = $dadosF['fiveth'] + 1;

                            }
                            if ($item["nota"] == 20) {
                                $dadosF['sixth'] = $dadosF['sixth'] + 1;

                            }
                            $dadosF['totalF'] = $dadosF['totalF'] + 1;
                        }

                    }


                }


            return [$dadosF, $dadosM];
            });
            // return $turmas;

            $total_AV=0;
            foreach ($turmas as $item_turma) {
                foreach ($item_turma as $item_a) {
                    // foreach ($item_a as $item) {
                    //     return $item;
                    // }

                    // return $item_a["totalF"];

                    if (isset($item_a["totalF"])) {
                        $total_AV += $item_a["totalF"];
                    }
                    if (isset($item_a["totalM"])) {
                        $total_AV += $item_a["totalM"];
                    }

                }
            }


            $curso = DB::table('courses as cursos')
                 ->join('courses_translations as ct', 'ct.courses_id', '=', 'cursos.id')
                 ->where('cursos.id', $cursos)
                 ->where('ct.active', 1)
                 ->select(['cursos.id as codigo', 'cursos.code as cg', 'ct.display_name as nome'])
                 ->get();

            $vagas = DB::table('anuncio_vagas as vagas')
                 ->where('vagas.id_fase', $fase)
                 ->where('vagas.course_id', $cursos)
                 ->where('vagas.lective_year', $ano)
                 ->whereNull('deleted_at')
                 ->select([
                    'vagas.manha',
                    'vagas.tarde',
                    'vagas.noite',
                 ])
                 ->get();

            $vaga = 0;

            foreach ($vagas as $item) {
                $vaga = $item->manha+$item->tarde+$item->noite;
            }


             $Pauta_Name = " Candidatos ( ".$fase_nome."º Fase )";

             //dados da instituição
             $institution = Institution::latest()->first();

             //$titulo_documento = "Pauta de";
             // $Logotipo_instituicao="https://".$_SERVER['HTTP_HOST']."/storage/".$institution->logotipo;
             $Logotipo_instituicao = "https://" . $_SERVER['HTTP_HOST'] . "/storage/" . $institution->logotipo;
             //Dados do chefe do gabinente
             $gabinete_chefe = User::whereHas('roles', function ($q) {
                 $q->whereIn('id', [47]);
             })->leftJoin('user_parameters as u_p9', function ($q) {
                 $q->on('users.id', '=', 'u_p9.users_id')
                     ->where('u_p9.parameters_id', 1);
             })->first();


             $documentoCode_documento = 501;

             $data = [
                 'documentoCode_documento' => $documentoCode_documento,
                 'institution' => $institution,
                 'documentoCode_documento' => $documentoCode_documento,
                 'chefe_gabinet' => $gabinete_chefe,
                 'logotipo' => $Logotipo_instituicao,
                 'Pauta_Name' => $Pauta_Name,
                 'cursos' => $curso[0]->nome,
                 'turma' => $turma,
                 'total_AV' =>$total_AV,
                 'turmas' => $turmas,
                 'total_candidatos' => $total_candidatos,
                 'vaga' =>$vaga,
                 'fase_nome' =>$fase_nome,
             ];
             //return view("Avaliations::avaliacao-estatistica.pdf.estatisticaTurma", $data);

             $pdf = PDF::loadView("Avaliations::avaliacao-estatistica.pdf.candidato", $data);
             $pdf->setOption('margin-top', '2mm');
             $pdf->setOption('margin-left', '2mm');
             $pdf->setOption('margin-bottom', '13mm');
             $pdf->setOption('margin-right', '2mm');
             $pdf->setPaper('a4', 'landscape');

             $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
             $pdf->setOption('footer-html', $footer_html);

             return $pdf->stream('folha_de_estatistica_anual' . '.pdf');
         } catch (Exception | Throwable $e) {
             logError($e);
             return $e;
             return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
         }
     }


    // ===========================================  RELATORIO PRIMARIO DE GRADUADOS ========================================
    public function generateRelatorioGraduados()
    {
        try {
            //Pegar o ano lectivo na select
            $lectiveYears = LectiveYear::with(['currentTranslation'])->get();

            $lectiveYearSelected = DB::table('lective_years')
            ->whereRaw('"'.Carbon::now().'" between `start_date` and `end_date`')->first();

            $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
            return view("Avaliations::avaliacao-estatistica.relatorioPrimarioGraduado", compact('lectiveYears', 'lectiveYearSelected'));
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function generateRelatorioGraduadosxls(Request $request)
    {
        try{
            $lectiveYears = LectiveYear::with(['currentTranslation'])
            ->where('id',$request->lective_year)->first();
            $yearname = $lectiveYears->currentTranslation->display_name;
            return Excel::download(new GraduadosExport($yearname), 'Registo-primario-graduados_'.Carbon::now()->format('Y-m-d H:i:s').'.xlsx');

        } catch (Throwable $th) {
            Log::error($th);
            return redirect()->back();
        }
    }

}
