<?php

namespace App\Modules\Users\Controllers;


use App\Modules\Users\util\CandidatesUtil;
use App\Modules\Users\util\FaseCandidaturaUtil;
use App\Modules\Users\Models\User;
use App\Modules\Payments\Models\ArticleRequest;
use App\Modules\GA\Models\Course;
use App\Modules\GA\Models\LectiveYear;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Toastr;
use Auth;
use DB;

class TransferenciaController extends Controller
{
    private $candidateUtil;
    private $faseCandidateUtil;
    private $candidateController;

    function __construct()
    {
        $this->candidateUtil = new CandidatesUtil();
        $this->faseCandidateUtil = new FaseCandidaturaUtil();
        $this->candidateController = new CandidatesController();
    }


    public function historico($user_id)
    {
        $json = DB::table('lective_candidate_historico_fase as lchf')
            ->join('historic_classe_candidate as hcc', 'lchf.id', '=', 'hcc.id_historic_user_candidate')
            ->join('lective_candidate as lc', 'lchf.id_fase', '=', 'lc.id')
            ->join('classes as c', 'hcc.id_classe', '=', 'c.id')
            ->join('courses_translations as ct', 'ct.courses_id', '=', 'c.courses_id')
            ->join('lective_year_translations as lyt', 'lc.id_years', '=', 'lyt.lective_years_id')
            ->select(
                'c.display_name as turma',
                'ct.display_name as curso',
                'lc.fase',
                'lyt.display_name as ano_lectivo',
                'c.id as class_id',
                'ct.courses_id as curso_id',
                'lc.id as fase_id',
                'lchf.id'
            )
            ->where('lchf.user_id', $user_id)
            ->where('ct.active', 1)
            ->where('lyt.active', 1)
            ->get();
        return response()->json($json);
    }

    public function course($user_id)
    {
        $user = User::with('courses')->whereId($user_id)->first();
        $array = [];
        foreach ($user->courses as $item) {
            $tr = DB::table('courses_translations')->where('courses_id', $item->id)->where('active', 1)->first();
            array_push($array, $tr);
        }
        return response()->json($array);
    }


    public function defaultCurso(Request $request)
    {
        $auth = Auth::user()->id;
        $currentData = Carbon::now();

        DB::table('courses_default')->updateOrInsert([
            "users_id" => $request->user_id, "courses_id" => $request->course_id
        ], [
            "users_id" => $request->user_id, "courses_id" => $request->course_id,
            "created_by" => $auth, "updated_by" => $auth,
            "created_at" => $currentData, "updated_at" => $currentData
        ]);

        Toastr::success(_('Curso definido como padrão com successo'), __('toastr.success'));
        return redirect()->back();
    }


