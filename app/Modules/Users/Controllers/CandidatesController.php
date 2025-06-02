<?php

namespace App\Modules\Users\Controllers;

use App\Mail\NewLinkAccessCandidate;
use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\GA\Models\Course;
use App\Modules\GA\Models\Department;
use App\Modules\GA\Models\Discipline;
use App\Modules\GA\Models\LectiveYear;
use App\Modules\Payments\Models\ArticleRequest;
use App\Modules\Users\Models\Parameter;
use App\Modules\Users\Models\ParameterGroup;
use App\Modules\Users\Models\Permission;
use App\Modules\Users\Models\Role;
use App\Modules\Users\Models\TeacherClass;
use App\Modules\Users\Models\User;
use App\Modules\Users\Models\UserCandidate;
use App\Modules\Users\Models\UserParameter;
use App\Modules\Users\Requests\UserRequest;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\PseudoTypes\False_;
use Illuminate\Support\Facades\Mail;
use App\Modules\Users\util\CandidatesUtil;
use App\Modules\Users\util\FaseCandidaturaUtil;
use App\Modules\Users\util\EnumVariable;
use App\Model\Institution;
use App\Modules\Users\Enum\ParameterGroupEnum;
use App\Modules\Users\Enum\ParameterEnum;
use Carbon\Carbon;
use DataTables;
use Throwable;
use PdfMerger;
use Exception;
use Storage;
use Toastr;
use Auth;
use PDF;
use Log;
use DB;
use App\Modules\Users\Exports\CandidatesExport;
use Maatwebsite\Excel\Facades\Excel;
class CandidatesController extends Controller
{

  private $candidateUtil;
  public $options;

  function __construct()
  {
    $this->options = null;
    $this->candidateUtil = new CandidatesUtil();
  }


  public function index()
  {
    try {
      $this->actualizarDatasCalendariosPassaram();

      DB::update('UPDATE lective_years SET is_termina= 1 WHERE end_date < CURDATE()');
      //se o usuario for candidato a estudante, redirecionar para o perfil
      $userId = auth()->user()->id;
      $user = User::whereId($userId)->first();

      $data = [
        'action' => 'show'
      ];

      if ($user->hasAnyRole(['candidado-a-estudante'])) {
        return redirect()->route('candidates.show', $userId)->with($data);
      }

      $lectiveYears = LectiveYear::with(['currentTranslation'])
        ->get();

      $courses = Course::with([
        'currentTranslation'
      ])->get();

      $currentData = Carbon::now();
      $lectiveYearSelected = DB::table('lective_years')
        ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
        ->first();
      $lectiveYearSelected = $lectiveYearSelected->id ?? 6;

      $lectiveCandidateNext = DB::table('lective_candidate')
        ->whereRaw('"' . $currentData . '" between `data_inicio` and `data_fim`')
        ->first();

      $lectiveYearSelectedStausCandadatura = DB::table('lective_candidate_calendarie')->where('id_years', $lectiveYearSelected)->first();
      return view(
        'Users::candidate.index',
        compact(
          'lectiveYears',
          'lectiveYearSelected',
          'lectiveYearSelectedStausCandadatura',
          'lectiveCandidateNext',
          'courses'
        )
      );
    } catch (Exception | Throwable $e) {
      return $e;
      Log::error($e);
      Toastr::error($e->getMessage(), __('toastr.error'));
      return redirect()->back();
    }
  }






  public function actualizar($id_ano)
  {
    return $id_ano;
  }








  public function listaCandidate()
  {
    //se o usuario for candidato a estudante, redirecionar para o perfil
    $userId = auth()->user()->id;
    $user = User::whereId($userId)->first();

    $courses = Course::with([
      'currentTranslation'
    ])
      ->where('id', '!=', 22)
      ->where('id', '!=', 18);

    if ($user->hasAnyRole(['candidado-a-estudante'])) {

      return redirect()->route('candidates.show', $userId)->with($data);
    }

    $lectiveYears = LectiveYear::with(['currentTranslation'])
      ->get();

    $currentData = Carbon::now();
    $lectiveYearSelected = DB::table('lective_years')
      ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
      ->first();
    $lectiveYearSelected = $lectiveYearSelected->id ?? 6;

    $data = [
      'courses' => $courses->get(),
      'lectiveYearSelected' => $lectiveYearSelected,
      'lectiveYears' => $lectiveYears
    ];


    return view('Users::candidate.listar-candidate')->with($data);
  }



  private function ajaxYearLective($model, $cursos = [])
  {
    return $this->candidateUtil->addColumnCheckBox($model)
      ->addColumn('actions', function ($item) {
        return view('Users::candidate.datatables.actions')->with('item', $item);
      })
      ->addColumn('states', function ($state) use ($cursos) {
        return view('Users::candidate.datatables.states', compact('cursos', 'state'));
      })
      ->addColumn('cursos', function ($cadidate) {
        return view('Users::candidate.datatables.courses_states', compact('cadidate'));
      })
      ->rawColumns(['actions', 'states', 'cursos', 'bi_doc', 'diploma', 'foto'])
      ->addIndexColumn()
      ->toJson();
  }
  public function ajax()
  {
    try {

      $currentData = Carbon::now();
      $lectiveYear = DB::table('lective_years')
        ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
        ->first();

      if (!isset($lectiveYear->id)) {
        return $this->ajaxYearLective([]);
      }

      $lectiveCandidate = DB::table('lective_candidate')
        ->where('id_years', $lectiveYear->id)
        ->where('fase', 1)
        ->first();

      if (!isset($lectiveCandidate->id)) {
        return $this->ajaxYearLective([]);
      }

      $model = $this->candidateUtil->modelQueryGet($lectiveYear, $lectiveCandidate->id);

      $cursos = $this->candidateUtil->cursoQueryGet($lectiveYear, $lectiveCandidate->id);

      return $this->ajaxYearLective($model, $cursos);
    } catch (Exception | Throwable $e) {
      Log::error($e);
      return response()->json($e->getMessage(), 500);
    }
  }

  public static function get_course($id)
  {
    $cursos = DB::table('user_courses as uc')
      ->leftJoin('courses_translations as ct', function ($join) {
        $join->on('ct.courses_id', '=', 'uc.courses_id');
        $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
        $join->on('ct.active', '=', DB::raw(true));
      })
      ->where('uc.users_id', $id)
      ->get();

    $my_course = [];

    if ((count($cursos) > 0) && (isset($cursos))) {
      foreach ($cursos as $item) {
        array_push($my_course, $item->display_name);
      }
    }

    return ($my_course);
  }

  public static function get_emolumentos($year, $id)
  {
    $emolumentos = DB::table('articles as art')
      ->leftJoin('article_requests as ar', 'ar.article_id', '=', "art.id")
      ->where('art.anoLectivo', $year)
      ->whereNull('ar.deleted_at')
      ->where('art.id', 135)
      ->where('ar.user_id', $id)
      ->get();
    return ($emolumentos);
  }










  public function getStudentsBy($lectiveYear)
  {
    try {

      $lectiveYear = LectiveYear::where('id', $lectiveYear)->first();

      $lectiveCandidate = DB::table('lective_candidate')
        ->where('id_years', $lectiveYear->id)
        ->where('fase', 1)
        ->first();

      $model = $this->candidateUtil->modelQuery($lectiveYear)
        ->where('uca.year', $lectiveYear->id)
        ->where('uca.year_fase_id', $lectiveCandidate->id)
        ->get();

      $cursos = $this->candidateUtil->cursoQueryGet($lectiveYear);


      return Datatables::of($model)
        ->addColumn('actions', function ($item) {
          return view('Users::candidate.datatables.actions')->with('item', $item);
        })
        // ->addColumn('courses', function ($item) {
        //    return $item->courses->map(function ($course) {
        //    return $course->currentTranslation->display_name;
        //    })->implode(", ");
        //    //return $item->roles->first()->currentTranslation->display_name;
        // })
        ->addColumn('states', function ($state) use ($cursos) {
          return view('Users::candidate.datatables.states', compact('cursos', 'state'));
        })
        ->addColumn('cursos', function ($cadidate) use ($cursos) {
          return view('Users::candidate.datatables.courses_states', compact('cursos', 'cadidate'));
        })
        ->rawColumns(['actions', 'states', 'cursos'])
        ->addIndexColumn()
        ->toJson();
    } catch (Exception | Throwable $e) {
      Log::error($e);
      return response()->json($e->getMessage(), 500);
    }
  }



  public function create()
  {
    try {

      $currentData = Carbon::now();
      $faseNext = FaseCandidaturaUtil::faseActual();

      if (!isset($faseNext->id)) {
        Toastr::warning("Atenção ! não foi possivível prosseguir com a candidatura porque esta data [" . ($currentData->format('d-m-Y')) . "] actual não pertence em nenhum intervalo de fases.", __('toastr.warning'));
        return redirect()->back();
      }

      $roles = Role::with([
        'currentTranslation'
      ])->where('id', 15)->first();

      $data = [
        'action' => 'create',
        //'parameters' => $parameters,
        //'parameter_groups' => $parameter_groups, 
        'roles' => $roles,
      ];

      $lectiveYears = LectiveYear::with(['currentTranslation'])->get();

      $lectiveYearSelected = DB::table('lective_years')
        ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
        ->first();

      $lectiveYearSelected = $lectiveYearSelected->id ?? 6;

      return view('Users::candidate.candidate', compact('lectiveYears', 'lectiveYearSelected'))->with($data);
    } catch (Exception | Throwable $e) {
      Log::error($e);
      Toastr::error($e->getMessage(), __('toastr.error'));
      return redirect()->back();
    }
  }



  public function validate_PassWord($passwWord)
  {


    $biNumber = User::leftJoin('user_parameters as u_p', function ($join) use ($passwWord) {
      $join->on('users.id', '=', 'u_p.users_id')
        ->where('u_p.parameters_id', 14);
    })
      ->where('u_p.value', '=', $passwWord)
      ->get();
    if (count($biNumber) > 0) {
      // return "Já está";
      return response()->json(['status' => 1]);
    } else {

      return response()->json(['status' => 0]);
    }
  }

  private function createNewCode($latestsCandidate)
  {
    if ($latestsCandidate && Carbon::parse($latestsCandidate->created_at)->year === Carbon::now()->year) {
      $nextCode = 'CE' . ((int) ltrim($latestsCandidate->code, 'CE') + 1);
    } else {
      $nextCode = 'CE' . substr(Carbon::now()->format('Y'), -2) . '0001';
    }
    return $nextCode;
  }

