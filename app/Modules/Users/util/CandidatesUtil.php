<?php

namespace App\Modules\Users\util;

use App\Modules\Users\Enum\ParameterEnum;
use App\Modules\Users\Models\User;
use App\Modules\Users\Models\Parameter;
use App\Modules\GA\Models\Classes;
use App\Modules\GA\Models\LectiveYear;
use App\Helpers\LanguageHelper;
use Carbon\Carbon;
use DataTables;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CandidatesUtil
{

  public function addColumnCheckBox($model)
  {
    return Datatables::of($model)
      ->addColumn('bi_doc', function ($item) {
        return view('Users::candidate.datatables.bi_doc', compact('item'));
      })
      ->addColumn('diploma', function ($item) {
        return view('Users::candidate.datatables.diploma', compact('item'));
      })
      ->addColumn('foto', function ($item) {
        return view('Users::candidate.datatables.foto', compact('item'));
      });
  }

  public static function parameter($id)
  {
    $parameter = Parameter::with('currentTranslation')->find($id);
    return $parameter;
  }

  public function modelQueryTwoCourse($lectiveYear, $id_fase)
  {
    $sql = DB::table('articles as art')
      ->leftjoin('article_requests as ar', 'art.id', '=', 'ar.article_id')
      ->join('disciplines as disciplina', 'disciplina.id', '=', 'ar.discipline_id')
      ->join('courses as curso', 'disciplina.courses_id', '=', 'curso.id')
      ->join('courses_translations as ct', 'ct.courses_id', '=', 'curso.id')
      ->join('users as usuario', 'ar.user_id', '=', 'usuario.id')
      ->join('user_candidate as uca', 'uca.user_id', '=', 'usuario.id')
      ->where("art.id_code_dev", 6)
      ->where("ct.active", 1)
      ->where("uca.year", $lectiveYear->id)
      ->where('usuario.name', "!=", "")
      ->whereNull('ar.deleted_at')
      ->whereNull('ar.deleted_by')
      ->whereNull('disciplina.deleted_at')
      ->whereNull('ct.deleted_at')
      ->whereNull('art.deleted_at')
      ->whereNull('art.deleted_by')
      ->whereNull('curso.deleted_at')
      ->whereNull('usuario.deleted_at')
      ->where('ar.discipline_id', "!=", null)
      ->whereBetween('art.created_at', [$lectiveYear->start_date, $lectiveYear->end_date])
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
        "usuario.name as usuario"
      ])
      ->orderBy('ar.id', 'desc');
    if ($id_fase != null) {
      if($id_fase == 10){
        $sql = $sql->where('uca.year_fase_id', 3);
      }
      $sql = $sql->where('uca.year_fase_id', $id_fase);
    }


    return $sql;
  }

  public function modelQueryGlobal($lectiveYear)
  {

    $users = User::query()
      ->whereHas('roles', function ($q) {
        $q->whereIn('id', [15, 6]);
      })
      ->join('users as u1', 'u1.id', '=', 'users.created_by')
      ->join('user_candidate as uca', 'uca.user_id', '=', 'users.id')
      ->join('lective_candidate as lc', function ($join) {
        $join->on('lc.id', '=', 'uca.year_fase_id');
      })
      ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'users.id')
      ->leftJoin('courses_translations as ct', function ($join) {
        $join->on('ct.courses_id', '=', 'uc.courses_id');
        $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
        $join->on('ct.active', '=', DB::raw(true));
      })

      ->leftJoin('users_states as us', 'users.id', '=', 'us.user_id')
      ->leftJoin('article_requests', 'article_requests.user_id', '=', 'users.id')
      ->leftJoin('user_parameters as candidate', function ($join) {
        $join->on('users.id', '=', 'candidate.users_id')
          ->where('candidate.parameters_id', ParameterEnum::NUMERO_CANDIDADO);
      })
      ->leftJoin('user_parameters as full_name', function ($join) {
        $join->on('users.id', '=', 'full_name.users_id')
          ->where('full_name.parameters_id', ParameterEnum::NOME);
      })
      ->leftJoin('user_parameters as up_meca', function ($join) {
        $join->on('users.id', '=', 'up_meca.users_id')
          ->where('up_meca.parameters_id', ParameterEnum::N_MECANOGRAFICO);
      })
      ->leftJoin('user_parameters as up_foto', function ($join) {
        $join->on('users.id', '=', 'up_foto.users_id')
          ->where('up_foto.parameters_id', ParameterEnum::FOTOGRAFIA);
      })
      ->leftJoin('user_parameters as up_bidoc', function ($join) {
        $join->on('users.id', '=', 'up_bidoc.users_id')
          ->where('up_bidoc.parameters_id', ParameterEnum::DOCUMENTO_BI);
      })
      ->leftJoin('user_parameters as up_dip', function ($join) {
        $join->on('users.id', '=', 'up_dip.users_id')
          ->where('up_dip.parameters_id', ParameterEnum::DIPLOMA_ENSINO_MEDIO_PDF);
      })
      ->leftJoin('lective_years', function ($join) use ($lectiveYear) {
        //$join->whereRaw('users.created_at between `start_date` and `end_date`');
        $join->where('lective_years.id', $lectiveYear->id);
      })
      ->join('lective_year_translations as lyt', function ($join) {
        $join->on('lyt.lective_years_id', '=', 'lective_years.id');
        $join->on('lyt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
        $join->on('lyt.active', '=', DB::raw(true));
      })
      ->leftJoin('users as u2', 'u2.id', '=', 'users.updated_by')
      ->leftJoin('users as u3', 'u3.id', '=', 'users.deleted_by')
      ->select([
        'users.*',
        'full_name.value as name_name',
        'up_dip.value as diploma',
        'up_foto.value as foto',
        'up_bidoc.value as bi_doc',
        'u1.name as us_created_by',
        'u2.name as us_updated_by',
        'full_name.updated_at as updated_at',
        'ct.display_name as nome_course',
        'ct.courses_id as id_curso',
        'article_requests.status as state',
        'article_requests.id as art',
        //'candidate.value as cand_number',
        'uca.code as cand_number',
        'lyt.display_name as lective_year_code',
        'lc.fase as fase',
        'uca.year_fase_id as id_fase',
        'up_meca.value as matriculation',
        'uca.year as ano_lectivo',
        'uca.created_at as criado_a'
      ])
      ->whereBetween('uca.created_at', [$lectiveYear->start_date, $lectiveYear->end_date])
      ->whereNull('article_requests.deleted_at')
      ->groupBy('full_name.users_id')
      //->whereNotNull('candidate.value')
      ->orderBy('uca.id', 'DESC')
      ->distinct('full_name.value')
      ->where('uca.year', $lectiveYear->id);

    return $users;
  }

  public function modelQuery($lectiveYear, $id_fase = null)
  {
    log::info('dados do modelQuery', $lectiveYear);
    $users = User::query()
      ->whereHas('roles', function ($q) {
        $q->whereIn('id', [15, 6]);
      })
      ->join('users as u1', 'u1.id', '=', 'users.created_by')
      ->join('user_candidate as uca', 'uca.user_id', '=', 'users.id')
      ->join('lective_candidate as lc', function ($join) {
        $join->on('lc.id', '=', 'uca.year_fase_id');
      })
      ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'users.id')
      ->leftJoin('courses_translations as ct', function ($join) {
        $join->on('ct.courses_id', '=', 'uc.courses_id');
        $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
        $join->on('ct.active', '=', DB::raw(true));
      })

      ->leftJoin('users_states as us', 'users.id', '=', 'us.user_id')
      ->leftJoin('article_requests', 'article_requests.user_id', '=', 'users.id')
      ->leftJoin('user_parameters as candidate', function ($join) {
        $join->on('users.id', '=', 'candidate.users_id')
          ->where('candidate.parameters_id', ParameterEnum::NUMERO_CANDIDADO);
      })
      ->leftJoin('user_parameters as full_name', function ($join) {
        $join->on('users.id', '=', 'full_name.users_id')
          ->where('full_name.parameters_id', ParameterEnum::NOME);
      })
      ->leftJoin('user_parameters as up_meca', function ($join) {
        $join->on('users.id', '=', 'up_meca.users_id')
          ->where('up_meca.parameters_id', ParameterEnum::N_MECANOGRAFICO);
      })
      ->leftJoin('user_parameters as up_foto', function ($join) {
        $join->on('users.id', '=', 'up_foto.users_id')
          ->where('up_foto.parameters_id', ParameterEnum::FOTOGRAFIA);
      })
      ->leftJoin('user_parameters as up_bidoc', function ($join) {
        $join->on('users.id', '=', 'up_bidoc.users_id')
          ->where('up_bidoc.parameters_id', ParameterEnum::DOCUMENTO_BI);
      })
      ->leftJoin('user_parameters as up_dip', function ($join) {
        $join->on('users.id', '=', 'up_dip.users_id')
          ->where('up_dip.parameters_id', ParameterEnum::DIPLOMA_ENSINO_MEDIO_PDF);
      })
      ->leftJoin('lective_years', function ($join) use ($lectiveYear) {
        //$join->whereRaw('users.created_at between `start_date` and `end_date`');
        $join->where('lective_years.id', $lectiveYear->id);
      })
      ->join('lective_year_translations as lyt', function ($join) {
        $join->on('lyt.lective_years_id', '=', 'lective_years.id');
        $join->on('lyt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
        $join->on('lyt.active', '=', DB::raw(true));
      })
      ->leftJoin('users as u2', 'u2.id', '=', 'users.updated_by')
      ->leftJoin('users as u3', 'u3.id', '=', 'users.deleted_by')
      ->select([
        'users.*',
        'full_name.value as name_name',
        'up_dip.value as diploma',
        'up_foto.value as foto',
        'up_bidoc.value as bi_doc',
        'u1.name as us_created_by',
        'u2.name as us_updated_by',
        'full_name.updated_at as updated_at',
        'ct.display_name as nome_course',
        'ct.courses_id as id_curso',
        'article_requests.status as state',
        'article_requests.id as art',
        //'candidate.value as cand_number',
        'uca.code as cand_number',
        'lyt.display_name as lective_year_code',
        'lc.fase as fase',
        'uca.year_fase_id as id_fase',
        'up_meca.value as matriculation',
        'uca.year as ano_lectivo',
        'uca.created_at as criado_a'
      ])
      //->whereBetween('uca.created_at', [$lectiveYear->start_date, $lectiveYear->end_date])
      ->whereNull('article_requests.deleted_at')
      ->groupBy('full_name.users_id')
      //->whereNotNull('candidate.value')
      ->orderBy('uca.id', 'DESC')
      ->distinct('full_name.value');

    if ($id_fase != null) {
      if($id_fase == 10){
        $users = $users->where('uca.year_fase_id', 3);
      }
      $users = $users->where('uca.year_fase_id', $id_fase);
    }

    return $users;
  }

  public function cursoQuery($lectiveYear, $id_fase = null)
  {
    $sql = DB::table('articles as art')
      ->leftjoin('article_requests as ar', 'art.id', '=', 'ar.article_id')
      ->join('disciplines as disciplina', 'disciplina.id', '=', 'ar.discipline_id')
      ->join('courses as curso', 'disciplina.courses_id', '=', 'curso.id')
      ->join('courses_translations as ct', 'ct.courses_id', '=', 'curso.id')
      ->join('users as usuario', 'ar.user_id', '=', 'usuario.id')
      ->where("art.id_code_dev", 6)
      ->where("ct.active", 1)
      ->where('usuario.name', "!=", "")
      ->whereNull('ar.deleted_at')
      ->whereNull('disciplina.deleted_at')
      ->whereNull('ct.deleted_at')
      ->whereNull('art.deleted_at')
      ->whereNull('curso.deleted_at')
      ->whereNull('usuario.deleted_at')
      ->where('ar.discipline_id', "!=", null)
      //->whereBetween('art.created_at', [$lectiveYear->start_date, $lectiveYear->end_date])
      //->whereBetween('ar.created_at', [$lectiveYear->start_date, $lectiveYear->end_date])
      ->select([
        "art.id as articles",
        "ar.id as articles_req",
        "ar.discipline_id as discipline",
        "curso.id as course",
        "ct.display_name as nome_curso",
        "ar.user_id as usuario_id",
        "ar.status as state",
        "ar.base_value as valor",
        "usuario.name as usuario"
      ])
      ->orderBy('ar.id', 'desc');
    if ($id_fase != null) {
    }

    return $sql;
  }

  public function modelQueryGet($lectiveYear, $id_fase = null)
  {
    return $this->modelQuery($lectiveYear, $id_fase)->get();
  }

  public function modelQueryGetGlobal($lectiveYear)
  {
    return $this->modelQueryGlobal($lectiveYear)->get();
  }

  public function modelQueryTwoCourseGet($lective_year, $id_fase)
  {
    return $this->modelQueryTwoCourse($lective_year, $id_fase)->get();
  }

  public function cursoQueryGet($lectiveYear, $id_fase = null)
  {
    return $this->cursoQuery($lectiveYear, $id_fase)->get();
    //return DB::select('CALL proc_candidate_course(?)', [$lectiveYear->id]);
  }

  private function queryStaffStudentByBi($bi)
  {
    return DB::table('staff_student as ss')
      ->join('user_parameters as up', 'up.users_id', '=', 'ss.id_user')
      ->where('ss.status', 1)
      ->where('up.parameters_id', 14)
      ->where('up.value', $bi)
      ->select(['ss.id_user', 'ss.status', 'ss.id', 'ss.is_candidato']);
  }

  public function verifyInStaffStudentByBi($bi)
  {
    return $this->queryStaffStudentByBi($bi)->first();
  }

  public function staffStudentByBiAll($bi)
  {
    return $this->queryStaffStudentByBi($bi)->get();
  }

  public function biStaffEstudante($biNumber, $createdID)
  {
    if (!isset($createdID))
      return;
    $staffs = $this->staffStudentByBiAll($biNumber);
    foreach ($staffs as $staff) {
      DB::table('staff_student')->where(['id' => $staff->id])->update([
        "status" => 0
      ]);
    }
  }

  public function analisarFase($userCandidate, $courses, $turmas)
  {
    if (isset($userCandidate->user_id)) {
      $faseActual = DB::table('lective_candidate')->where('id', $userCandidate->year_fase_id)->first();

      if (isset($faseActual->id)) {
        $auth = Auth::user()->id;
        $data = Carbon::now();
        foreach ($courses as $course) {
          DB::table('lective_candidate_historico_fase')->updateOrInsert(
            ['id_fase' => $faseActual->id, 'user_id' => $userCandidate->user_id, 'id_curso' => $course],
            ['id_fase' => $faseActual->id, 'user_id' => $userCandidate->user_id, 'id_curso' => $course, 'updated_by' => $auth, 'updated_at' => $data]
          );
        }
        //verificar o turmas no historico de fase
        foreach ($turmas as $turma) {
          $classe = DB::table('classes')->find($turma);
          if (isset($classe->id)) {
            DB::table('lective_candidate_historico_fase')->updateOrInsert(
              ['id_fase' => $faseActual->id, 'user_id' => $userCandidate->user_id, 'id_curso' => $classe->courses_id],
              [
                'id_fase' => $faseActual->id,
                'user_id' => $userCandidate->user_id,
                'id_curso' => $classe->courses_id,
                'id_turma' => $classe->id,
                'updated_by' => $auth,
                'updated_at' => $data
              ]
            );
          }
        }
      }
    }
  }

  public function modelVerifyUserFaseNull($model)
  {
    foreach ($model as $user) {
      $userCandidate = DB::table('user_candidate')->where('user_id', $user->id)->first();
      if (isset($userCandidate->user_id)) {
        if ($userCandidate->year_fase_id == null) {
          $lective_year = $this->calculateLective($userCandidate->created_at);
          if (isset($lective_year->id)) {
            $fase = DB::table('lective_candidate')
              ->whereRaw('"' . $userCandidate->created_at . '" between `data_inicio` and `data_fim`')
              ->where('id_years', $lective_year->id)
              ->first();
            if (isset($fase->id)) {
              DB::update('UPDATE user_candidate SET year_fase_id=? WHERE user_id=? AND code=?', [$fase->id, $userCandidate->user_id, $userCandidate->code]);
            } else {
              $fase = DB::table('lective_candidate')
                ->where('data_fim', '<=', $userCandidate->created_at)
                ->where('id_years', $lective_year->id)
                ->orderBy('data_fim', 'ASC')
                ->first();
              if (isset($fase->id))
                DB::update('UPDATE user_candidate SET year_fase_id=? WHERE user_id=? AND code=?', [$fase->id, $userCandidate->user_id, $userCandidate->code]);
            }
          }
        }
      }
    }
    return $model;
  }

  public function calculateLective($data)
  {
    return DB::table('lective_years')->whereRaw('"' . $data . '" between `start_date` and `end_date`')->first();
  }

  public function actualizarDatasCalendariosPassaram()
  {
    DB::update('UPDATE lective_candidate SET is_termina = ? WHERE now() > data_fim', [1]);
    DB::update('UPDATE lective_candidate SET is_termina = ? WHERE now() < data_fim', [0]);

    DB::update('UPDATE lective_candidate_calendarie SET is_termina = ? WHERE now() > data_fim', [1]);
    DB::update('UPDATE lective_candidate_calendarie SET is_termina = ? WHERE now() < data_fim', [0]);
  }

  public static function classCandidate($user)
  {
    $course = $user->courses;
    if (!isset($course[0]))
      return [];

    $currentData = Carbon::now();
    $lectiveYear = LectiveYear::whereRaw('"' . $currentData . '" between `start_date` and `end_date`')->first();
    if (!isset($lectiveYear->id))
      return [];

    $classes = Classes::where(['courses_id' => $course[0]->id, 'lective_year_id' => $lectiveYear->id,])->get();

    return $classes;
  }

  public static function isUserClass($user_id, $class_id)
  {
    $db = DB::table('user_classes')->where(['user_id' => $user_id, 'class_id' => $class_id])->first();
    return isset($db);
  }

}

?>