    public function transferenciaFase(Request $request)
    { 
        DB::beginTransaction();
        
        try {
        
           $currentData = Carbon::now();
             $lectiveYearSelected = DB::table('lective_years')
            ->where("id",$request->lective_year)
            ->first();
             
        if ($request->manterCourseAnClass == "no") {
         
           
           
          
            $candidate = User::with('classes', 'courses', 'disciplines')
                                    ->whereId($request->user_id)
                                    ->first();
                
            if (!isset($request->courseJoinClass)) {
                Toastr::warning(_('Seleciona o curso e a turma.'), __('toastr.warning'));
                return redirect()->back();
            }

            $courseJoinClass = $request->courseJoinClass;

            $Classes = [];
            $Courses = [];

            foreach ($courseJoinClass as $item) {
                $Array = explode("+", $item);
                $Courses[] = $Array[0];
                $Classes[] = $Array[1];
            }
   
            $Verify = DB::table('lective_candidate_historico_fase')
                ->where(['user_id' => $request->user_id, 'id_fase' => $request->faseNextId])
                ->first();
            
      
            if (!isset($Verify->id)) {

                $id_historic_candidate = DB::table('lective_candidate_historico_fase')->insertGetId(
                    ['user_id' => $request->user_id, 'id_fase' => $request->faseNextId, 'created_by' => Auth::user()->id]
                );
                
                collect($candidate->classes)->map(function ($item) use ($request, $id_historic_candidate) {
                        //Relacionamento entre historico e as turmas
                    DB::table('historic_classe_candidate')->updateOrInsert(
                        ['id_classe' => $item->id, 'id_historic_user_candidate' => $id_historic_candidate],
                        ['id_classe' => $item->id, 'id_historic_user_candidate' => $id_historic_candidate]
                    );
                });

                // Mudança de curso e de turma 
                $candidate->classes()->sync($Classes);
                $candidate->courses()->sync($Courses);
                
                
                disciplinesSelectForCandidates($Courses)->map(function ($item) {
                    return ["courses_id" => $item->courses_id, "disciplina" => $item->id];
                })->unique('courses_id')->map(function ($item) use ($request,$lectiveYearSelected) {
                    $disciplina_id = $item['disciplina'];
                    $emolumento_exame  = EmolumentCodevLective('exame', $lectiveYearSelected->id);
                if(!$emolumento_exame->isEmpty()){
                     return createAutomaticArticleRequestCandidate($request->user_id, $emolumento_exame[0]->id_emolumento, null, null, $disciplina_id);
                }else{

                }
                });
                
               
              
             
                DB::table('user_candidate')->updateOrInsert(
                    [
                    'user_id' => $request->user_id, 
                    'year_fase_id' => $request->faseNowId
                    ],
                    [
                     'year_fase_id' => $request->faseNextId,
                     'updated_at'=>now(), 
                     'updated_by'=>Auth::user()->id
                     
                     ]
                );                
            
             
                
                }else {
                    Toastr::warning(_('A forLEARN detectou que a transferência deste candidato nesta fase encontra-se realizada! Verifica na lista principal.'), __('toastr.warning'));
                    return redirect()->route('candidates.index');
                }
            
           
            
        }
        
        
        else {
            $candidate = User::with('classes', 'courses', 'disciplines')->whereId($request->user_id)->first();

            $discipline = collect($candidate->disciplines)
                ->map(function ($item) {
                    return $data = ["courses_id" => $item->courses_id, "disciplina" => $item->id];
                });

            $unique = $discipline->unique('courses_id')->map(function ($item) use ($request, $lectiveYearSelected) {
                    $disciplina_id = $item['disciplina'];
                    // return createAutomaticArticleRequestCandidate($request->user_id, 135, null, null, $disciplina_id);
                    
                    $emolumento_exame  = EmolumentCodevLective('exame', $lectiveYearSelected->id);
                if(!$emolumento_exame->isEmpty()){
                     return createAutomaticArticleRequestCandidate($request->user_id, $emolumento_exame[0]->id_emolumento, null, null, $disciplina_id);
                }else{

                }
                });

            $Verify = DB::table('lective_candidate_historico_fase')
                ->where(['user_id' => $request->user_id, 'id_fase' => $request->faseNextId])
                ->first();

            if (!isset($Verify->id)) {

                $id_historic_candidate = DB::table('lective_candidate_historico_fase')->insertGetId(
                    ['user_id' => $request->user_id, 'id_fase' => $request->faseNextId, 'created_by' => Auth::user()->id]
                );

                collect($candidate->classes)->map(function ($item) use ($request, $id_historic_candidate) {
                        //Relacionamento entre historico e as turmas
                    DB::table('historic_classe_candidate')->updateOrInsert(
                        ['id_classe' => $item->id, 'id_historic_user_candidate' => $id_historic_candidate],
                        ['id_classe' => $item->id, 'id_historic_user_candidate' => $id_historic_candidate]
                    );
                });

                DB::table('user_candidate')->updateOrInsert(
                    ['user_id' => $request->user_id, 'year_fase_id' => $request->faseNowId],
                    ['year_fase_id' => $request->faseNextId,
                     'updated_at'=>now(), 
                     'updated_by'=>Auth::user()->id
                     ]
                );

            } else {
                Toastr::warning(_('A forLEARN detectou a transferência deste candidato nesta fase encontra-se realizada! Verifica na lista principal.'), __('toastr.warning'));
                return redirect()->route('candidates.index');
            }
        }
          DB::commit();
        Toastr::success(_('Transferência realizada com sucesso'), __('toastr.success'));
        return redirect()->route('candidates.index');
        
        
        
        } catch (Exception $e) {
             DB::rollBack();
            logError($e);
            return $e;
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
        
        
        
        
        
        
    }


    public function userTrans(Request $request)
    {

        $this->candidateUtil->actualizarDatasCalendariosPassaram();

        $user = DB::table('user_candidate as uc')
            ->join('users as u', 'u.id', '=', 'uc.user_id')
            ->select('uc.*', 'u.*')
            ->where('uc.user_id', $request->user)
            ->first();

        if (!isset($user->user_id)) {
            Toastr::warning(_('Usuário não encontrado.'), __('toastr.warning'));
            return redirect()->back();
        }

        $lectiveCandidate = DB::table('lective_candidate')->find($request->lective_candidate_id);

        if (!isset($lectiveCandidate->id)) {
            Toastr::warning(_("A forLEARN não detectou nenhuma fase!"), __('toastr.warning'));
            return redirect()->back();
        }

        $currentData = Carbon::now();

        $lectiveCandidateNext = DB::table('lective_candidate')
            ->whereRaw('"' . $currentData . '" between `data_inicio` and `data_fim`')->first();

        if (!isset($lectiveCandidateNext->id)) {
            Toastr::warning(_("Atenção! A forLEARN informa que esta data não sé encontra em nenhum intervalo da fase."), __('toastr.warning'));
            return redirect()->back();
        }

        if ($lectiveCandidateNext->is_termina) {
            Toastr::warning(_("Atenção! A forLEARN o periodo da fase de candidatura que pretende fazer a transferência encontra-se terminada, por favor verifique o calendário de fase, Caso contrário consulte o apoio a forLEARN."), __('toastr.warning'));
            return redirect()->back();
        }

        $lectiveYears = LectiveYear::with(['currentTranslation'])
            ->get();



        $lectiveYearSelected = DB::table('lective_years')
            ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
            ->first();

        $lectiveYearsUser = DB::table('lective_years')->find($request->ano_lective);


        if ($lectiveYearsUser->id < $lectiveYearSelected->id) {
            $nextCode = $this->faseCandidateUtil->nextCode();

            $userCandidate = $this->faseCandidateUtil->createUserCandidate(
                $nextCode,
                $user->user_id,
                $lectiveCandidate,
                $lectiveCandidateNext
            );
            
            $this->candidateController->options = (object)[
                "transf" => true,
                "lectiveCandidateNow" => $lectiveCandidate,
                "lectiveCandidateNext" => $lectiveCandidateNext,
                "userCandidate" => $userCandidate,
                "nextCode" => $userCandidate->code,
                "action" => "edit"
            ];

            return $this->candidateController->edit($request->user);
        }

        $courses = Course::with([
            'currentTranslation'
        ])->get();

        $lectiveYearSelected = $lectiveYearSelected->id ?? 6;

        return  view(
            "Users::candidate.fase_candidatura.transferencia",
            compact('courses', 'lectiveCandidate', 'lectiveYears', 'lectiveCandidateNext', 'lectiveYearSelected', 'user')
        );
    }

    /* transferência de aluno */
    public function update(Request $request, $id)
    {
        try {
           
            if ($request->has('attachment_parameters')) {
                $files = $request->file('attachment_parameters');
                if (is_array($files)) {
                    foreach ($files as $index_parameter_group => $parameter) {
                        foreach ($parameter as $index_parameter => $file) {
                            $filename = $file->getClientOriginalName();
                            $fileExtension = pathinfo($filename, PATHINFO_EXTENSION);
                            // return $index_parameter;
                            // return
                            if ($index_parameter == 29 && $fileExtension != "pdf") {
                                Toastr::error("Erro com o arquivo", __('toastr.error'));
                                return redirect()->back();
                            }

                            if ($index_parameter == 56 && $fileExtension != "pdf") {
                                Toastr::error("Erro com o arquivo", __('toastr.error'));
                                return redirect()->back();
                            }

                            if ($index_parameter == 17 && $fileExtension != "pdf") {
                                Toastr::error("Erro com o arquivo", __('toastr.error'));
                                return redirect()->back();
                            }

                            if ($index_parameter == 202 && $fileExtension != "pdf") {
                                Toastr::error("Erro com o arquivo", __('toastr.error'));
                                return redirect()->back();
                            }
                        }
                    }
                }
            }
           
            $isCandidate = User::whereId($id)->firstOrFail();
            if (!$isCandidate->hasAnyRole(['candidado-a-estudante'])) {
            }

            DB::beginTransaction();

            $current_user = Auth::user();

            $user = User::whereId($id)->firstOrFail();

            $user->name = $request->get('name');
            $user->email = $request->get('email');
            if (!empty($request->get('password'))) {
                $user->password = bcrypt($request->get('password'));
            }
            $user->updated_by = auth()->user()->id;
            $user->save();

            $user->parameters()->sync([]);

            $user_parameters = [];
            if ($request->has('attachment_parameters')) {
                $updated_parameters = [];

                $files = $request->file('attachment_parameters');
                if (is_array($files)) {
                    foreach ($files as $index_parameter_group => $parameter) {
                        foreach ($parameter as $index_parameter => $file) {
                            $filename = $user->id . '_file_' . $index_parameter . '_' . $file->getClientOriginalName();

                            $file->storeAs('attachment', $filename);

                            //if($index_parameter != 311 && $index_parameter_group != 11)
                            $user_parameters[] = [
                                'parameters_id' => $index_parameter,
                                'created_by' => $current_user->id ?? 0,
                                'parameter_group_id' => $index_parameter_group,
                                'value' => $filename,
                                'updated_by' => $current_user->id,
                                'updated_at'=> Carbon::now()
                            ];

                            // if($index_parameter != 311 && $index_parameter_group != 11)
                            $updated_parameters[] = [
                                'parameters_id' => $index_parameter,
                                'parameter_group_id' => $index_parameter_group,
                                'updated_by' => $current_user->id,
                                'updated_at'=> Carbon::now()
                            ];
                        }
                    }
                }
            }

            if ($request->has('parameters')) {
                foreach ($request->get('parameters') as $index_parameter_group => $parameters) {
                    foreach ($parameters as $index_parameter => $parameter) {
                        $value = is_array($parameter) ? implode(',', $parameter) : $parameter ?? '';


                        if (!empty($updated_parameters)) {
                            foreach ($updated_parameters as $updated_parameter) {
                                if ($index_parameter_group === $updated_parameter['parameter_group_id'] && $index_parameter === $updated_parameter['parameters_id']) {
                                    continue 2;
                                }
                            }
                        }


                        //if($index_parameter != 311 && $index_parameter_group != 11)
                        $user_parameters[] = [
                            'parameters_id' => $index_parameter,
                            'created_by' => $current_user->id ?? 0,
                            'parameter_group_id' => $index_parameter_group,
                            'value' => $value,
                            'updated_by' => $current_user->id,
                            'updated_at'=> Carbon::now()
                        ];


                        if (!$isCandidate->hasAnyRole(['candidado-a-estudante'])) {
                            if ($index_parameter === 19) {
                                $findDuplicateMechanographic = UserParameter::where('parameters_id', 19)
                                    ->where('value', $value)
                                    ->count();

                                if ($findDuplicateMechanographic) {
                                    return redirect()->back()->withErrors(['Nº de: Matrícula | Mecanográfico já existe'])->withInput();
                                }
                            }
                        }
                    }
                }
            }

            if (!empty($user_parameters)) {
                $user->parameters()->sync($user_parameters);
            }

            $userCandidate = DB::table('user_candidate')
                ->where('user_id', $user->id)
                ->first();

            DB::table('user_parameters')
                ->updateOrInsert(
                    ['parameter_group_id' => 1, 'parameters_id' => 311, 'users_id' => $user->id],
                    ['value' => $userCandidate->code, 'created_by' => 1, 'updated_by' => 1]
                );

            DB::table('user_parameters')
                ->updateOrInsert(
                    ['parameter_group_id' => 1, 'parameters_id' => 312, 'users_id' => $user->id],
                    ['value' => $request->get('email'), 'created_by' => 1, 'updated_by' => 1]
                );

            DB::table('user_parameters')
                ->updateOrInsert(
                    ['parameter_group_id' => 11, 'parameters_id' => 311, 'users_id' => $user->id],
                    ['value' => $userCandidate->code, 'created_by' => 1, 'updated_by' => 1]
                );

            DB::table('user_parameters')
                ->updateOrInsert(
                    ['parameter_group_id' => 11, 'parameters_id' => 312, 'users_id' => $user->id],
                    ['value' => $request->get('email'), 'created_by' => 1, 'updated_by' => 1]
                );


            if ($request->has('course') && $user->hasAnyRole(['teacher', 'candidado-a-estudante'])) {
                $c = $request->get('course');
                if ($c) {
                    foreach ($c as $course) {
                        $courses[] = (int)$course;
                    }
                }
            }

            if (!empty($courses)) {
                $user->courses()->sync($courses);
            }

            $classes = [];
            if ($request->has('classes') && $user->hasAnyRole(['candidado-a-estudante', 'student', 'teacher'])) {
                $cl = $request->get('classes');
                if ($cl) {
                    foreach ($cl as $class) {
                        $classes[] = (int)$class;
                    }
                }
            }

            if (!empty($classes)) {
                $user->classes()->sync($classes);
            }

            $disciplines = [];
            if ($request->has('disciplines')) {
                $d = $request->get('disciplines');
                if ($d) {
                    foreach ($d as $discipline) {
                        $disciplines[] = (int)$discipline;
                    }
                }
            }

            if (!empty($disciplines)) {
                $user->disciplines()->sync($disciplines);
            }

            $data = [
                'disciplines' => $request->get('disciplines')
            ];

            foreach ($data['disciplines'] as $item) {
                $faseActual = FaseCandidaturaUtil::faseActual();
                $emolumento_confirmacao  = EmolumentCodevLective('exame',$faseActual->id_years ?? 0);
                if(!$emolumento_confirmacao->isEmpty()){
                    createAutomaticArticleRequestCandidate($user->id, $emolumento_confirmacao[0]->id_emolumento, null, null, $item);
                }else{

                }
            }

            DB::commit();

            if (auth()->user()->hasRole(['candidado-a-estudante'])) {
                $dataEmail = [
                    "name" => $request->get('parameters')[2][1],
                    "email" => $request->get('parameters')[6][34],
                ];
                Mail::send('Users::users.email.link-formulario', $dataEmail, function ($info) use ($dataEmail) {
                    $info->to($dataEmail['email'], $dataEmail['name'])->subject('Candidatura | Envio de Formulário');
                    $info->from('candidaturas@ispm.co.ao', 'Candidatura | Envio de Formulário');
                });

            }
            Toastr::success("Candidatura editada com sucesso", __('toastr.success'));
            return redirect()->route('candidates.show', $user->id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Users::users.not_found_message'), __('toastr.error'));
            logError($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
}