  public function store(Request $request)
  {
    try {
      $faseNext = FaseCandidaturaUtil::faseActual();

      if (!isset($faseNext->id)) {
        Toastr::warning("Atenção ! não foi possivível prosseguir com a candidatura porque esta data [" . ($currentData->format('d-m-Y')) . "] actual não pertence em nenhum intervalo de fases.", __('toastr.warning'));
        return redirect()->back();
      }

      DB::beginTransaction();

      $auth_id = auth()->user()->id ?? 1;
      $user = User::withTrashed()->where('email', $request->get('email'))->first();

      if (isset($user)) {
        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->password = bcrypt($request->get('id_number'));
        $user->updated_by = $auth_id;
        $user->deleted_at = null;
        $user->save();
      } else {
        $user = User::create([
          'name' => $request->get('name'),
          'email' => $request->get('email'),
          'password' => bcrypt($request->get('id_number')),
          'created_by' => $auth_id
        ]);
        $user->save();
      }

      $user_parameters[] = [
        'parameters_id' => ParameterEnum::NOME,
        'created_by' => $auth_id,
        'parameter_group_id' => ParameterGroupEnum::DADOS_PESSOAIS,
        'value' => $request->get('full_name')
      ];

      $user->parameters()->sync($user_parameters);

      // Roles
      $user->syncRoles($request->get('roles'));

      if ((int) $request->get('roles') === 15) {

        $latestsCandidate = DB::table('user_candidate')->orderBy('id', 'DESC')->first();
        $nextCode = $this->createNewCode($latestsCandidate);
        $Verificar = DB::table('user_candidate')->where('code', $nextCode)->get();

        if (count($Verificar) > 0) {
          Toastr::error("Atenção! não foi possivível prosseguir com a candidatura porque no momento de gerar o número automático de candidato houve um conflito com um registo já existente, tente novamente, no caso de persitir o erro contacte o Apoio a forLEARN. code: " . $nextCode, __('toastr.error'));
          return back();
        }

        $dataCandidate = ['user_id' => $user->id, 'year_fase_id' => $faseNext->id, 'year' => $faseNext->id_years];

        $userCandidate = UserCandidate::where($dataCandidate)->orderBy('id', 'DESC')->first();
        if (!isset($userCandidate->id)) {
          $dataCandidate["code"] = $nextCode;
          $dataCandidate["created_at"] = Carbon::now();
          $dataCandidate["created_by"] = $auth_id;
          UserCandidate::create($dataCandidate);
        } else {
          $nextCode = $userCandidate->code;
          $dataCandidate["updated_at"] = Carbon::now();
          $dataCandidate["updated_by"] = $auth_id;
          $userCandidate->update($dataCandidate);
        }

        $user_number[] = [
          'parameters_id' => ParameterEnum::NUMERO_CANDIDADO,
          'created_by' => $auth_id,
          'parameter_group_id' => ParameterGroupEnum::IDENTIFICACAO_ISPM,
          'value' => $nextCode
        ];

        $user->parameters()->attach($user_number);

        $user_n_number[] = [
          'parameters_id' => ParameterEnum::NUMERO_CANDIDADO,
          'created_by' => $auth_id,
          'parameter_group_id' => ParameterGroupEnum::IDENTIFICACAO_ISPM_CANDIDATO,
          'value' => $nextCode
        ];

        $user->parameters()->attach($user_n_number);

        $user_n_bilhete[] = [
          'parameters_id' => ParameterEnum::BILHETE_DE_IDENTIDADE,
          'created_by' => $auth_id,
          'parameter_group_id' => ParameterGroupEnum::DOCUMENTOS_PESSOAIS_CANDIDATO,
          'value' => $request->get('id_number')
        ];

        $user->parameters()->attach($user_n_bilhete);

        $this->candidateUtil->biStaffEstudante($request->get('id_number'), $user->id);

        $user_nif[] = [
          'parameters_id' => ParameterEnum::NUMERO_DE_IDENTIFICACAO_FISCAL,
          'created_by' => $auth_id,
          'parameter_group_id' => ParameterGroupEnum::DOCUMENTOS_PESSOAIS_CANDIDATO,
          'value' => $request->get('id_number')
        ];

        $user->parameters()->attach($user_nif);

        $user_email[] = [
          'parameters_id' => ParameterEnum::EMAIL,
          'created_by' => 1 ?? 0,
          'parameter_group_id' => ParameterGroupEnum::IDENTIFICACAO_ISPM_CANDIDATO,
          'value' => $request->get('email')
        ];

        $user->parameters()->attach($user_email);
      }

      DB::commit();


      if ((int) $request->get('roles') === 15) {
        $user = User::whereId($user->id)->first();
        $data = ['action' => 'edit'];
        return redirect()->route('candidates.edit', $user->id)->with($data);
      }

      Toastr::success(__('Users::users.store_success_message'), __('toastr.success'));
      return redirect()->route('users.index');
    } catch (Exception | Throwable $e) {
      dd($e->getMessage());
      Log::error($e);
      return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
    }
  }

  public function edit($id)
  {
    $action = 'edit';
    try {
      $user = User::whereId($id)->first();


      return $this->fetch($user, $action);


    } catch (ModelNotFoundException $e) {
      Toastr::error(__('Users::users.not_found_message'), __('toastr.error'));
      logError($e);
      return redirect()->back() ?? abort(500);
    } catch (Exception | Throwable $e) {
      logError($e);
      return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
    }
  }

