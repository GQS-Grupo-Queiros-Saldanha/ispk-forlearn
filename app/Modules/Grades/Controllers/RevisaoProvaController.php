<?php

namespace App\Modules\Grades\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Toastr;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Modules\GA\Models\LectiveYear;
use App\Modules\Users\Controllers\MatriculationController;
use PDF;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LanguageHelper;
use App\Model\Institution;

class RevisaoProvaController extends Controller {




    public function index(){
        
        try{
           //Pegar o ano lectivo na select
           $lectiveYears = LectiveYear::with(['currentTranslation'])->get();
           $currentData = Carbon::now();
           $lectiveYearSelected = DB::table('lective_years')
               ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
               ->first(); 
           $lectiveYearSelected = $lectiveYearSelected->id ?? 6;

           $url = substr(request()->path(),9);
           $type = $url == "/revisao-de-prova" ? 1 : 0;
           //-----------------------------------------------------------------------//
           $data = [
                      //'courses' => $courses->get(),
                      'lectiveYearSelected'=>$lectiveYearSelected,
                      'lectiveYears'=>$lectiveYears,
                      'type' => $type
                   ];

            return view("Grades::melhoria-notas.revisao-prova")->with($data);

        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    
    }


    public function store(Request $request){
        try{
            if(auth()->user()->id == 23)
           dd($request);
           $discipline_id =explode(',', $request->get('disciplina'))[1];
           $data = [
            'students' => $request->get('estudantes'),
            'grades' => $request->get('notas'),
            'ausentes' => $request->get('inputCheckBox')
           ];

           $LectiveYear = LectiveYear::where('id', $request->get('selectedLective'))
           ->with(['currentTranslation'])
           ->first();

           DB::beginTransaction();

            for($i = 0; $i < count($data['students']); $i++){

                $new_grade = $data['grades'][$i];
                 //validar nota superior a 20 valores
                 $notaMaior= $data['grades'][$i] >20 ? 1 : 0;
                 if($notaMaior==1){
                 Toastr::warning(__('Atenção! não foi possivel atribuir as notas porque o detetamos que a nota inserida para a um determinado estudante foi superior a 20 valoreses, verifica os campos ao atribuir as notas.'), __('toastr.success'));
                 return back();
                 }

                   //fim validação
                 $user_id = $data['students'][$i];

                 $old_grade = DB::table('new_old_grades')
                                    ->where('user_id', $user_id)
                                    ->where('discipline_id', $discipline_id)
                                    ->first()
                                    ->grade;

                $ausente = is_array($data['ausentes']) && in_array($user_id,$data['ausentes']) ? 1 : null;
                $new_grade = $ausente == 1 ? null : $new_grade;

                $notaMaior = $new_grade > $old_grade;
                 
                DB::table('melhoria_notas')->updateOrInsert(
                    [
                        'user_id' => $user_id,
                        'discipline_id' => $discipline_id,
                        'lective_year' => $LectiveYear->id
                    ]
                    ,
                    [
                        'new_grade' => $new_grade,
                        'old_grade' => $old_grade,
                        'ausente' => $ausente,
                        'updated_by' => Auth::user()->id,
                        'created_by' => Auth::user()->id
                     ]

                    ); 

                if($notaMaior){
                  
                    DB::table('new_old_grades')
                    ->where('user_id', $user_id)
                    ->where('discipline_id', $discipline_id)
                    ->update(
                        [
                            'grade' => $new_grade,
                            'lective_year' => $LectiveYear->currentTranslation->display_name,
                            'updated_at' => Carbon::now()
                        ]
                        );
                }
                   
            }

            DB::commit();

            // Success message
            Toastr::success(__('Registo inserido com sucesso'), __('toastr.success'));
            return back();

        } catch (Exception | Throwable $e) {
        DB::rollback();
        logError($e);
        return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    
    }

    public function studentsGrades($id, $id_anoLectivo,$type){
        try{

            $lectiveYearSelected = DB::table('lective_years')
                ->where('id', $id_anoLectivo)
                ->first();
            
            $course_id  = explode(',', $id)[0];
            $discipline_id = explode(',', $id)[1];
            
            $dados =$this->students_melhoria($course_id,$discipline_id,$lectiveYearSelected,$type); 
           
            $grades = DB::table('melhoria_notas as mn')
                    ->join('matriculations as mt', 'mt.user_id', '=', 'mn.user_id')
                     ->select(
                        'mn.new_grade as aanota',
                        'mn.user_id as user_id',
                        'mn.ausente as ausente'
                       )
                    ->whereIn('mn.user_id', $dados->pluck('user_id')->toArray())
                    ->where('mn.discipline_id', $discipline_id)
                    ->where('mn.lective_year', $lectiveYearSelected->id)
                    ->where('mn.finalist', $type)
                    ->distinct() 
                    ->get();

                    return json_encode(['students'=> $dados,'grades'=>$grades]);

        
        } catch (Exception | Throwable $e) {
        logError($e);
        return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    
    }

            private function students_melhoria($course_id,$discipline_id,$lectiveYearSelected,$type){
                try{

                    $emolumento_confirma_prematricula= MatriculationController::pre_matricula_confirma_emolumento($lectiveYearSelected->id);

                    $emolumento_melhoria = EmolumentCodevLective("melhoria_nota", $lectiveYearSelected->id);

                    if (!$emolumento_melhoria->isEmpty()) {
                        $article_melhoria = $emolumento_melhoria[0]->id_emolumento;
                    }
                   
                    $students = DB::table('user_courses as uc')
                    ->where('uc.courses_id', $course_id)
                    ->leftJoin('matriculations as mt', function ($join) use($lectiveYearSelected) {
                        $join->on('mt.user_id', '=', 'uc.users_id')
                        ->whereNull('mt.deleted_at')
                        ->where('mt.lective_year', $lectiveYearSelected->id);
                    })
                    ->leftJoin('article_requests as matricula',function ($join) use($emolumento_confirma_prematricula)
                    {
                        $join->on('matricula.user_id','=','mt.user_id')
                        ->whereIn('matricula.article_id', $emolumento_confirma_prematricula)
                         ->where('matricula.deleted_by', null) 
                    ->where('matricula.deleted_at', null)
                    ->where('matricula.status', "total");
                    })
                    ->join('tb_exame_melhoria_nota as mel', function ($join) use ($discipline_id,$lectiveYearSelected,$type){
                        $join->on('mel.id_user','mt.user_id')
                        ->where('mel.id_discipline', $discipline_id)
                        ->where('mel.id_lectiveYear',$lectiveYearSelected->id)
                        ->where('mel.finalist',$type);
                    })
                    ->leftJoin('article_requests as melhoria',function ($join) use($article_melhoria)
                    {
                        $join->on('melhoria.user_id','=','mt.user_id')
                        ->where('melhoria.article_id', $article_melhoria)
                         ->where('melhoria.deleted_by', null) 
                    ->where('melhoria.deleted_at', null)
                    ->where('melhoria.status', "total");
                    })
                    ->leftJoin('users as users', 'users.id', '=', 'mt.user_id')
                    ->leftJoin('user_parameters as u_p', function ($join) {
                        $join->on('users.id', '=', 'u_p.users_id')
                        ->where('u_p.parameters_id', 1);
                    })
                    ->leftJoin('user_parameters as up_n', function ($join) {
                        $join->on('users.id', '=', 'up_n.users_id')
                             ->where('up_n.parameters_id', 19);
                    })
                    ->select([
                        'mt.id as id_mat',
                        'users.id as user_id',
                        'u_p.value as user_name',
                        'up_n.value as n_student'     
                    ])
                  ->orderBy('user_name', 'ASC')
                  ->distinct()
                  ->get();
              

               return $students;
                } catch (Exception | Throwable $e) {
                    logError($e);
                    return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
                    }
            }

        public function generatePDFGrades($id, $id_anoLectivo,$type){
            
            $lectiveYearSelected = DB::table('lective_years')
                ->where('id', $id_anoLectivo)
                ->first();
            
            $course_id  = explode(',', $id)[0];
            $discipline_id = explode(',', $id)[1];

            try{
                $students = DB::table('melhoria_notas as mn')
                    ->join('matriculations as mt', 'mt.user_id', '=', 'mn.user_id')
                    ->join('users as users', 'users.id', '=', 'mt.user_id')
                    ->join('user_parameters as u_p', function ($join) {
                        $join->on('users.id', '=', 'u_p.users_id')
                        ->where('u_p.parameters_id', 1);
                    })
                    ->join('user_parameters as up_n', function ($join) {
                        $join->on('users.id', '=', 'up_n.users_id')
                             ->where('up_n.parameters_id', 19);
                    })
                     ->select(
                        'mn.new_grade as aanota',
                        'mn.ausente as ausente',
                        'u_p.value as user_name',
                        'up_n.value as n_student'  
                       )
                    ->where('mn.discipline_id', $discipline_id)
                    ->where('mn.lective_year', $lectiveYearSelected->id)
                    ->where('mn.finalist', $type)
                    ->distinct() 
                    ->get();

                    if($students->isEmpty())
                    {
                        Toastr::warning(__('Nenhuma nota lançada'),__('toastr.warning'));
                        return redirect()->back();
                    }


                         //pegar os utilizadores que lançaram as notas 
                    $coordenador = DB::table('melhoria_notas as mn')
                    ->join('model_has_roles as mr','mr.model_id','mn.updated_by')
                        ->join('user_parameters as u_p9', function ($q) {
                            $q->on('mn.updated_by', '=', 'u_p9.users_id')
                                ->where('u_p9.parameters_id', 1);
                        })
                        ->select(['mn.updated_at as actualizado_a','u_p9.value as actualizador_fullname'])
                        ->where('mn.discipline_id', $discipline_id)
                        ->where('mn.lective_year', $lectiveYearSelected->id)
                        ->where('mn.finalist', $type)
                        ->first();

                        $LectiveYear = LectiveYear::where('id', $lectiveYearSelected->id)
                        ->with(['currentTranslation'])
                        ->first();

                        //Pegar a disciplina 
                    $disciplina = DB::table('disciplines as disc')
                    ->leftJoin('disciplines_translations as trans', function ($join) {
                        $join->on('trans.discipline_id', '=', 'disc.id');
                        $join->on('trans.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('trans.active', '=', DB::raw(true));
                    })

                    ->select(['disc.code as codigo', 'trans.display_name as disciplina'])
                    ->where(['disc.id' => $discipline_id])
                    ->get();


                    //Dados do curso
                $course = DB::table('courses')
                ->leftJoin('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'courses.id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })
                ->select(['ct.display_name'])
                ->where('courses.id', $course_id)
                ->first();

                    //dados da instituição
                    $institution = Institution::latest()->first();
                    //Logotipo
                    $Logotipo_instituicao = "https://" . $_SERVER['HTTP_HOST'] . "/instituicao-arquivo/" . $institution->logotipo;
                    // $titulo_documento = "Pauta de";
                    // $documentoGerado_documento = "Documento gerado a";
                    $documentoCode_documento = 10;

                        $data = [

                            'lectiveYear' => $LectiveYear,
                            'discipline_code' => $disciplina[0]->codigo . ' - ' . $disciplina[0]->disciplina,
                            'discipline_name' => $disciplina[0]->disciplina,
                            'curso' => $course->display_name,
                            'institution' => $institution,
                            'type' => $type,
                            'logotipo' => $Logotipo_instituicao,
                            'documentoCode_documento' => $documentoCode_documento,
                            'students' => $students,
                            'coordenador' => $coordenador
                        ];
                
                    
                
                        $pdf = PDF::loadView("Grades::melhoria-notas.pdf", $data);
                
                
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
                    
                    $pdf_name = 'pauta_melhoria' . $disciplina[0]->codigo . $LectiveYear->currentTranslation->display_name;
                    return $pdf->stream($pdf_name . '.pdf');

            } catch (Exception | Throwable $e) {
                logError($e);
                return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
                }
            }





























}