  public function show($id)
  {
    $action = 'show';
    try {
      $user = User::whereId($id)->first();
      return $this->fetch($user, $action);
    } catch (ModelNotFoundException $e) {
      Toastr::error(__('Users::users.not_found_message'), __('toastr.error'));
      logError($e);
      return redirect()->back() ?? abort(500);
    } catch (Exception | Throwable $e) {
      logError($e);
      return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
    }
  }

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
        // if (!$this->matriculationNumberValidation($request, $id)->isEmpty() || !$this->BINumberValidation($request, $id)->isEmpty()) {
        //     Toastr::error("Há dados introduzidos em conflito com outro utilizador", __('toastr.error'));
        //     return redirect()->back();
        // }
      }

      $current_user = Auth::user()->id ?? 1;

      $user = User::whereId($id)->firstOrFail();

      $data = [];

      if (!empty($request->get('name'))) {
        $data['name'] = $request->get('name');
      }

      if (!empty($request->get('password'))) {
        $data['password'] = bcrypt($request->get('password'));
      }

      $data['updated_by'] = auth()->user()->id;
      $user->update($data);
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

              $user_parameters[] = [
                'parameters_id' => $index_parameter,
                'created_by' => $current_user,
                'parameter_group_id' => $index_parameter_group,
                'value' => $filename
              ];

              // Skip this parameter/group combination, since it was updated
              $updated_parameters[] = [
                'parameters_id' => $index_parameter,
                'parameter_group_id' => $index_parameter_group,
              ];
            }
          }
        }
      }

      if ($request->has('parameters')) {
        foreach ($request->get('parameters') as $index_parameter_group => $parameters) {
          foreach ($parameters as $index_parameter => $parameter) {
            $value = is_array($parameter) ? implode(',', $parameter) : $parameter ?? '';

            // If it was an updated file, delete it and skip this parameter
            if (!empty($updated_parameters)) {
              foreach ($updated_parameters as $updated_parameter) {
                if ($index_parameter_group === $updated_parameter['parameter_group_id'] && $index_parameter === $updated_parameter['parameters_id']) {
                  continue 2;
                }
              }
            }

            $user_parameters[] = ['parameters_id' => $index_parameter, 'created_by' => $current_user, 'parameter_group_id' => $index_parameter_group, 'value' => $value];

            if ($index_parameter == 14)
              $this->candidateUtil->biStaffEstudante($value, $user->id);

            if (!$isCandidate->hasAnyRole(['candidado-a-estudante'])) {
              if ($index_parameter === 19) {
                $findDuplicateMechanographic = UserParameter::where('parameters_id', 19)->where('value', $value)->count();
                if ($findDuplicateMechanographic)
                  return redirect()->back()->withErrors(['Nº de: Matrícula | Mecanográfico já existe'])->withInput();
              }
            }

          }
        }
      }

      if (!empty($user_parameters))
        $user->parameters()->sync($user_parameters);

      $userCandidate = DB::table('user_candidate')->where('user_id', $user->id)->first();

      DB::table('user_parameters')
        ->updateOrInsert(
          ['parameter_group_id' => ParameterGroupEnum::IDENTIFICACAO_ISPM, 'parameters_id' => ParameterEnum::NUMERO_CANDIDADO, 'users_id' => $user->id],
          ['value' => $userCandidate->code, 'created_by' => $current_user, 'updated_by' => $current_user]
        );

      DB::table('user_parameters')->updateOrInsert(
        ['parameter_group_id' => ParameterGroupEnum::IDENTIFICACAO_ISPM, 'parameters_id' => ParameterEnum::EMAIL, 'users_id' => $user->id],
        ['value' => $request->get('email'), 'created_by' => $current_user, 'updated_by' => $current_user]
      );

      DB::table('user_parameters')->updateOrInsert(
        ['parameter_group_id' => ParameterGroupEnum::IDENTIFICACAO_ISPM_CANDIDATO, 'parameters_id' => ParameterEnum::NUMERO_CANDIDADO, 'users_id' => $user->id],
        ['value' => $userCandidate->code, 'created_by' => $current_user, 'updated_by' => $current_user]
      );

      DB::table('user_parameters')->updateOrInsert(
        ['parameter_group_id' => ParameterGroupEnum::IDENTIFICACAO_ISPM_CANDIDATO, 'parameters_id' => ParameterEnum::EMAIL, 'users_id' => $user->id],
        ['value' => $request->get('email'), 'created_by' => $current_user, 'updated_by' => $current_user]
      );


      if ($request->has('course') && $user->hasAnyRole(['teacher', 'candidado-a-estudante'])) {
        $c = $request->get('course');
        if ($c) {
          foreach ($c as $course) {
            $courses[] = (int) $course;
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
            $classes[] = (int) $class;
          }
        }
      }

      if (!empty($classes))
        $user->classes()->sync($classes);

      $disciplines = [];
      if ($request->has('disciplines')) {
        $d = $request->get('disciplines');
        if ($d) {
          foreach ($d as $discipline) {
            $disciplines[] = (int) $discipline;
          }
        }
      }

      if (!empty($disciplines))
        $user->disciplines()->sync($disciplines);


      //caso for candidato a estudante gerar emolumento das disciplinas criadas
      //antes verificar se ja existe o emolumento
      //emolumento id => 6


      $orderDiscipline = DB::table('disciplines')
        ->whereIn('disciplines.id', $request->get('disciplines'))
        ->orderBy('disciplines.code')
        ->get() // Executa a consulta e obtém a coleção de resultados
        ->map(function ($item) {
          return $item->id;
        });

      $data = ['disciplines' => $orderDiscipline];

      //return $data['disciplines'][0];
      //foreach ($data['disciplines'] as $disciplineId) {
      //verificar se ja existe um emolumento com essa disciplina
      //TODO:: Verificar por ano lectivo.

      //Precisa-se vir colocar uma forma de trazer o emolumento de inscrição de cada ano lectivo
      //20/21 o id do emolumento foi : 6 
      //21/22 o id é 99 

      $coursesAndDisciplina = [];
      foreach ($data['disciplines'] as $disciplineid) {
        $dis = DB::table("disciplines")->find($disciplineid);
        if (!isset($coursesAndDisciplina[$dis->courses_id])) {
          $coursesAndDisciplina[$dis->courses_id] = $disciplineid;
        }
      }

      $faseActual = FaseCandidaturaUtil::faseActual();
      $emolumento_confirmacao = EmolumentCodevLective('exame', $faseActual->id_years ?? 0);

      foreach ($coursesAndDisciplina as $key => $value) {
        if (!$emolumento_confirmacao->isEmpty()) {
          $article = ArticleRequest::whereArticleId($emolumento_confirmacao[0]->id_emolumento)
            ->where('discipline_id', $value)
            ->where('user_id', $user->id)
            ->get();
          if ($article->isEmpty()) {
            createAutomaticArticleRequestCandidate($user->id, $emolumento_confirmacao[0]->id_emolumento, null, null, $value);
          }
        }
      }

      // DB::commit();

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


      DB::table('users')
        ->where("id", $id)
        ->update(
          ['updated_by' => auth()->user()->id],
          ['updated_at' => 'now()']
        );

      Toastr::success("Candidatura editada com sucesso ", __('toastr.success'));
      return redirect()->route('candidates.show', $user->id);
    } catch (ModelNotFoundException $e) {
      Toastr::error(__('Users::users.not_found_message'), __('toastr.error'));
      logError($e);
      return $e;
      // return redirect()->back() ?? abort(500);
    } catch (Exception | Throwable $e) {
      logError($e);
      return $e;
      // return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param User $user
   * @return Response
   */
  public function destroy($id)
  {
    try {
      DB::beginTransaction();
      $user = User::whereId($id)->first();

      // Find and delete
      $user->syncPermissions([]);
      $user->syncRoles([]);

      // $user->courses()->delete();
      // $user->disciplines()->delete();

      $user->delete();

      $user->deleted_by = auth()->user()->id;
      $user->save();

      DB::commit();
      // Success message
      Toastr::success(__('Users::users.destroy_success_message'), __('toastr.success'));
      return redirect()->route('candidates.index');
    } catch (ModelNotFoundException $e) {
      Toastr::error(__('Users::users.not_found_message'), __('toastr.error'));
      Log::error($e);
      return redirect()->back() ?? abort(500);
    } catch (Exception | Throwable $e) {
      Log::error($e);
      return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
    }
  }

  private function fetch($user, $action)
  {
    try {
      $parameter_groups = ParameterGroup::with([
        'currentTranslation',
        'roles',
        'parameters' => function ($q) {
          $q->with([
            'currentTranslation',
            'roles',
            'options' => function ($q) {
              $q->with([
                'currentTranslation',
                'relatedParametersRecursive'
              ]);
            }
          ]);
        }
      ])->orderBy('order')->get();

      // Find
      $user = $user->whereId($user->id)->with(
        [
          /*'parameters.parameter.options',*/
          'roles' => function ($q) {
            $q->with([
              'currentTranslation'
            ]);
          },
          'parameters' => function ($q) {
            $q->with([
              'currentTranslation',
              'groups',
            ]);
          },
          'courses',
          'disciplines',
          'candidate'
        ]
      )->firstOrFail();

      $dd = UserParameter::where('users_id', $user->id)->get();

      // Set relation keys
      /*$user->setRelation('parameters', $user->parameters->groupBy('parameters_id'));*/

      $courses = Course::with([
        'currentTranslation'
      ])
        //where('id','!=',22)
        ->get();

      $data = [
        'action' => $action,
        'user' => $user,
        'parameter_groups' => $parameter_groups,
        'courses' => $courses,
        'disciplines' => disciplinesSelectForCandidates(),
        'dd' => $dd,
        'options' => $this->options
      ];

      $view = 'Users::candidate.candidate_profile';
      if (isset($this->options->transf))
        $view = 'Users::candidate.candidate_transferencia';

      return view($view)->with($data);
    } catch (ModelNotFoundException $e) {
      Toastr::error(__('Users::users.not_found_message'), __('toastr.error'));
      Log::error($e);
      return redirect()->back() ?? abort(500);
    } catch (Exception | Throwable $e) {
      Log::error($e);
      return abort(500);
    }
  }


  public function relatorios()
  {

    $courses = Course::with([
      'currentTranslation'
    ])
      ->where('id', '!=', 22)
      ->where('id', '!=', 18);

    // if (auth()->user()->hasRole('teacher')) {
    //     $teacherCourses = auth()->user()->courses()->pluck('id')->all();
    //     $courses = $courses->whereIn('id', $teacherCourses);
    // }

    $lectiveYears = LectiveYear::with(['currentTranslation'])
      ->get();

    $currentData = Carbon::now();
    $lectiveYearSelected = DB::table('lective_years')
      ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
      ->first();
    $lectiveYearSelected = $lectiveYearSelected->id ?? 6;

    $data = [
      'courses' => $courses->get(),
      'lectiveYearSelected' => $lectiveYearSelected,
      'lectiveYears' => $lectiveYears
    ];
    return view("Users::candidate.relatorios")->with($data);
  }



  public function relatoriosPDF($anoletivo, Request $request)
  {
    try {
      if (isset($anoletivo)) {
        $lectiveYear = DB::table('lective_years')
          ->where('id', $anoletivo)
          ->first();
      }
      $lectiveCandidate = DB::table('lective_candidate')
        ->where('id_years', $lectiveYear->id)
        ->where('fase', 1)
        ->first();

      $cursos = $this->candidateUtil->modelQueryTwoCourseGet($lectiveYear, $request->fase);
      $all_emolumentos = $this->candidateUtil->cursoQueryGet($lectiveYear, $lectiveCandidate->id);
      $model = $this->candidateUtil->modelQueryGet($lectiveYear, $request->fase);
      // return $all_emolumentos;

      $twoCourse = [];

      foreach ($cursos as $item) {
        if ($item->state == 'total') {
          if (isset($twoCourse[$item->usuario_id])) {
            $twoCourse[$item->usuario_id]++;
          } else {
            $twoCourse[$item->usuario_id] = 1;
          }
        }
      }

      $twoCourseUsers = array_filter($twoCourse, function ($count) {
        return $count == 2;
      });
      $twoCourseUsers = count($twoCourseUsers);


      $array_candidates = array();

      foreach ($model as $candidates) {
        array_push($array_candidates, $candidates->id);
      }





      // return $all_emolumentos;
      // return $model;

      $lectiveYears = LectiveYear::with(['currentTranslation'])
        ->where('id', $anoletivo)
        ->select('*')
        ->get();

      $emolumentos_vagas = DB::table('articles as art')
        ->join('article_requests as ar', 'art.id', '=', 'ar.article_id')
        ->join('disciplines as disciplina', 'disciplina.id', '=', 'ar.discipline_id')
        ->join('courses as curso', 'disciplina.courses_id', '=', 'curso.id')
        ->join('courses_translations as ct', 'ct.courses_id', '=', 'curso.id')
        ->leftJoin('user_classes', 'user_classes.user_id', '=', 'ar.user_id')
        ->leftJoin('classes as turmas', function ($join) {
          $join->on('turmas.id', '=', 'user_classes.class_id');
        })
        ->join('users as usuario', 'ar.user_id', '=', 'usuario.id')
        ->join('user_candidate as uca', 'uca.user_id', '=', 'usuario.id')
        ->join('user_parameters as u_p1', function ($join) {
          $join->on('usuario.id', '=', 'u_p1.users_id')
            ->where('u_p1.parameters_id', 2);
        })
        ->where("art.id_code_dev", 6)
        ->where("ct.active", 1)
        ->where("uca.year", $lectiveYear->id)
        ->where("uca.year_fase_id", $request->fase)
        ->where("ar.status", "total")
        ->where('usuario.name', "!=", "")
        ->whereNull('ar.deleted_at')
        ->whereNull('disciplina.deleted_at')
        ->whereNull('ct.deleted_at')
        ->whereNull('art.deleted_at')
        ->whereNull('curso.deleted_at')
        ->whereNull('usuario.deleted_at')
        ->where('ar.discipline_id', "!=", null)
        ->whereBetween('ar.created_at', [$lectiveYear->start_date, $lectiveYear->end_date])
        ->select([
          "art.id as articles",
          "ar.id as articles_req",
          "ar.discipline_id as discipline",
          "curso.id as course",
          "ct.display_name as nome_curso",
          "ar.user_id as usuario_id",
          "ar.status as state",
          "ar.base_value as valor",
          "usuario.name as usuario",
          'user_classes.class_id as turma',
          'turmas.id as turma_nova',
          'turmas.code as turma',
          'turmas.schedule_type_id as turno',
          'u_p1.value as sexo'
        ])
        ->orderBy('ar.id', 'desc')
        ->get();


      $last_cand = DB::table('user_candidate')
        ->where("code", "like", "%CE%")
        ->where("year_fase_id", $request->fase)
        ->where("year", $lectiveYear->id)
        ->orderBy("code", "desc")
        ->first();

      if (!isset($last_cand->code)) {
        Toastr::warning("A forLEARN não detectou candidatos a estudantes nesta fase", 'Nenhum candidato');
        return redirect()->back();
      }

      // Se for a nova fase


      $last_cand = DB::table('user_candidate')
        ->where("code", "like", "%CE%")
        ->where("year_fase_id", $request->fase)
        ->where("year", $lectiveYear->id)
        ->orderBy("code", "desc")
        ->count();

      $todos_candidatos = $last_cand;

      $emolumentos_vagas = collect($emolumentos_vagas)->map(function ($item) {
        if ($item->sexo == "Feminino" || $item->sexo == 125 || $item->sexo == "feminino" || $item->sexo == "f" || $item->sexo == "F")
          $item->sexo = 'F';

        if ($item->sexo == "Masculino" || $item->sexo == 124 || $item->sexo == "masculino" || $item->sexo == "m" || $item->sexo == "M")
          $item->sexo = 'M';

        return $item;
      });



      $candidatos = collect($emolumentos_vagas)->groupBy("course")->map(function ($curso) use ($lectiveYear) {


        $estatisticas = [

          "money" => 0,
          'manha' => [
            'candidaturas' => [
              'total' => 0,
              'm' => 0,
              'f' => 0
            ],
            'exames' => [
              'total' => 0,
              'm' => 0,
              'f' => 0
            ],
            'ausentes' => [
              'total' => 0,
              'm' => 0,
              'f' => 0
            ],
            'reprovados' => [
              'total' => 0,
              'm' => 0,
              'f' => 0
            ],
            'admitidos' => [
              'total' => 0,
              'm' => 0,
              'f' => 0
            ],
            'matriculados' => [
              'total' => 0,
              'm' => 0,
              'f' => 0
            ],
          ],

          'tarde' => [
            'candidaturas' => [
              'total' => 0,
              'm' => 0,
              'f' => 0
            ],
            'exames' => [
              'total' => 0,
              'm' => 0,
              'f' => 0
            ],
            'ausentes' => [
              'total' => 0,
              'm' => 0,
              'f' => 0
            ],
            'reprovados' => [
              'total' => 0,
              'm' => 0,
              'f' => 0
            ],
            'admitidos' => [
              'total' => 0,
              'm' => 0,
              'f' => 0
            ],
            'matriculados' => [
              'total' => 0,
              'm' => 0,
              'f' => 0
            ],
          ],

          'noite' => [
            'candidaturas' => [
              'total' => 0,
              'm' => 0,
              'f' => 0
            ],
            'exames' => [
              'total' => 0,
              'm' => 0,
              'f' => 0
            ],
            'ausentes' => [
              'total' => 0,
              'm' => 0,
              'f' => 0
            ],
            'reprovados' => [
              'total' => 0,
              'm' => 0,
              'f' => 0
            ],
            'admitidos' => [
              'total' => 0,
              'm' => 0,
              'f' => 0
            ],
            'matriculados' => [
              'total' => 0,
              'm' => 0,
              'f' => 0
            ],
          ]

        ];

        // m = 6, t = 7, n = 3
        $turnos = ["qtd_m" => 0, "qtd_t" => 0, "qtd_n" => 0];

        foreach ($curso as $item) {

          if (isset($item->state) && $item->state == "total") {

            $estatisticas["money"] = $item->valor;
            if (isset($item->turno)) {

              $grades = DB::table('grades as g')
                ->where('student_id', $item->usuario_id)
                ->whereBetween('g.updated_at', [$lectiveYear->start_date, $lectiveYear->end_date])
                ->whereNull('g.deleted_by')
                ->whereNull('g.deleted_at')
                ->join('disciplines as d', 'g.discipline_id', '=', 'd.id')
                ->leftJoin('disciplines_translations as dt', function ($join) {
                  $join->on('dt.discipline_id', '=', 'd.id');
                  $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                  $join->on('dt.active', '=', DB::raw(true));
                })
                ->where('d.discipline_profiles_id', 8)
                ->select(['g.value as nota', 'dt.display_name as disciplina', 'd.percentage as percentagem'])
                ->get();



              if (isset($grades[0]->nota, $grades[1]->nota)) {
                $resultado = round(($grades[0]->nota * ($grades[0]->percentagem / 100)) + ($grades[1]->nota * ($grades[1]->percentagem / 100)));
              } else if (isset($grades[0]->nota) && !isset($grades[1]->nota)) {
                $resultado = $grades[0]->nota;
              } else {
                $resultado = round((($grades[0]->nota ?? 0) + ($grades[1]->nota ?? 0)) / 2);
              }

              $emolumento_confirma_prematricula = $this->pre_matricula_confirma_emolumento($lectiveYear->id);

              $matriculado = DB::table('matriculations')
                ->join('users as u0', 'u0.id', '=', 'matriculations.user_id')
                ->join('article_requests as art_requests', 'art_requests.user_id', '=', 'matriculations.user_id')
                ->join('articles', function ($join) {
                  $join->on('art_requests.article_id', '=', 'articles.id')
                    ->whereNull('articles.deleted_by')
                    ->whereNull('articles.deleted_at');
                })
                ->where('art_requests.status', 'total')
                ->where('matriculations.user_id', $item->usuario_id)
                ->whereIn('art_requests.article_id', $emolumento_confirma_prematricula)
                ->where('matriculations.lective_year', $lectiveYear->id)
                ->distinct()
                ->get();




              if ($item->turno == 11) {





                $estatisticas["manha"]['candidaturas']['total'] += 1;

                if ($item->sexo == 'M')
                  $estatisticas["manha"]['candidaturas']['m'] += 1;

                if ($item->sexo == 'F')
                  $estatisticas["manha"]['candidaturas']['f'] += 1;

                // exames
                if (!$grades->isEmpty()) {

                  $estatisticas["manha"]['exames']['total'] += 1;

                  if ($item->sexo == 'M')
                    $estatisticas["manha"]['exames']['m'] += 1;

                  if ($item->sexo == 'F')
                    $estatisticas["manha"]['exames']['f'] += 1;

                  //admitidos
                  if ($resultado >= 10) {
                    $estatisticas["manha"]['admitidos']['total'] += 1;

                    if ($item->sexo == 'M')

                      $estatisticas["manha"]['admitidos']['m'] += 1;

                    if ($item->sexo == 'F')
                      $estatisticas["manha"]['admitidos']['f'] += 1;


                    // matriculados
                    if (!$matriculado->isEmpty()) {

                      $estatisticas["manha"]['matriculados']['total'] += 1;

                      if ($item->sexo == 'M')

                        $estatisticas["manha"]['matriculados']['m'] += 1;

                      if ($item->sexo == 'F')
                        $estatisticas["manha"]['matriculados']['f'] += 1;
                    }


                  }
                  //reprovados
                  else {
                    $estatisticas["manha"]['reprovados']['total'] += 1;

                    if ($item->sexo == 'M')

                      $estatisticas["manha"]['reprovados']['m'] += 1;

                    if ($item->sexo == 'F')
                      $estatisticas["manha"]['reprovados']['f'] += 1;
                  }
                }

                // ausentes
                else {

                  $estatisticas["manha"]['ausentes']['total'] += 1;

                  if ($item->sexo == 'M')
                    $estatisticas["manha"]['ausentes']['m'] += 1;

                  if ($item->sexo == 'F')
                    $estatisticas["manha"]['ausentes']['f'] += 1;

                }


              }
              if ($item->turno == 12) {

                $estatisticas["tarde"]['candidaturas']['total'] += 1;

                if ($item->sexo == 'M')
                  $estatisticas["tarde"]['candidaturas']['m'] += 1;

                if ($item->sexo == 'F')
                  $estatisticas["tarde"]['candidaturas']['f'] += 1;


                // exames
                if (!$grades->isEmpty()) {
                  $estatisticas["tarde"]['exames']['total'] += 1;

                  if ($item->sexo == 'M')
                    $estatisticas["tarde"]['exames']['m'] += 1;

                  if ($item->sexo == 'F')
                    $estatisticas["tarde"]['exames']['f'] += 1;

                  //admitidos
                  if ($resultado >= 10) {
                    $estatisticas["tarde"]['admitidos']['total'] += 1;

                    if ($item->sexo == 'M')

                      $estatisticas["tarde"]['admitidos']['m'] += 1;

                    if ($item->sexo == 'F')
                      $estatisticas["tarde"]['admitidos']['f'] += 1;


                    // matriculados
                    if (!$matriculado->isEmpty()) {

                      $estatisticas["tarde"]['matriculados']['total'] += 1;

                      if ($item->sexo == 'M')

                        $estatisticas["tarde"]['matriculados']['m'] += 1;

                      if ($item->sexo == 'F')
                        $estatisticas["tarde"]['matriculados']['f'] += 1;
                    }


                  }
                  //reprovados
                  else {
                    $estatisticas["tarde"]['reprovados']['total'] += 1;

                    if ($item->sexo == 'M')

                      $estatisticas["tarde"]['reprovados']['m'] += 1;

                    if ($item->sexo == 'F')
                      $estatisticas["tarde"]['reprovados']['f'] += 1;
                  }
                }




                // ausentes
                else {
                  $estatisticas["tarde"]['ausentes']['total'] += 1;

                  if ($item->sexo == 'M')
                    $estatisticas["tarde"]['ausentes']['m'] += 1;

                  if ($item->sexo == 'F')
                    $estatisticas["tarde"]['ausentes']['f'] += 1;

                }
              }
              if ($item->turno == 13) {

                $estatisticas["noite"]['candidaturas']['total'] += 1;

                $estatisticas["noite"]['candidaturas']['total'] = ++$turnos["qtd_m"];

                if ($item->sexo == 'M')
                  $estatisticas["noite"]['candidaturas']['m'] += 1;

                if ($item->sexo == 'F')
                  $estatisticas["noite"]['candidaturas']['f'] += 1;


                // exames
                if (!$grades->isEmpty()) {
                  $estatisticas["noite"]['exames']['total'] += 1;

                  if ($item->sexo == 'M')
                    $estatisticas["noite"]['exames']['m'] += 1;

                  if ($item->sexo == 'F')
                    $estatisticas["noite"]['exames']['f'] += 1;

                  //admitidos
                  if ($resultado >= 10) {
                    $estatisticas["noite"]['admitidos']['total'] += 1;

                    if ($item->sexo == 'M')

                      $estatisticas["noite"]['admitidos']['m'] += 1;

                    if ($item->sexo == 'F')
                      $estatisticas["noite"]['admitidos']['f'] += 1;


                    // matriculados
                    if (!$matriculado->isEmpty()) {

                      $estatisticas["noite"]['matriculados']['total'] += 1;

                      if ($item->sexo == 'M')

                        $estatisticas["noite"]['matriculados']['m'] += 1;

                      if ($item->sexo == 'F')
                        $estatisticas["noite"]['matriculados']['f'] += 1;
                    }


                  }
                  //reprovados
                  else {
                    $estatisticas["noite"]['reprovados']['total'] += 1;

                    if ($item->sexo == 'M')

                      $estatisticas["noite"]['reprovados']['m'] += 1;

                    if ($item->sexo == 'F')
                      $estatisticas["noite"]['reprovados']['f'] += 1;
                  }
                }




                // ausentes
                else {
                  $estatisticas["noite"]['ausentes']['total'] += 1;

                  if ($item->sexo == 'M')
                    $estatisticas["noite"]['ausentes']['m'] += 1;

                  if ($item->sexo == 'F')
                    $estatisticas["noite"]['ausentes']['f'] += 1;

                }
              }
            }
          }
        }

        return $estatisticas;
      });




      $emolumentos = ["total" => 0, "pending" => 0, "total_money" => 0, "espera_money" => 0];

      foreach ($all_emolumentos as $item) {

        if (in_array($item->usuario_id, $array_candidates)) {
          if ($item->state == "total") {
            ++$emolumentos["total"];
            $emolumentos["total_money"] += $item->valor;
          }
          if ($item->state == "pending") {
            ++$emolumentos["pending"];
            $emolumentos["espera_money"] += $item->valor;
          }
        }
      }




      $staff = collect($model)->groupBy("us_created_by")->map(function ($candidato) {

        $estatisticas = ["inscricao" => 0];
        $p_total = 0;
        foreach ($candidato as $item) {

          $estatisticas["inscricao"] = ++$p_total;
        }

        return $estatisticas;
      });

      $datas = collect($model)->groupBy("criado_a")->map(function ($candidato) {
        $estatisticas = ["inscricao" => 0];
        $p_total = 0;
        foreach ($candidato as $item) {

          $estatisticas["inscricao"] = ++$p_total;
        }

        return $estatisticas;
      });


      $new_datas = [];

      $dat = 0;
      foreach ($datas as $key => $item) {

        $new_datas[] = ["dia" => explode(" ", $key)[0], "candidatos" => $item["inscricao"]];
      }

      $datas_inscricao = collect($new_datas)->sortBy('dia')->groupBy("dia")->map(function ($dias) {
        $inscricao = 0;
        foreach ($dias as $key => $item) {
          $inscricao = $inscricao + $item["candidatos"];
        }
        return $inscricao;
      });

      $lectiveFase = DB::table('lective_candidate')->find($request->fase);

      $vagas = DB::table('anuncio_vagas  as vaga')
        ->join('courses as c', 'c.id', '=', 'vaga.course_id')
        ->leftjoin('department_translations as dpt', 'dpt.departments_id', '=', 'c.departments_id')
        ->leftJoin('lective_candidate as lc', 'lc.id', '=', 'vaga.id_fase')
        ->leftJoin('courses_translations as ct', function ($join) {
          $join->on('ct.courses_id', '=', 'c.id');
          $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
          $join->on('ct.active', '=', DB::raw(true));
        })
        ->where([
          ['vaga.lective_year', '=', $anoletivo],
          ['vaga.id_fase', '=', $request->fase],
          ['vaga.deleted_at', '=', null]
        ])
        ->where([
          ['dpt.language_id', '=', 1],
          ['dpt.active', '=', 1],
        ])
        ->select([
          'dpt.display_name as departamento',
          'c.departments_id as departamento_id',
          'vaga.id as id_vaga',
          'ct.display_name',
          'ct.abbreviation',
          'ct.courses_id',
          'vaga.manha as manha',
          'vaga.tarde as tarde',
          'vaga.noite as noite',
          'vaga.lective_year as id_ano_lectivo',
          'lc.fase'
        ])
        ->orderBy('ct.display_name')
        ->get();

      $vagas = $vagas->groupBy('departamento');
      
      // view("Grades::exame.list_candidate")->with($data);
      $institution = Institution::latest()->first();

      $cordenador = DB::table('user_parameters as up')
        ->join("institutions", "institutions.vice_director_academica", "up.users_id")
        ->where("up.parameters_id", "1")
        ->select(["up.value"])
        ->first();


        $cordenador = isset($cordenador->value) ? ($cordenador->value) : "";

        $titulo_documento = "Relatório: Candidaturas";
        $anoLectivo_documento = "Ano ACADÊMICO: ";
        $documentoGerado_documento = "Documento gerado a";
        $documentoCode_documento = 5;
        $logotipo = "https://" . $_SERVER['HTTP_HOST'] . "/instituicao-arquivo/" . $institution->logotipo;
        $date_generated = date("Y/m/d");
        
        // Retornando a view ao invés de gerar PDF
        return view(
            "Users::candidate.pdf-relatorios-new",
            compact(
                'vagas',
                'cordenador',
                'lectiveFase',
                'lectiveYears',
                'institution',
                'titulo_documento',
                'anoLectivo_documento',
                'documentoGerado_documento',
                'documentoCode_documento',
                'date_generated',
                'twoCourse',
                'twoCourseUsers',
                'logotipo',
                'candidatos',
                'todos_candidatos',
                'staff',
                'datas_inscricao',
                'emolumentos'
            )
        );

    } catch (Exception $e) {
      dd($e);
      ;
    }
  }

  private function pre_matricula_confirma_emolumento($lectiveYearSelected)
  {

    $confirm = EmolumentCodevLective("confirm", $lectiveYearSelected)->first();
    $Prematricula = EmolumentCodevLective("p_matricula", $lectiveYearSelected)->first();
    $emolumentos = [];

    if ($confirm != null) {
      $emolumentos[] = $confirm->id_emolumento;
    }
    if ($Prematricula != null) {
      $emolumentos[] = $Prematricula->id_emolumento;
    }
    return $emolumentos;


  }
  public function relatoriosPDFGlobal($anoletivo)
  {
    try {
      if (isset($anoletivo)) {
        $lectiveYear = DB::table('lective_years')
          ->where('id', $anoletivo)
          ->first();
      }
      $lectiveCandidate = DB::table('lective_candidate')
        ->where('id_years', $lectiveYear->id)
        ->where('fase', 1)
        ->first();

      $cursos = $this->candidateUtil->modelQueryTwoCourseGet($lectiveYear, null);

      $all_emolumentos = $this->candidateUtil->cursoQueryGet($lectiveYear, $lectiveCandidate->id);
      $model = $this->candidateUtil->modelQueryGetGlobal($lectiveYear);
      // return $all_emolumentos;
      $twoCourse = [];

      foreach ($cursos as $item) {
        if ($item->state == 'total') {
          if (isset($twoCourse[$item->usuario_id])) {
            $twoCourse[$item->usuario_id]++;
          } else {
            $twoCourse[$item->usuario_id] = 1;
          }
        }
      }

      $twoCourseUsers = array_filter($twoCourse, function ($count) {
        return $count == 2;
      });
      $twoCourseUsers = count($twoCourseUsers);


      $array_candidates = array();

      foreach ($model as $candidates) {
        array_push($array_candidates, $candidates->id);
      }




      // return $all_emolumentos;
      // return $model;

      $lectiveYears = LectiveYear::with(['currentTranslation'])
        ->where('id', $anoletivo)
        ->select('*')
        ->get();

      $emolumentos_vagas = DB::table('articles as art')
        ->select([
          DB::raw('DISTINCT ar.id as articles_req'),
          "art.id as articles",
          "ar.discipline_id as discipline",
          "curso.id as course",
          "ct.display_name as nome_curso",
          "ar.user_id as usuario_id",
          "ar.status as state",
          "ar.base_value as valor",
          "usuario.name as usuario",
          'user_classes.class_id as turma',
          'turmas.id as turma_nova',
          'turmas.code as turma',
          'turmas.schedule_type_id as turno',
          'u_p1.value as sexo',
          'curso.departments_id as departamento_id' // Adicionando o ID do departamento
        ])
        ->join('article_requests as ar', 'art.id', '=', 'ar.article_id')
        ->join('disciplines as disciplina', 'disciplina.id', '=', 'ar.discipline_id')
        ->join('courses as curso', 'disciplina.courses_id', '=', 'curso.id')
        ->join('courses_translations as ct', 'ct.courses_id', '=', 'curso.id')
        ->join('users as usuario', 'ar.user_id', '=', 'usuario.id')
        ->join('user_candidate as uca', 'uca.user_id', '=', 'usuario.id')
        ->join('user_parameters as u_p1', function ($join) {
          $join->on('usuario.id', '=', 'u_p1.users_id')
            ->where('u_p1.parameters_id', 2);
        })
        // Alterando para inner join se precisamos garantir que o aluno tem turma
        ->join('user_classes', 'user_classes.user_id', '=', 'ar.user_id')
        ->join('classes as turmas', 'turmas.id', '=', 'user_classes.class_id')
        ->where("art.id_code_dev", 6)
        ->where("ct.active", 1)
        ->where("ar.status", "total")
        ->where("uca.year", $anoletivo)
        ->where('usuario.name', "!=", "")
        ->whereNull('ar.deleted_at')
        ->whereNull('disciplina.deleted_at')
        ->whereNull('ct.deleted_at')
        ->whereNull('art.deleted_at')
        ->whereNull('curso.deleted_at')
        ->whereNull('usuario.deleted_at')
        ->where('ar.discipline_id', "!=", null)
        ->whereBetween('ar.created_at', [$lectiveYear->start_date, $lectiveYear->end_date])
        ->select([
          "art.id as articles",
          "ar.id as articles_req",
          "ar.discipline_id as discipline",
          "curso.id as course",
          "ct.display_name as nome_curso",
          "ar.user_id as usuario_id",
          "ar.status as state",
          "ar.base_value as valor",
          "usuario.name as usuario",
          'user_classes.class_id as turma',
          'turmas.id as turma_nova',
          'turmas.code as turma',
          'turmas.schedule_type_id as turno',
          'u_p1.value as sexo'
        ])
        ->orderBy('ar.id', 'desc')
        ->get();



      $last_cand = DB::table('user_candidate')
        ->where("code", "like", "%CE%")
        ->where("year", $lectiveYear->id)
        ->orderBy("code", "desc")
        ->first();

      if (!isset($last_cand->code)) {
        Toastr::warning("A forLEARN não detectou candidatos a estudantes nesta fase", 'Nenhum candidato');
        return redirect()->back();
      }

      // Se for a nova fase


      $last_cand = DB::table('user_candidate')
        ->where("code", "like", "%CE%")
        ->where("year", $lectiveYear->id)
        ->orderBy("code", "desc")
        ->count();

      $todos_candidatos = $last_cand;



      $emolumentos_vagas = collect($emolumentos_vagas)->map(function ($item) {
        if ($item->sexo == "Feminino" || $item->sexo == 125 || $item->sexo == "feminino" || $item->sexo == "f" || $item->sexo == "F")
          $item->sexo = 'F';

        if ($item->sexo == "Masculino" || $item->sexo == 124 || $item->sexo == "masculino" || $item->sexo == "m" || $item->sexo == "M")
          $item->sexo = 'M';

        return $item;
      });



      $candidatos = collect($emolumentos_vagas)->groupBy("course")->map(function ($curso) use ($lectiveYear) {
        $estatisticas = [

          "money" => 0,
          'manha' => [
            'candidaturas' => [
              'total' => 0,
              'm' => 0,
              'f' => 0
            ],
            'exames' => [
              'total' => 0,
              'm' => 0,
              'f' => 0
            ],
            'ausentes' => [
              'total' => 0,
              'm' => 0,
              'f' => 0
            ],
            'reprovados' => [
              'total' => 0,
              'm' => 0,
              'f' => 0
            ],
            'admitidos' => [
              'total' => 0,
              'm' => 0,
              'f' => 0
            ],
            'matriculados' => [
              'total' => 0,
              'm' => 0,
              'f' => 0
            ],
          ],

          'tarde' => [
            'candidaturas' => [
              'total' => 0,
              'm' => 0,
              'f' => 0
            ],
            'exames' => [
              'total' => 0,
              'm' => 0,
              'f' => 0
            ],
            'ausentes' => [
              'total' => 0,
              'm' => 0,
              'f' => 0
            ],
            'reprovados' => [
              'total' => 0,
              'm' => 0,
              'f' => 0
            ],
            'admitidos' => [
              'total' => 0,
              'm' => 0,
              'f' => 0
            ],
            'matriculados' => [
              'total' => 0,
              'm' => 0,
              'f' => 0
            ],
          ],

          'noite' => [
            'candidaturas' => [
              'total' => 0,
              'm' => 0,
              'f' => 0
            ],
            'exames' => [
              'total' => 0,
              'm' => 0,
              'f' => 0
            ],
            'ausentes' => [
              'total' => 0,
              'm' => 0,
              'f' => 0
            ],
            'reprovados' => [
              'total' => 0,
              'm' => 0,
              'f' => 0
            ],
            'admitidos' => [
              'total' => 0,
              'm' => 0,
              'f' => 0
            ],
            'matriculados' => [
              'total' => 0,
              'm' => 0,
              'f' => 0
            ],
          ]

        ];

        // m = 6, t = 7, n = 3
        $turnos = ["qtd_m" => 0, "qtd_t" => 0, "qtd_n" => 0];

        foreach ($curso as $item) {

          if (isset($item->state) && $item->state == "total") {

            $estatisticas["money"] = $item->valor;
            if (isset($item->turno)) {

              $grades = DB::table('grades as g')
                ->where('student_id', $item->usuario_id)
                ->whereBetween('g.updated_at', [$lectiveYear->start_date, $lectiveYear->end_date])
                ->whereNull('g.deleted_by')
                ->whereNull('g.deleted_at')
                ->join('disciplines as d', 'g.discipline_id', '=', 'd.id')
                ->leftJoin('disciplines_translations as dt', function ($join) {
                  $join->on('dt.discipline_id', '=', 'd.id');
                  $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                  $join->on('dt.active', '=', DB::raw(true));
                })
                ->where('d.discipline_profiles_id', 8)
                ->select(['g.value as nota', 'dt.display_name as disciplina', 'd.percentage as percentagem'])
                ->get();



              if (isset($grades[0]->nota, $grades[1]->nota)) {
                $resultado = round(($grades[0]->nota * ($grades[0]->percentagem / 100)) + ($grades[1]->nota * ($grades[1]->percentagem / 100)));
              } else if (isset($grades[0]->nota) && !isset($grades[1]->nota)) {
                $resultado = $grades[0]->nota;
              } else {
                $resultado = round((($grades[0]->nota ?? 0) + ($grades[1]->nota ?? 0)) / 2);
              }

              $emolumento_confirma_prematricula = $this->pre_matricula_confirma_emolumento($lectiveYear->id);


              $matriculado = DB::table('matriculations')
                ->join('users as u0', 'u0.id', '=', 'matriculations.user_id')
                ->join('article_requests as art_requests', 'art_requests.user_id', '=', 'matriculations.user_id')
                ->whereIn('art_requests.article_id', $emolumento_confirma_prematricula)
                ->where('matriculations.lective_year', $lectiveYear->id)
                ->where('art_requests.status', 'total')
                ->where('matriculations.user_id', $item->usuario_id)
                ->distinct('matriculations.id')
                ->get();

              if ($item->turno == 11) {
                $estatisticas["manha"]['candidaturas']['total'] += 1;

                if ($item->sexo == 'M')
                  $estatisticas["manha"]['candidaturas']['m'] += 1;

                if ($item->sexo == 'F')
                  $estatisticas["manha"]['candidaturas']['f'] += 1;

                // exames
                if (!$grades->isEmpty()) {

                  $estatisticas["manha"]['exames']['total'] += 1;

                  if ($item->sexo == 'M')
                    $estatisticas["manha"]['exames']['m'] += 1;

                  if ($item->sexo == 'F')
                    $estatisticas["manha"]['exames']['f'] += 1;

                  //admitidos
                  if ($resultado >= 10) {
                    $estatisticas["manha"]['admitidos']['total'] += 1;

                    if ($item->sexo == 'M')
                      $estatisticas["manha"]['admitidos']['m'] += 1;

                    if ($item->sexo == 'F')
                      $estatisticas["manha"]['admitidos']['f'] += 1;


                    // matriculados
                    if (!$matriculado->isEmpty()) {

                      $estatisticas["manha"]['matriculados']['total'] += 1;

                      if ($item->sexo == 'M')
                        $estatisticas["manha"]['matriculados']['m'] += 1;

                      if ($item->sexo == 'F')
                        $estatisticas["manha"]['matriculados']['f'] += 1;
                    }


                  }
                  //reprovados
                  else {
                    $estatisticas["manha"]['reprovados']['total'] += 1;

                    if ($item->sexo == 'M')
                      $estatisticas["manha"]['reprovados']['m'] += 1;

                    if ($item->sexo == 'F')
                      $estatisticas["manha"]['reprovados']['f'] += 1;
                  }
                }

                // ausentes
                else {

                  $estatisticas["manha"]['ausentes']['total'] += 1;

                  if ($item->sexo == 'M')
                    $estatisticas["manha"]['ausentes']['m'] += 1;

                  if ($item->sexo == 'F')
                    $estatisticas["manha"]['ausentes']['f'] += 1;

                }


              }
              if ($item->turno == 12) {

                $estatisticas["tarde"]['candidaturas']['total'] += 1;

                if ($item->sexo == 'M')
                  $estatisticas["tarde"]['candidaturas']['m'] += 1;

                if ($item->sexo == 'F')
                  $estatisticas["tarde"]['candidaturas']['f'] += 1;


                // exames
                if (!$grades->isEmpty()) {
                  $estatisticas["tarde"]['exames']['total'] += 1;

                  if ($item->sexo == 'M')
                    $estatisticas["tarde"]['exames']['m'] += 1;

                  if ($item->sexo == 'F')
                    $estatisticas["tarde"]['exames']['f'] += 1;

                  //admitidos
                  if ($resultado >= 10) {
                    $estatisticas["tarde"]['admitidos']['total'] += 1;

                    if ($item->sexo == 'M')

                      $estatisticas["tarde"]['admitidos']['m'] += 1;

                    if ($item->sexo == 'F')
                      $estatisticas["tarde"]['admitidos']['f'] += 1;


                    // matriculados
                    if (!$matriculado->isEmpty()) {

                      $estatisticas["tarde"]['matriculados']['total'] += 1;

                      if ($item->sexo == 'M')

                        $estatisticas["tarde"]['matriculados']['m'] += 1;

                      if ($item->sexo == 'F')
                        $estatisticas["tarde"]['matriculados']['f'] += 1;
                    }


                  }
                  //reprovados
                  else {
                    $estatisticas["tarde"]['reprovados']['total'] += 1;

                    if ($item->sexo == 'M')

                      $estatisticas["tarde"]['reprovados']['m'] += 1;

                    if ($item->sexo == 'F')
                      $estatisticas["tarde"]['reprovados']['f'] += 1;
                  }
                }




                // ausentes
                else {
                  $estatisticas["tarde"]['ausentes']['total'] += 1;

                  if ($item->sexo == 'M')
                    $estatisticas["tarde"]['ausentes']['m'] += 1;

                  if ($item->sexo == 'F')
                    $estatisticas["tarde"]['ausentes']['f'] += 1;

                }
              }
              if ($item->turno == 13) {

                $estatisticas["noite"]['candidaturas']['total'] += 1;

                if ($item->sexo == 'M')
                  $estatisticas["noite"]['candidaturas']['m'] += 1;

                if ($item->sexo == 'F')
                  $estatisticas["noite"]['candidaturas']['f'] += 1;


                // exames
                if (!$grades->isEmpty()) {
                  $estatisticas["noite"]['exames']['total'] += 1;

                  if ($item->sexo == 'M')
                    $estatisticas["noite"]['exames']['m'] += 1;

                  if ($item->sexo == 'F')
                    $estatisticas["noite"]['exames']['f'] += 1;

                  //admitidos
                  if ($resultado >= 10) {
                    $estatisticas["noite"]['admitidos']['total'] += 1;

                    if ($item->sexo == 'M')

                      $estatisticas["noite"]['admitidos']['m'] += 1;

                    if ($item->sexo == 'F')
                      $estatisticas["noite"]['admitidos']['f'] += 1;


                    // matriculados
                    if (!$matriculado->isEmpty()) {

                      $estatisticas["noite"]['matriculados']['total'] += 1;

                      if ($item->sexo == 'M')

                        $estatisticas["noite"]['matriculados']['m'] += 1;

                      if ($item->sexo == 'F')
                        $estatisticas["noite"]['matriculados']['f'] += 1;
                    }


                  }
                  //reprovados
                  else {
                    $estatisticas["noite"]['reprovados']['total'] += 1;

                    if ($item->sexo == 'M')

                      $estatisticas["noite"]['reprovados']['m'] += 1;

                    if ($item->sexo == 'F')
                      $estatisticas["noite"]['reprovados']['f'] += 1;
                  }
                }




                // ausentes
                else {
                  $estatisticas["noite"]['ausentes']['total'] += 1;

                  if ($item->sexo == 'M')
                    $estatisticas["noite"]['ausentes']['m'] += 1;

                  if ($item->sexo == 'F')
                    $estatisticas["noite"]['ausentes']['f'] += 1;

                }
              }
            }
          }
        }

        return $estatisticas;
      });


      $emolumentos = ["total" => 0, "pending" => 0, "total_money" => 0, "espera_money" => 0];

      foreach ($all_emolumentos as $item) {

        if (in_array($item->usuario_id, $array_candidates)) {
          if ($item->state == "total") {
            ++$emolumentos["total"];
            $emolumentos["total_money"] += $item->valor;
          }
          if ($item->state == "pending") {
            ++$emolumentos["pending"];
            $emolumentos["espera_money"] += $item->valor;
          }
        }
      }




      $staff = collect($model)->groupBy("us_created_by")->map(function ($candidato) {

        $estatisticas = ["inscricao" => 0];
        $p_total = 0;
        foreach ($candidato as $item) {

          $estatisticas["inscricao"] = ++$p_total;
        }

        return $estatisticas;
      });

      $datas = collect($model)->groupBy("criado_a")->map(function ($candidato) {
        $estatisticas = ["inscricao" => 0];
        $p_total = 0;
        foreach ($candidato as $item) {

          $estatisticas["inscricao"] = ++$p_total;
        }

        return $estatisticas;
      });


      $new_datas = [];

      $dat = 0;
      foreach ($datas as $key => $item) {

        $new_datas[] = ["dia" => explode(" ", $key)[0], "candidatos" => $item["inscricao"]];
      }

      $datas_inscricao = collect($new_datas)->sortBy('dia')->groupBy("dia")->map(function ($dias) {
        $inscricao = 0;
        foreach ($dias as $key => $item) {
          $inscricao = $inscricao + $item["candidatos"];
        }
        return $inscricao;
      });





      $fase = DB::table('lective_candidate as lc')
        ->where('lc.id_years', $anoletivo)
        ->where('lc.fase', 1)
        ->select('lc.id')
        ->first();


      $vagas = DB::table('anuncio_vagas as vaga')
        ->join('courses as c', 'c.id', '=', 'vaga.course_id')
        ->leftjoin('department_translations as dpt', 'dpt.departments_id', '=', 'c.departments_id')
        ->leftJoin('lective_candidate as lc', 'lc.id', '=', 'vaga.id_fase')
        ->leftJoin('courses_translations as ct', function ($join) {
          $join->on('ct.courses_id', '=', 'c.id');
          $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
          $join->on('ct.active', '=', DB::raw(true));
        })
        ->where([
          ['vaga.lective_year', '=', $anoletivo],
          ['vaga.id_fase', '=', $fase->id],
          ['vaga.deleted_at', '=', null]
        ])
        ->where([
          ['dpt.language_id', '=', 1],
          ['dpt.active', '=', 1],
        ])
        ->select([
          'dpt.display_name as departamento',
          'c.departments_id as departamento_id',
          'ct.display_name',
          'ct.courses_id',
          'ct.abbreviation',
          DB::raw('SUM(vaga.manha) as manha'),
          DB::raw('SUM(vaga.tarde) as tarde'),
          DB::raw('SUM(vaga.noite) as noite'),
          'vaga.lective_year as id_ano_lectivo',
          'lc.fase'
        ])
        ->groupBy('dpt.display_name', 'c.departments_id', 'ct.display_name', 'ct.courses_id', 'vaga.lective_year')
        ->orderBy('ct.display_name')
        ->get();

      $vagas = $vagas->groupBy('departamento');

      // view("Grades::exame.list_candidate")->with($data);
      $institution = Institution::latest()->first();

      $cordenador = DB::table('user_parameters as up')
        ->join("institutions", "institutions.vice_director_academica", "up.users_id")
        ->where("up.parameters_id", "1")
        ->select(["up.value"])
        ->first();


      $cordenador = isset($cordenador->value) ? ($cordenador->value) : "";
      $titulo_documento = "Relatório: Candidaturas";
      $anoLectivo_documento = "Ano ACADÊMICO: ";
      $documentoGerado_documento = "Documento gerado a";
      $documentoCode_documento = 5;
      $logotipo = "https://" . $_SERVER['HTTP_HOST'] . "/instituicao-arquivo/" . $institution->logotipo;
      $date_generated = date("Y/m/d");
      $pdf = PDF::loadView(
        "Users::candidate.pdf-relatorios-global",
        compact(
          'vagas',
          'cordenador',
          'lectiveYears',
          'institution',
          'titulo_documento',
          'anoLectivo_documento',
          'documentoGerado_documento',
          'documentoCode_documento',
          'date_generated',
          'twoCourse',
          'twoCourseUsers',
          'logotipo',
          'candidatos',
          'todos_candidatos',
          'staff',
          'datas_inscricao',
          'emolumentos'
        )
      );

      $pdf->setOption('margin-top', '2mm');
      $pdf->setOption('margin-left', '2mm');
      $pdf->setOption('margin-bottom', '15mm');
      $pdf->setOption('margin-right', '2mm');
      $pdf->setOption('enable-javascript', true);
      $pdf->setOption('debug-javascript', true);
      $pdf->setOption('javascript-delay', 1000);
      $pdf->setOption('enable-smart-shrinking', true);
      $pdf->setOption('no-stop-slow-scripts', true);
      $pdf->setPaper('a4', 'portrait');

      $pdf_name = "Relatório_candidaturas_" . $lectiveYears[0]->currentTranslation->display_name . "(" . "Global" . "ª Fase)";

      // $footer_html = view()->make('Users::users.partials.pdf_footer', compact('institution'))->render();
      $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
      $pdf->setOption('footer-html', $footer_html);
      return $pdf->stream($pdf_name . '.pdf');

    } catch (Exception $e) {
      dd($e);
    }
  }

  public function generatePDFForCandidate($id, Request $request)
  {

    // Find
    $user = User::whereId($id)->with([
      'roles' => function ($q) {
        $q->with([
          'currentTranslation'
        ]);
      },
      'parameters' => function ($q) {
        $q->with([
          'currentTranslation',
          'groups'
        ]);
      },
      'courses',
      'disciplines',
      'classes' => function ($q) {
        $q->with([
          'room' => function ($q) {
            $q->with([
              'currentTranslation'
            ]);
          }
        ]);
      }
    ])->firstOrFail();
    $courses = Course::with([
      'currentTranslation'
    ])->get();


    // Options
    $measurement = 'cm';
    $font_measurement = 'cm';
    // $options = [
    //     'columns_per_group' => $request->get('columns-per-group'),
    //     'extension' => '.pdf',
    //     'filename' => $user->id,
    //     'margins' => [
    //         'top' => $request->get('margin-top') . $measurement,
    //         'bottom' => $request->get('margin-bottom') . $measurement,
    //         'left' => $request->get('margin-left') . $measurement,
    //         'right' => $request->get('margin-right') . $measurement,
    //     ],
    //     'orientation' => $request->get('orientation'),
    //     'paper' => $request->get('paper-size'),
    //     'font-size' => $request->get('font-size') . $font_measurement,
    // ];

    $options = [
      'columns_per_group' => "7",
      'extension' => '.pdf',
      'filename' => $user->id,
      'margins' => [
        'top' => '2mm',
        'bottom' => '12mm',
        'left' => '2mm',
        'right' => '2mm',
      ],

      'orientation' => "portrait",
      'paper' => "a4",
      'font-size' => "20" . $font_measurement,
    ];


    $parameter_groups = ParameterGroup::with([
      'currentTranslation',
      'roles',
      'parameters' => function ($q) {
        $q->with([
          'currentTranslation',
          'roles',
          'options' => function ($q) {
            $q->with([
              'currentTranslation',
              'relatedParametersRecursive'
            ]);
          }
        ]);
      }
    ])->orderBy('order')->get();

    $data = [
      'action' => 'print',
      'user' => $user,
      'parameter_groups' => $parameter_groups,
      'date_generated' => date('d/m/Y'),
      'include_attachments' => $request->get('include-attachments'),
      'courses' => $courses,
      'options' => $options
    ];

    if ($request->get('view')) {
      return view('Users::users.pdf', $data);
    }

    //return view('Users::users.pdf', $data);
    $pdf = SnappyPDF::loadView('Users::users.pdf', $data);

    $pdf->setOption('margin-top', $options['margins']['top']);
    $pdf->setOption('margin-left', $options['margins']['left']);
    $pdf->setOption('margin-bottom', $options['margins']['bottom']);
    $pdf->setOption('margin-right', $options['margins']['right']);
    $pdf->setOption('enable-javascript', true);
    $pdf->setOption('debug-javascript', true);
    $pdf->setOption('javascript-delay', 1000);
    $pdf->setOption('enable-smart-shrinking', true);
    $pdf->setOption('no-stop-slow-scripts', true);

    // Footer
    $footer_html = view()->make('Users::users.partials.pdf_footer')->render();
    $pdf->setOption('footer-html', $footer_html);

    $pdf->setPaper($options['paper'], $options['orientation']);

    // If we want to add attachments
    if ($request->get('include-attachments')) {
      $temp_filename = $options['filename'] . $options['extension'];

      // Create temporary pdf
      Storage::put($temp_filename, $pdf->output());

      // Merge all pdfs
      $merger = PdfMerger::init();
      $merger->addPDF(instituicao-arquivo_path('app/public/' . $temp_filename));

      // Search all attachments
      $attachments = $user->parameters->filter(function ($item) {
        return in_array($item->type, ['file_pdf'], true);
      });

      // Merge all attachments
      if (count($attachments) > 0) {
        foreach ($attachments as $attachment) {
          $finfo = finfo_open(FILEINFO_MIME_TYPE);

          $allowed_pdf_mime_types = ['application/pdf'];
          $allowed_image_mime_types = ['image/jpeg', 'image/gif', 'image/png', 'image/bmp', 'image/svg+xml'];
          try {
            $filename = storage_path('app/public/attachment/' . $attachment->pivot->value);
            $mime_type = finfo_file($finfo, $filename);

            // If its PDF
            if (in_array($mime_type, $allowed_pdf_mime_types, true)) {
              $merger->addPDF($filename);
            } elseif (in_array($mime_type, $allowed_image_mime_types, true)) {
              //TODO: convert image to pdf
            }
          } catch (Exception | Throwable $e) {
            //dump($e);
          }
        }
      }

      $merger->merge();

      Storage::delete($temp_filename);

      return $merger->save($temp_filename, 'browser');
    }

    return $pdf->stream($options['filename'] . $options['extension']);
  }

  public function generatePDFForCandidateAfterUpdate($id)
  {

    // Find
    $user = User::whereId($id)->with([
      'roles' => function ($q) {
        $q->with([
          'currentTranslation'
        ]);
      },
      'parameters' => function ($q) {
        $q->with([
          'currentTranslation',
          'groups'
        ]);
      },
      'courses',
      'disciplines',
      'classes' => function ($q) {
        $q->with([
          'room' => function ($q) {
            $q->with([
              'currentTranslation'
            ]);
          }
        ]);
      }
    ])->firstOrFail();
    $courses = Course::with([
      'currentTranslation'
    ])->get();


    // Options
    $measurement = 'cm';
    $font_measurement = 'px';


    $options = [
      'columns_per_group' => "7",
      'extension' => '.pdf',
      'filename' => $user->id,
      'margins' => [
        'top' => '2mm',
        'bottom' => '12mm',
        'left' => '2mm',
        'right' => '2mm',
      ],

      'orientation' => "portrait",
      'paper' => "a4",
      'font-size' => "12" . $font_measurement,
    ];


    $parameter_groups = ParameterGroup::with([
      'currentTranslation',
      'roles',
      'parameters' => function ($q) {
        $q->with([
          'currentTranslation',
          'roles',
          'options' => function ($q) {
            $q->with([
              'currentTranslation',
              'relatedParametersRecursive'
            ]);
          }
        ]);
      }
    ])->orderBy('order')->get();

    $data = [
      'action' => 'print',
      'user' => $user,
      'parameter_groups' => $parameter_groups,
      'date_generated' => date('d/m/Y'),
      'include_attachments' => 1,
      'courses' => $courses,
      'options' => $options
    ];



    //return view('Users::users.pdf', $data);
    $pdf = SnappyPDF::loadView('Users::users.pdf', $data);

    $pdf->setOption('margin-top', $options['margins']['top']);
    $pdf->setOption('margin-left', $options['margins']['left']);
    $pdf->setOption('margin-bottom', $options['margins']['bottom']);
    $pdf->setOption('margin-right', $options['margins']['right']);
    $pdf->setOption('enable-javascript', true);
    $pdf->setOption('debug-javascript', true);
    $pdf->setOption('javascript-delay', 1000);
    $pdf->setOption('enable-smart-shrinking', true);
    $pdf->setOption('no-stop-slow-scripts', true);

    // Footer
    $footer_html = view()->make('Users::users.partials.pdf_footer')->render();
    $pdf->setOption('footer-html', $footer_html);

    $pdf->setPaper($options['paper'], $options['orientation']);

    // If we want to add attachments

    $temp_filename = $options['filename'] . $options['extension'];

    // Create temporary pdf
    Storage::put($temp_filename, $pdf->output());

    // Merge all pdfs
    $merger = PdfMerger::init();
    $merger->addPDF(storage_path('app/public/' . $temp_filename));

    // Search all attachments
    $attachments = $user->parameters->filter(function ($item) {
      return in_array($item->type, ['file_pdf'], true);
    });
    // Merge all attachments
    if (count($attachments) > 0) {
      foreach ($attachments as $attachment) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $allowed_pdf_mime_types = ['application/pdf'];
        $allowed_image_mime_types = ['image/jpeg', 'image/gif', 'image/png', 'image/bmp', 'image/svg+xml'];
        try {
          $filename = storage_path('app/public/attachment/' . $attachment->pivot->value);
          $mime_type = finfo_file($finfo, $filename);

          // If its PDF
          if (in_array($mime_type, $allowed_pdf_mime_types, true)) {
            $merger->addPDF($filename);
          } elseif (in_array($mime_type, $allowed_image_mime_types, true)) {
            //TODO: convert image to pdf
          }
        } catch (Exception | Throwable $e) {
          //dump($e);
        }
      }


      $merger->merge();

      Storage::delete($temp_filename);

      return $merger->save($temp_filename, 'browser');
    }

    return $pdf->stream($options['filename'] . $options['extension']);
  }






  public function coursesDisciplinesAjax(Request $request)
  {
    //return response()->json(['disciplines' => "oola"]);
    try {
      $courses = $request->get('courses');
      $user = User::whereId($request->get('user'))->firstOrFail();
      $disciplines = disciplinesSelectForCandidates($courses);
      $classes = classesSelectForCandidates($courses);
      return response()->json(['disciplines' => $disciplines, 'classes' => $classes], 200);
    } catch (Exception | Throwable $e) {
      Log::error($e);
      return response()->json($e->getMessage(), 500);
    }
  }











  public function convertToEmail($name)
  {
    $pieces = explode(",", $name);
    //count the quantity of name to use in email
    $lenght = strlen($pieces[0]);

    //return first and last name
    $nameLenght = count($pieces);
    $firstAndLastName = $pieces[0] . " " . $pieces[$nameLenght - 1];

    //checar caracteres com acentuacao
    //IMPORTANTE

    $specialCharacters = [
      "á" => "a",
      "à" => "a",
      "â" => "a",
      "ã" => "a",
      "Á" => "A",
      "À" => "A",
      "Â" => "A",
      "Ã" => "A",
      "È" => "E",
      "É" => "E",
      "è" => "e",
      "é" => "e",
      "Ê" => "E",
      "ê" => "e",
      "Ì" => "I",
      "Í" => "I",
      "ì" => "i",
      "í" => "i",
      "Î" => "I",
      "î" => "i",
      "ó" => "o",
      "ò" => "o",
      "Ó" => "O",
      "Ò" => "O",
      "Ô" => "O",
      "Õ" => "O",
      "õ" => "o",
      "ô" => "o",
      "Ù" => "U",
      "Ú" => "U",
      "ù" => "u",
      "ú" => "u",
      "û" => "u",
      "Û" => "U"
    ];


    //Ultimo email  da cadeia de palavras
    $lastEmail = strtolower(strtr($pieces[0], $specialCharacters) . "." . strtr($pieces[$nameLenght - 1], $specialCharacters) . EnumVariable::$CONVERT_TO_EMAIL);


    for ($i = 0; $i <= $lenght; $i++) {
      $letter = strtr($pieces[0], $specialCharacters);
      $lastNameWithoutSpecialCharacters = strtr($pieces[$nameLenght - 1], $specialCharacters);
      $email = strtolower(substr($letter, 0, $i + 1) . "." . $lastNameWithoutSpecialCharacters . EnumVariable::$CONVERT_TO_EMAIL);

      $checkEmail = User::where('users.email', '=', $email)->get();

      if ($checkEmail->isEmpty()) {
        $email = $email;
        break;
      } else if ($lastEmail == $email) {
        //se o email for o último e já existe.
        $checkEmail_Point = User::where('users.email', '=', $email)->get();
        $rand = rand(10, 1000);
        $novoEmail = strtolower(strtr($pieces[0], $specialCharacters) . "." . strtr($pieces[$nameLenght - 1], $specialCharacters) . $rand . EnumVariable::$CONVERT_TO_EMAIL);
        if (!$checkEmail_Point->isEmpty()) {
          $email = $novoEmail;
        }
      }
    }

    $data = ['name' => $firstAndLastName, 'email' => $email];
    return response()->json($data);
  }



  //sedrac e joaquim
  public function getValidationNewNumberBI($valorBi)
  {
    try {

      $getNumberBi = User::join('user_parameters as u_p', function ($join) {
        $join->on('users.id', '=', 'u_p.users_id')
          ->where('u_p.parameters_id', 14);
      })
        ->where('u_p.value', '=', $valorBi)
        ->get();

      return !$getNumberBi->isEmpty() ? response()->json(true) : response()->json(false);
    } catch (Exception | Throwable $e) {
      return $e;
      Log::error($e);
      return response()->json($e->getMessage(), 500);
    }
  }
  public function anoLectivo()
  {
    $lectiveYears = LectiveYear::with(['currentTranslation'])->get();
    $currentData = Carbon::now();
    $lectiveYearSelected = DB::table('lective_years')->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')->first();
    $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
    $ano_lectivo = DB::select("SELECT * FROM lective_years WHERE id = ?", [$lectiveYearSelected])[0];
    //$years = DB::select("SELECT * FROM lective_years where deleted_at is null");
    return view('Users::candidate.ano_lectivo', compact('ano_lectivo', 'lectiveYears', 'lectiveYearSelected'));
  }


  private function actualizarDatasCalendariosPassaram()
  {
    DB::update('UPDATE lective_candidate SET is_termina = ? WHERE now() > data_fim', [1]);
    DB::update('UPDATE lective_candidate SET is_termina = ? WHERE now() <= data_fim', [0]);

    DB::update('UPDATE lective_candidate_calendarie SET is_termina = ? WHERE now() > data_fim', [1]);
    DB::update('UPDATE lective_candidate_calendarie SET is_termina = ? WHERE now() <= data_fim', [0]);
  }

  public function anoLectivoStore(Request $request)
  {
    try {

      $data_start = strtotime($request->data_start);
      $data_end = strtotime($request->data_end);

      if ($data_start > $data_end) {
        Toastr::warning('A data de começo é maior que a data de termino', __('toastr.warning'));
        return redirect()->route('candidate.ano_lectivo');
      }

      $auth = Auth::user()->id;
      $result = DB::insert('INSERT INTO lective_candidate_calendarie(id_years,data_inicio,data_fim,created_by,updated_by,deleted_by) VALUES (?,?,?,?,?,?)', [
        $request->lective_year,
        $request->data_start,
        $request->data_end,
        $auth,
        $auth,
        $auth
      ]);

      $this->actualizarDatasCalendariosPassaram();
      Toastr::success('Calendário foi criado com sucesso', __('toastr.success'));
      return redirect()->route('candidate.ano_lectivo');
    } catch (Exception | Throwable $e) {
      dd($e->getMessage());
      Log::error($e);
      Toastr::error($e->getMessage(), __('toastr.error'));
      return redirect()->back();
    }
  }

  public function validate_ano_candidato($id)
  {

    $this->actualizarDatasCalendariosPassaram();
    $obj = DB::table('lective_candidate_calendarie')->where('id_years', $id)->first();
    if (isset($obj->id)) {
      $create = (object) [
        "status" => 1,
        "body" => $obj
      ];
      return response()->json($create);
    }
    return response()->json((object) [
      "status" => 0,
    ]);
  }

  public function validate_ano_lectivo($id)
  {
    $this->actualizarDatasCalendariosPassaram();
    $obj = DB::table('lective_years as ly')
      ->leftJoin('lective_candidate_calendarie as lc', 'ly.id', '=', 'lc.id_years')
      ->select('ly.id', 'ly.start_date', 'ly.end_date', 'lc.is_termina')
      ->where('ly.id', $id)
      ->first();
    if (isset($obj->id)) {
      $create = (object) [
        "status" => 1,
        "body" => $obj
      ];
      return response()->json($create);
    }
    return response()->json((object) [
      "status" => 0,
    ]);
  }

  public function ajax_list(Request $request)
  {
    try {
      $this->actualizarDatasCalendariosPassaram();
      $model = DB::table('lective_candidate_calendarie as lc')
        ->join('lective_year_translations as lyt', 'lc.id_years', '=', 'lyt.lective_years_id')
        ->where('lyt.active', 1)
        ->whereNull('lyt.deleted_at')
        ->select('lc.id', 'lc.data_inicio', 'lc.data_fim', 'lyt.display_name', 'lc.id_years');
      if (isset($request->year)) {
        $model->where('lc.id_years', $request->year);
      }
      $model = $model->get();
      return Datatables::of($model)
        ->addColumn('actions', function ($item) {
          return view('Users::candidate.datatables.candidato_actions')->with('item', $item);
        })
        ->rawColumns(['actions'])
        ->addIndexColumn()
        ->toJson();
    } catch (Exception | Throwable $e) {
      Log::error($e);
      return response()->json($e->getMessage(), 500);
    }
  }

  public function listCandidatura()
  {
    try {
      $lectiveYears = LectiveYear::with(['currentTranslation'])->get();
      $currentData = Carbon::now();
      $lectiveYearSelected = DB::table('lective_years')->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')->first();
      $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
      return view('Users::candidate.ano_lectivo_list', compact('lectiveYears', 'lectiveYearSelected'));
    } catch (Exception | Throwable $e) {
      Log::error($e);
      Toastr::error($e->getMessage(), __('toastr.error'));
      return redirect()->back();
    }
  }
  public function viewCandidatura($id)
  {
    try {
      $this->actualizarDatasCalendariosPassaram();
      $lectiveYears = LectiveYear::with(['currentTranslation'])->get();
      $currentData = Carbon::now();
      $lectiveYearSelected = DB::table('lective_years')->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')->first();
      $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
      $ano_lectivo = DB::select("SELECT * FROM lective_years WHERE id = ?", [$lectiveYearSelected])[0];
      $action = "GET";

      $ano_candidatura = DB::select("SELECT * FROM lective_candidate_calendarie WHERE id = ?", [$id])[0];
      return view('Users::candidate.ano_lectivo', compact('ano_lectivo', 'lectiveYears', 'lectiveYearSelected', 'action', 'id', 'ano_candidatura'));
    } catch (Exception | Throwable $e) {
      Log::error($e);
      Toastr::error($e->getMessage(), __('toastr.error'));
      return redirect()->back();
    }
  }
  public function editCandidatura($id)
  {
    //dd($id);
    try {
      $this->actualizarDatasCalendariosPassaram();
      $ano_candidatura = DB::select("SELECT * FROM lective_candidate_calendarie WHERE id = ?", [$id])[0];
      $lectiveYears = LectiveYear::with(['currentTranslation'])->get();
      $lectiveYearSelected = DB::table('lective_years')->where('id', $ano_candidatura->id_years)->first();
      $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
      $ano_lectivo = DB::select("SELECT * FROM lective_years WHERE id = ?", [$lectiveYearSelected])[0];
      $action = "PUT";
      return view('Users::candidate.ano_lectivo', compact('ano_lectivo', 'lectiveYears', 'lectiveYearSelected', 'action', 'id', 'ano_candidatura'));
    } catch (Exception | Throwable $e) {
      Log::error($e);
      Toastr::error($e->getMessage(), __('toastr.error'));
      return redirect()->back();
    }
  }

  public function editStoreCandidatura(Request $request, $id)
  {
    try {

      // dd($request->all(), $id);
      $data_start = strtotime($request->data_start);
      $data_end = strtotime($request->data_end);

      if ($data_start > $data_end) {
        Toastr::warning("A data de começo é maior que a data de termino", 'Conflito entre datas');
        return redirect()->back();
      }

      $result = DB::update('UPDATE lective_candidate_calendarie SET data_inicio=?, data_fim=?  WHERE id = ?', [
        $request->data_start,
        $request->data_end,
        $id
      ]);

      $this->actualizarDatasCalendariosPassaram();

      Toastr::success("calendário foi actualizado com sucesso", 'Sucesso');
      return redirect()->back();
    } catch (Exception | Throwable $e) {
      Log::error($e);
      Toastr::error($e->getMessage(), __('toastr.error'));
      return redirect()->back();
    }
  }





  //curso padrão 
  public function StoreDefaultCourseCandidate(Request $request)
  {

    try {



      //dados
      $user_id = $request->id_user;
      $course_id = $request->course_default;

      DB::table('courses_default')
        ->updateOrInsert(
          [
            'users_id' => $user_id,
          ],
          [
            'courses_id' => $course_id,
            'created_by' => $userId = auth()->user()->id,
            'updated_by' => $userId = auth()->user()->id,
          ]
        );


      Toastr::success("Opcão de curso actualizada com Manuel sucesso!", __('toastr.success'));
      return redirect()->back();
    } catch (Exception | Throwable $e) {

      Log::error($e);
      Toastr::error($e->getMessage(), __('toastr.error'));
      return redirect()->back();
    }
  }

  public function generateCandidatesGep($ano_lectivo){
    try{
        
        return Excel::download(new CandidatesExport($ano_lectivo), 'candidates-gep.xlsx');
       
    } catch (Exception | Throwable $e) {
       
        Log::error($e);
        Toastr::error($e->getMessage(), __('toastr.error'));
        return redirect()->back();
    }
}
}
