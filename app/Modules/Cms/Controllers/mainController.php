<?php

namespace App\Modules\Cms\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Model\Institution;
use App\Modules\Cms\Models\Language;
use App\Modules\Cms\Models\Menu;
use App\Modules\Cms\Models\MenuItem;
use App\Modules\Cms\Models\MenuTranslation;
use App\Modules\Cms\Requests\MenuRequest;
use App\Modules\GA\Enum\PeriodTypeEnum;
use App\Modules\GA\Models\Building;
use App\Modules\GA\Models\Classes;
use App\Modules\GA\Models\DayOfTheWeek;
use App\Modules\GA\Models\Discipline;
use App\Modules\GA\Models\DisciplineRegime;
use App\Modules\GA\Models\LectiveYear;
use App\Modules\GA\Models\PeriodType;
use App\Modules\GA\Models\Room;
use App\Modules\GA\Models\Schedule;
use App\Modules\GA\Models\ScheduleEvent;
use App\Modules\GA\Models\ScheduleTime;
use App\Modules\GA\Models\ScheduleTimeTranslation;
use App\Modules\GA\Models\ScheduleTranslation;
use App\Modules\GA\Models\ScheduleType;
use App\Modules\GA\Models\ScheduleTypeTime;
use App\Modules\GA\Models\StudyPlan;
use App\Modules\GA\Models\StudyPlanEdition;
use App\Modules\GA\Requests\ScheduleRequest;
use App\Modules\Users\Models\Matriculation;
use App\Modules\Users\Models\User;
use Auth;
use Carbon\Carbon;
use DataTables;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use PDF;
use Throwable;
use Toastr;
use App\Modules\GA\Controllers\SchedulesController;

class mainController extends Controller
{

    public function index()
    {
        try {

            $institution = Institution::latest()->first();
            $logotipo = $_SERVER['HTTP_HOST'] . "/instituicao-arquivo/" . $institution->logotipo;

            if (Auth::check() && Auth::user()->hasRole('student')) {
                return $this->student();
            }

            // Para tesoureiros Chefes
            if (Auth::check() && (Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('chefe_tesoureiro') || Auth::user()->hasRole('promotor') || Auth::user()->hasRole('presidente'))) {
                return $this->staff();
            }


            $data = ["logotipo" => $logotipo];


            return view('Cms::initial.index', $data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function student()
    {
        try {
                $currentData = Carbon::now();
                $lective = DB::table('lective_years')
                    ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                    ->first();

                if (!$lective) {
                    Log::error('Nenhum lective_year encontrado para a data ' . $currentData);
                    abort(404, 'Ano lectivo não encontrado');
                }

                $institution = Institution::latest()->first();
                $logotipo = $_SERVER['HTTP_HOST'] . "/storage/" . $institution->logotipo;
                $mesAtual = date('n');
                // Verifica em qual semestre o mês se encaixa
                if ($mesAtual >= 10 || $mesAtual <= 2) {
                    $semestre = 1;  // Primeiro semestre (Outubro a Fevereiro)
                } elseif ($mesAtual >= 3 && $mesAtual <= 7) {
                    $semestre = 2;  // Segundo semestre (Março a Julho)
                } else {
                    $semestre = 0;  // Caso não esteja em nenhum dos intervalos (Agosto e Setembro)
                }

                $config = DB::table('avalicao_config')->where('lective_year',$lective->id)->first();
                Log::info('CONFIG DEBUG3', ['config' => $config]);
                $student = auth()->user()->id;
                $matriculations = $this->get_matriculation_student(null, $student);

                $melhoria_notas = get_melhoria_notas($student, $lective->id, 0);
                $d = $this->schedule();

               if(is_array($d)){

                $data = [
                    "notification" => $this->get_notification(),
                    "articles" => $this->get_payments(),
                    "disciplines" => $this->get_disciplines(),
                    "percurso" => $this->get_percurso(),
                    "tempo" => $this->times(),
                    "plano" => $this->study_plain(),
                    "matriculations"=>$matriculations,
                    "classes"=>$this->matriculation_classes($matriculations->id),
                    "logotipo"=>$logotipo,
                    "semestre" => $semestre,
                    "config" => $config,
                    "institution" => $institution,
                    'schedule_types' => $d['schedule_types'],
                    'days_of_the_week' => $d['days_of_the_week'],
                    'user' => $d['user'],
                    'events_by_type' => $d['events_by_type'],
                    'schedule_id' => $d['schedule_id'],
                    'lectiveYearSelected' => $d['lectiveYearSelected'],
                    'lectiveYears' => $d['lectiveYears'],
                    'teacher_discipline' => $d['teacher_discipline'],
                    "melhoria_notas" => $melhoria_notas
                ];Log::info('CONFIG DEBUG1.1', ['config' => $config]);
            } 
            else {
              
                $data = [
                    "notification" => $this->get_notification(),
                    "articles" => $this->get_payments(),
                    "disciplines" => $this->get_disciplines(),
                    "percurso" => $this->get_percurso(),
                    "tempo" => $this->times(),
                    "plano" => $this->study_plain(),
                    "matriculations"=>$matriculations,
                    "classes"=>$this->matriculation_classes($matriculations->id),
                    "logotipo"=>$logotipo,
                    "semestre" => $semestre,
                    "config" => $config,
                    "institution" => $institution,
                    "melhoria_notas" => $melhoria_notas
                ];
            }
             Log::info('CONFIG DEBUG1', ['config' => $config]);
                return view('Cms::initial.student',$data);

        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function staff()
    {
        try {

            $institution = Institution::latest()->first();
            $logotipo = $_SERVER['HTTP_HOST'] . "/instituicao-arquivo/" . $institution->logotipo;

            $data = [
                "notification" => $this->get_notification(),
                "articles" => $this->get_payment_staff(),
                "logotipo" => $logotipo
            ];

            return view('Cms::initial.staff', $data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function get_payment_staff($lective_year = null)
    {
        $currentData = Carbon::now();
        $lective = DB::table('lective_years')
            ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
            ->first();

        if (!$lective) {
            // O que fazer se não houver ano lectivo activo?
            // Por exemplo, podes abortar:
            abort(404, 'Ano lectivo actual não encontrado.');
        }


        $recibo = DB::table('transactions as tr')
            ->join('transaction_receipts as trans', 'tr.id', '=', 'trans.transaction_id')
            ->leftJoin('users', 'tr.created_by', 'users.id')
            ->leftJoin('transaction_article_requests as tar', 'tr.id', '=', 'tar.transaction_id')
            ->join('article_requests as art', function ($join) {
                $join->on('art.id', '=', 'tar.article_request_id');
            })
            ->leftJoin('user_parameters as u_p', function ($join) {
                $join->on('art.user_id', '=', 'u_p.users_id')
                    ->where('u_p.parameters_id', 1);
            })
            ->leftJoin('user_parameters as u_m', function ($join) {
                $join->on('art.user_id', '=', 'u_m.users_id')
                    ->where('u_m.parameters_id', 19);
            })
            ->join('transaction_info as tinfo', 'tr.id', '=', 'tinfo.transaction_id')
            ->join('banks as ban', 'tinfo.bank_id', '=', 'ban.id')
            ->where('ban.id', '!=', '59')
            ->orderBy('code', 'asc')
            ->select(['tr.id as id_transacion', 'trans.id', 'trans.code', 'trans.path', 'users.name', 'trans.created_at', 'tr.value as valor', 'art.user_id', 'u_p.value as estudante', 'u_m.value as matricula'])
            // ->select(['trans.created_at', 'tr.value as valor'])
            ->where('tr.data_from', "!=", "Estorno")
            ->WhereDate('trans.created_at', '>=', $lective->start_date)
            ->WhereDate('trans.created_at', '<=', $lective->end_date)
            ->distinct('trans.path')
            ->get();


        $hoje = Carbon::now();

        $start_week = Carbon::now();
        $end_week = Carbon::now();
        $data_mes = Carbon::now();
        $currentData = Carbon::now();

        $segunda = $start_week->startOfWeek();
        $sabado = $end_week->endOfWeek();
        $sabado->subDays(1);




        $primeiroDiaMes = $data_mes->copy()->firstOfMonth()->toDateString();
        $ultimoDiaMes = $data_mes->copy()->lastOfMonth()->toDateString();

        $lective_year = DB::table('lective_years')
            ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
            ->first();


        $date = array(
            "month" => ["start" => $primeiroDiaMes, "end" => $ultimoDiaMes],
            "week" => [
                "seg" => ["date" => $segunda->toDateString(), "money" => 0],
                "ter" => ["date" => $start_week->addDays(1)->toDateString(), "money" => 0],
                "qua" => ["date" => $start_week->addDays(1)->toDateString(), "money" => 0],
                "qui" => ["date" => $start_week->addDays(1)->toDateString(), "money" => 0],
                "sex" => ["date" => $start_week->addDays(1)->toDateString(), "money" => 0],
                "sab" => ["date" => $sabado->toDateString(), "money" => 0]
            ],
            "year" => ["start" => $lective_year->start_date, "end" => $lective_year->end_date]
        );
        $money = 0;

        $payments = collect($recibo)->map(function ($item, $key) use ($date) {
            return [
                "date" => date('Y-m-d', strtotime($item->created_at)),
                "value" => $item->valor
            ];
        });

        $week = collect($payments)->groupBy("date")->map(function ($item, $key) use ($date) {

            $total = null;
            foreach ($item as $key_2 => $item_2) {
                if (date($item_2["date"]) == date('Y-m-d', strtotime($date["week"]["seg"]["date"]))) {
                    $total = $total + $item_2["value"];
                }
                if (date($item_2["date"]) == date('Y-m-d', strtotime($date["week"]["ter"]["date"]))) {
                    $total = $total + $item_2["value"];
                }
                if (date($item_2["date"]) == date('Y-m-d', strtotime($date["week"]["qua"]["date"]))) {
                    $total = $total + $item_2["value"];
                }
                if (date($item_2["date"]) == date('Y-m-d', strtotime($date["week"]["qui"]["date"]))) {
                    $total = $total + $item_2["value"];
                }
                if (date($item_2["date"]) == date('Y-m-d', strtotime($date["week"]["sex"]["date"]))) {
                    $total = $total + $item_2["value"];
                }
                if (date($item_2["date"]) == date('Y-m-d', strtotime($date["week"]["sab"]["date"]))) {
                    $total = $total + $item_2["value"];
                }
            }
            return $total;
        });

        // Resumo semanal

        $day_of_week = [];
        $day_of_week["total"] = 0;

        foreach ($week as $key => $item) {
            if ($item != null) {
                $day_of_week["total"] += $item;
                $day_of_week[$key] = number_format($item, 2, ',', '.');
            }
        }


        $month_day = collect($payments)->map(function ($item, $key) use ($date) {
            $item["date"] = explode('-', $item["date"])[1];
            return $item;
        });


        // Aqui reotorna o resumo mensal

        $month = collect($month_day)->groupBy("date")->map(function ($item, $key) {
            return number_format($item->sum("value"), 2, ',', '.');
        });
        $total_anual = collect($month_day)->groupBy("date")->map(function ($item, $key) {
            return $item->sum("value");
        });

        $total_year = 0;

        $new_array_month = [
            "m08" => ["money" => "0,00", "nome" => "Agosto"],
            "m09" => ["money" => "0,00", "nome" => "Setembro"],
            "m10" => ["money" => "0,00", "nome" => "Outubro"],
            "m11" => ["money" => "0,00", "nome" => "Novembro"],
            "m12" => ["money" => "0,00", "nome" => "Dezembro"],
            "m01" => ["money" => "0,00", "nome" => "Janeiro"],
            "m02" => ["money" => "0,00", "nome" => "Fevereiro"],
            "m03" => ["money" => "0,00", "nome" => "Março"],
            "m04" => ["money" => "0,00", "nome" => "Abril"],
            "m05" => ["money" => "0,00", "nome" => "Maio"],
            "m06" => ["money" => "0,00", "nome" => "Junho"],
            "m07" => ["money" => "0,00", "nome" => "Julho"],
            "total" => 0,
        ];

        foreach ($total_anual as $key => $item) {
            $new_array_month["m" . $key]["money"] = number_format($item, 2, ',', '.');
            $new_array_month["total"] += $item;
        }
        $new_array_month["total"] = number_format($new_array_month["total"], 2, ',', '.');

        return [
            "days" => $day_of_week,
            "month" => $new_array_month,
            "date" => $date
        ];
    }

    public static function fr_menu()
    {
        $menu = DB::table('menus')
            ->leftJoin('menu_items as m_i', 'm_i.menus_id', '=', 'menus.id')
            ->leftJoin('menu_item_translations as mit', 'mit.menu_items_id', '=', 'm_i.id')
            ->where("mit.active", "1")
            ->where("mit.language_id", "1")
            ->where("menus.order", "<", 13)
            ->whereNull("m_i.parent_id")
            ->whereNull("menus.deleted_at")
            ->whereNull("m_i.deleted_at")
            ->whereNull("mit.deleted_at")
            ->select([
                "menus.id",
                "menus.code",
                "menus.order",
                "m_i.parent_id",
                "m_i.id as menu_item",
                "m_i.external_link",
                "m_i.external_link",
                "mit.display_name",

            ])
            ->orderBy("menus.order")
            ->get();
        return $menu;
    }


    public static function verify_permission($menu)
    {



        $menu = DB::table('model_has_permissions as menu_permission')
            ->where('menu_permission.model_type', "App\Modules\Cms\Models\MenuItem")
            ->where('menu_permission.model_id', $menu)
            ->select(["menu_permission.permission_id"])
            ->get();


        if (count($menu) > 0) {
            $permission = DB::table('model_has_roles as usuario_cargo')
                ->Join("role_has_permissions as rhp", "rhp.role_id", "=", "usuario_cargo.role_id")
                ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
                ->where('usuario_cargo.model_id', auth()->user()->id)
                ->where('rhp.permission_id', $menu[0]->permission_id)
                ->select(["rhp.permission_id"])
                ->get();

            if (count($permission) > 0) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public static function get_payments($lective_year = null, $student = null)
    {

        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
            ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
            ->first();

        if (isset($lective_year)) {
            $lectiveYearSelected = $lective_year;
        } else {
            $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
        }

        if (isset($student)) {
        } else {
            $student = auth()->user()->id;
        }

        $bolseiros = DB::table('scholarship_holder')->get()->map(function ($item) {
            return $item->user_id;
        })->all();

        $payments = DB::table("article_requests as ar")
            ->join("articles as art", "art.id", "ar.article_id")
            ->join("article_translations as art_t", "art_t.article_id", "art.id")
            ->leftjoin("disciplines as disc", 'disc.id', 'ar.discipline_id')
            ->leftjoin('disciplines_translations as dt', function ($join) {
                $join->on('dt.discipline_id', '=', 'disc.id');
                $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dt.active', '=', DB::raw(true));
            })
            ->where("ar.user_id", $student)
            ->whereNull("ar.deleted_at")
            ->whereNull("art.deleted_at")
            ->where("art_t.active", 1)
            ->where("art.anoLectivo", $lectiveYearSelected)
            ->where("art_t.language_id", 1)
            ->whereNotIn('ar.user_id', $bolseiros ?? [])
            ->select(["art_t.display_name", "ar.base_value", "ar.status", "ar.month", "ar.year", "dt.display_name as disciplina", "disc.code as code_disciplina"])
            ->orderBy("ar.status", "asc")
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

        $count = collect($payments)->groupBy("status")->map(function ($item, $key) use ($config_divida) {
            //    return count($item);
            $i = null;

            if ($key == "pending") {
                foreach ($item as $mensalidade) {
                    if (isset($mensalidade->year) && ($mensalidade->year > 0)) {
                        $hoje = Carbon::create(date("Y-m-d"));
                        $limite = Carbon::create($mensalidade->year . "-" . $mensalidade->month . "-" . $config_divida->dias_exececao);

                        if ($hoje >= $limite) {
                            ++$i;
                        }
                    } else {
                        $i++;
                    }
                }
                return $i;
            } else {

                return count($item);
            }
        });





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

        return [
            "payments" => $payments,
            "count" => $count,
            "dividas" => $dividas,
        ];
    }

    public static function get_notification()
    {

        $aluno = auth()->user()->id;
        $userStudant = DB::table('users')->where('id', $aluno)->first();
        if ($userStudant != null)
            $estudanteNome = '%' . $userStudant->name . '%';


        $notification = DB::table("tb_notification")
            ->where("sent_to", auth()->user()->id)
            ->where('body_messenge', 'like', $estudanteNome)
            ->orderBy("date_sent", "desc")
            ->whereNull("deleted_at")
            ->get();

        $notification_new = DB::table("tb_notification")
            ->where("sent_to", auth()->user()->id)
            ->where('body_messenge', 'like', $estudanteNome)
            ->whereNull("state_read")
            ->whereNull("deleted_at")
            ->count();


        return [
            "notification" => $notification,
            "count" => $notification_new,
        ];
    }


    public static function get_disciplines($lective_year = null, $student_id = null)
    {


        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
            ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
            ->first();
        $lectiveYearSelected_id = $lectiveYearSelected->id ?? 6;


        if (isset($lective_year)) {
            $lectiveYearSelected_id = $lective_year;
        }

        if (isset($student_id)) {
            $student = $student_id;
        } else {
            $student = auth()->user()->id;
        }



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
            ->where("user.id", $student)
            ->select([
                'disc.id as id_disciplina',
                'disc.code as code_disciplina',
                'turma.display_name as turma',
                'mat.code',
                'mat_disc.exam_only as e_f',
                'dt.display_name as nome_disciplina',
                'turma.lective_year_id as id_anoLectivo'
            ])

            ->orderBy('code_disciplina', 'ASC')
            ->distinct(['disc.id', 'up_bi.value', 'mat.code', 'u_p.value'])
            ->where("mat.lective_year", $lectiveYearSelected_id)
            ->whereNull('mat.deleted_at')
            ->get()
            ->unique('id_disciplina');

        $collection = collect($model);


        $collection = collect($model);
        return $disciplines = $collection->groupBy('code_disciplina', function ($item) {
            return ($item);
        })->map(function ($disc) {
            return $disc->first();
        });
    }


    public static function get_matriculation_student($lective_year = null, $student = null){

         $currentDate = Carbon::now();
    
        // Verifica correctamente se falta o ano lectivo
        if ($lective_year === null) {
            $lectiveYear = DB::table('lective_years')
                ->whereDate('start_date', '<=', $currentDate)
                ->whereDate('end_date', '>=', $currentDate)
                ->first();
           
            $lectiveYearSelected = $lectiveYear->id;
        } else {
            // Já foi passado um ano lectivo, utiliza-o tal como está
            $lectiveYearSelected = $lective_year;
        }
        
        if (!isset($student)) {
            $student = auth()->user()->id;
        }
        //dd($student, $lectiveYearSelected);
       
        //dd($lectiveYearSelected);
        $emolumento_confirma_prematricula = mainController::pre_matricula_confirma_emolumento($lectiveYearSelected);

        return $model = Matriculation::join('users as u0', 'u0.id', '=', 'matriculations.user_id')
            ->join('users as u1', 'u1.id', '=', 'matriculations.created_by')
            ->leftJoin('users as u2', 'u2.id', '=', 'matriculations.updated_by')
            ->leftJoin('users as u3', 'u3.id', '=', 'matriculations.deleted_by')
            ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'u0.id')
            ->join('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'uc.courses_id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })

            ->leftJoin('matriculation_classes as mc', 'mc.matriculation_id', '=', 'matriculations.id')
            ->join('classes as cl', function ($join) {
                $join->on('cl.id', '=', 'mc.class_id');
                $join->on('mc.matriculation_id', '=', 'matriculations.id');
                $join->on('matriculations.course_year', '=', 'cl.year');
            })


            ->leftJoin('user_parameters as u_p', function ($join) {
                $join->on('u0.id', '=', 'u_p.users_id')
                    ->where('u_p.parameters_id', 1);
            })

            ->leftJoin('user_parameters as up_meca', function ($join) {
                $join->on('u0.id', '=', 'up_meca.users_id')
                    ->where('up_meca.parameters_id', 19);
            })
            ->leftJoin('user_parameters as up_bi', function ($join) {
                $join->on('u0.id', '=', 'up_bi.users_id')
                    ->where('up_bi.parameters_id', 14);
            })

            ->leftJoin('article_requests as art_requests', function ($join) use ($emolumento_confirma_prematricula) {
                $join->on('art_requests.user_id', '=', 'u0.id')
                    ->whereIn('art_requests.article_id', $emolumento_confirma_prematricula);
            })
            ->leftJoin('lective_year_translations as ly', function ($join) {
                $join->on('cl.lective_year_id', '=', 'ly.lective_years_id')
                    ->where('ly.active', 1)
                    ->where('ly.language_id', 1);
            })
            ->select([
                'matriculations.*',
                'u0.id as id_usuario',
                'matriculations.code as code_matricula',
                'up_meca.value as matricula',
                'art_requests.status as state',
                'up_bi.value as n_bi',
                'cl.display_name as classe',
                'u_p.value as full_name',
                'u0.email as email',
                'u1.name as criado_por',
                'u2.name as actualizado_por',
                'u3.name as deletador_por',
                'ct.display_name as course',
                'uc.courses_id as id_course',
                'ly.display_name as lective_year',

            ])
            ->where('art_requests.deleted_by', null)
            ->where('art_requests.deleted_at', null)
            ->groupBy('u_p.value')
            ->where('matriculations.user_id', $student)
            ->where('matriculations.lective_year', $lectiveYearSelected)//$lectiveYearSelected->id

            ->distinct('id')
            ->first();
    }

    public static function pre_matricula_confirma_emolumento($lectiveYearSelected)
    {

        $confirm = EmolumentCodevLective("confirm", $lectiveYearSelected)->first();
        $Prematricula = EmolumentCodevLective("p_matricula", $lectiveYearSelected)->first();
        $confirm_tardia = EmolumentCodevLective("confirm_tardia", $lectiveYearSelected)->first();

        $emolumentos = [];

        if ($confirm != null) {
            $emolumentos[] = $confirm->id_emolumento;
        }
        if ($Prematricula != null) {
            $emolumentos[] = $Prematricula->id_emolumento;
        }
        if ($confirm_tardia != null) {
            $emolumentos[] = $confirm_tardia->id_emolumento;
        }
        return $emolumentos;
    }

    public static function get_percurso($student = null)
    {

        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
            ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
            ->first();
        $lectiveYearSelected_id = $lectiveYearSelected->id ?? 6;

        if (isset($student)) {
        } else {
            $student = auth()->user()->id;
        }


        $matriculations = DB::table("matriculations")
            ->where("user_id", $student)
            ->whereNull("deleted_at")
            ->where("lective_year", $lectiveYearSelected_id)
            ->select(["lective_year", "id"])
            ->orderBy("lective_year", "asc")
            ->first();

        if (!isset($matriculations->lective_year)) {
            return "Nenhuma matrícula encontrada neste ano lectivo";
        }

        $courses = DB::table("user_courses")
            ->where("users_id", $student)
            ->select(["courses_id"])
            ->first();

        if (!isset($courses->courses_id)) {
            return "Nenhum curso associado";
        }

        return $model = BoletimNotas_Student($matriculations->lective_year, $courses->courses_id, $matriculations->id);

        if (isset($model) && (count($model) > 0)) {
            return $model;
        } else {
            return "Nenhuma nota encontrada neste ano lectivo";
        }
    }


    public static function schedule($lective_year = null){

        try{
            $currentData = Carbon::now();

            $lectiveYearSelected = DB::table('lective_years')
            ->when(isset($lective_year),function($q)use($lective_year){
                $q->where('id',$lective_year);
            })
            ->when(!isset($lective_year),function($q)use($currentData){
                $q->whereRaw('"' . $currentData . '" between `start_date` and `end_date`');
            })
           ->first();

            if (isset($lectiveYearSelected)) {

                $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();

                $user = User::whereId(Auth::user()->id)->with([
                    'classes',
                    'matriculation' => function ($q) use($lectiveYearSelected) {
                        $q->where('lective_year',$lectiveYearSelected->id);
                        $q->with([
                            'disciplines',
                            'classes'
                        ]);
                    }
                ])->firstOrFail();

                // Find
                //$schedule = Schedule::whereId(14)->with([

                // Verifica se existe matrícula para o estudante
                if ($user->matriculation == null) {
                    // return $user->matriculation;
                    Toastr::error(__('Não existe matrícula para o estudante.'), __('toastr.error'));
                    return redirect()->back();
                }
                switch(date('m')){
                    case 10:
                    case 11:
                    case 12:
                    case 1:
                    case 2:
                        $period = PeriodTypeEnum::PRIMEIRO_SEMESTRE;
                    case 3:
                    case 4:
                    case 5:
                    case 6:
                    case 7:
                        $period = PeriodTypeEnum::SEGUNDO_SEMESTRE;
               }

                $classes = $user->matriculation->classes->pluck('id')->all();
                $discipline_list = $user->matriculation->disciplines->pluck('id')->all();
                $schedule_id = Schedule::whereHas('events.discipline', function ($q) use ($discipline_list) {
                    $q->whereIn('id', $discipline_list);
                })->with([
                    'translations' => function ($q) {
                        $q->whereActive(true);
                    },
                    'events' => function ($q) use ($discipline_list) {
                        $q->whereHas('discipline', function ($q) use ($discipline_list) {
                            $q->whereIn('id', $discipline_list);
                        });
                        $q->with([
                            'discipline' => function ($q) {
                                $q->with([
                                    'course' => function ($q) {
                                        $q->with([
                                            'currentTranslation'
                                        ]);
                                    }
                                ]);
                                $q->with([
                                    'currentTranslation'
                                ]);
                            },
                            'room' => function ($q) {
                                $q->with([
                                    'currentTranslation'
                                ]);
                            }
                        ]);
                    },
                ])
                ->whereIn('discipline_class_id', $classes)
                ->where('period_type_id',$period)
                ->whereBetween('start_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                ->orderBy('schedule_type_id', 'ASC')->get();

                if (count($schedule_id) > 0) {

                    $events_by_type = [];
                    $schedule_id->groupBy('type.id')->each(function ($item, $key) use (&$events_by_type) {
                        $events = collect([]);

                        $item->each(function ($item, $key) use (&$events) {
                            $events = $events->merge($item->events);
                        });
                        $events_by_type[$key] = $events;
                    });


                    $days_of_the_week = DayOfTheWeek::with([
                        'currentTranslation'
                    ])
                    ->WhereNull("deleted_at")
                    ->get();

                    $schedule_types = ScheduleType::with([
                        'times',
                        'currentTranslation'
                    ])->get();
                    $sc = new SchedulesController();

                    $teacher_discipline = $sc->get_teacher_discipline($schedule_id);

                    return [
                        'schedule_types' => $schedule_types,
                        'days_of_the_week' => $days_of_the_week,
                        'user' => $user,
                        'events_by_type' => $events_by_type,
                        'schedule_id' => $schedule_id,
                        'lectiveYearSelected' => $lectiveYearSelected->id,
                        'lectiveYears' => $lectiveYears,
                        'teacher_discipline' => $teacher_discipline

                    ];

                }
                else {
                    return 1;
                }

            }
            else {
                return 2;
            }

        } catch (Exception | Throwable $e) {
            Log::error($e);
            return $e;
        }

        }

    public static function times($student = null)
    {

        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
            ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
            ->first();

        if (isset($student)) {
        } else {
            $student = auth()->user()->id;
        }

        $matriculations = DB::table("matriculations as mat")
            ->join("matriculation_classes as mat_class", 'mat.id', 'mat_class.matriculation_id')
            ->where("mat.user_id", $student)
            ->whereNull("mat.deleted_at")
            ->where("mat.lective_year", $lectiveYearSelected->id)
            ->select(["mat.lective_year", "mat.id", "mat_class.class_id"])
            ->orderBy("mat.lective_year", "asc")
            ->first();

        if (!isset($matriculations->lective_year)) {
            return "Nenhuma matrícula encontrada neste ano lectivo";
        }

        $class = DB::table("classes")
            ->where("id", $matriculations->class_id)
            ->select(["id", "schedule_type_id", "room_id"])
            ->first();

        if (!isset($class->schedule_type_id)) {
            return "Nenhuma turma encontrada";
        }

        return DB::table('schedule_type_times')
            ->where('schedule_type_id', $class->schedule_type_id)
            ->whereNull('deleted_at')
            ->select(["start", "end"])
            ->orderBy('start')
            ->get();
    }

    public static function verificar_pauta($turma, $disciplina, $lective, $pauta)
    {

        $pautas = DB::table("publicar_pauta")
            ->where('id_turma', $turma)
            ->where('id_disciplina', $disciplina)
            ->where('id_ano_lectivo', $lective)
            // ->where('estado',1)
            ->where('Pauta_tipo', "like", $pauta)
            ->get();

        return count($pautas);
    }

    public function get_boletim_student(Request $request, $lective_year = null)
    {

        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
            ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
            ->first();
        $lectiveYearSelected_id = $lectiveYearSelected->id ?? 11;

        if (isset($lective_year)) {
            $lectiveYearSelected_id = $lective_year;
        }

        $student = auth()->user()->id;



        $matriculations = DB::table("matriculations")
            ->where("user_id", $student)
            ->whereNull("deleted_at")
            ->where("lective_year", $lectiveYearSelected_id)
            ->select(["lective_year", "id"])
            ->orderBy("lective_year", "asc")
            ->first();

            
        if (!isset($matriculations->lective_year)) {
            return "Nenhuma matrícula encontrada neste ano lectivo";
        }

        $courses = DB::table("user_courses")
            ->where("users_id", $student)
            ->select(["courses_id"])
            ->first();

        if (!isset($courses->courses_id)) {
            return "Nenhum curso associado";
        }



        $disciplines = $this->get_disciplines($lectiveYearSelected_id);
        $percurso = BoletimNotas_Student($matriculations->lective_year, $courses->courses_id, $matriculations->id);
        //dd($percurso);
        $percurso =  $percurso->map(function ($grupo) {

            return $grupo->reject(function ($avl) use ($grupo) {
                $faltou =  isset($avl->presence);
                $nota_normal = !isset($avl->segunda_chamada);

                $fez_segunda_chamada = $grupo->where('user_id', $avl->user_id)
                    ->where('Disciplia_id', $avl->Disciplia_id)
                    ->where('Avaliacao_aluno_Metrica', $avl->Avaliacao_aluno_Metrica)
                    ->where('Avaliacao_aluno_turma', $avl->Avaliacao_aluno_turma)
                    ->where('segunda_chamada', 1)
                    ->isNotEmpty();


                $sai =  $faltou && $nota_normal && $fez_segunda_chamada;


                return $sai;
            });
        });

        $articles = $this->get_payments($lective_year);
        $plano = $this->study_plain($lective_year);
        $matriculations = $this->get_matriculation_student($lectiveYearSelected_id);
        $config = DB::table('avalicao_config')->where('lective_year',$lective_year)->first();
        $classes = $this->matriculation_classes($matriculations->id);
        $melhoria_notas = get_melhoria_notas($student, $lectiveYearSelected_id, 0);
        
        if($student == 461){
             dd([
                'percurso' => $percurso,
                'articles' => $articles,
                'plano' => $plano,
                'matriculations' => $matriculations,
                'disciplines' => $disciplines,
                'student' => $student,
                'config' => $config,
                'classes' => $classes,
                'melhoria_notas' => $melhoria_notas
            ]);
        }

        Log::info('CONFIG DEBUG1.2', ['config' => $config]);
        $html = view("Cms::initial.components.boletim", compact("percurso", "articles", "plano", "matriculations", "disciplines", "student", "config", "classes", "melhoria_notas"))->render();

        return response()->json($html);
    }

    public function get_schedule_student($lective_year){

        $d = $this->schedule($lective_year);
        $schedule_types = $d['schedule_types'];
        $days_of_the_week = $d['days_of_the_week'];
        $user = $d['user'];
        $events_by_type = $d['events_by_type'];
        $schedule_id = $d['schedule_id'];
        $lectiveYearSelected = $d['lectiveYearSelected'];
        $lectiveYears = $d['lectiveYears'];
        $teacher_discipline = $d['teacher_discipline'];

        $html = view("GA::schedules.partials.schedule_student",
        compact(
            'schedule_types',
            'days_of_the_week',
            'user',
            'events_by_type',
            'schedule_id',
            'lectiveYearSelected',
            'lectiveYears',
            'teacher_discipline'
        ))->render();
        return response()->json($html);

}
    public function matriculation_classes($matriculation_id)
    {
        return DB::table('matriculation_classes')
            ->where('matriculation_id', $matriculation_id)
            ->join('classes', 'classes.id', 'matriculation_classes.class_id')
            ->get();
        
    }

    public function get_matriculation_id($whatsapp)
    {
        //$whatsapp = '945347861';
        $matriculationId = DB::table('matriculations as m')
            ->join('users as u', 'm.user_id', '=', 'u.id')
            ->where('u.user_whatsapp', $whatsapp)
            ->value('m.id'); //->pluck('m.id');  //Todos

        return $this->boletim_pdf($matriculationId);
    }

    public function boletim_pdf($matriculation) //$whatsapp
    {
        //$whatsapp = $request->input('whatsapp');
        //$matriculation = $request->input('matriculation');

        // Verifica se o usuário é um pedido de API
        // Se for, valida o token e obtém a matrícula pelo WhatsApp, se necessário
        // Se não for, continua com a matrícula fornecida ou a do usuário autenticado

        $isApiRequest = request()->header('X-From-API') === 'flask';
        $tokenRecebido = request()->bearerToken();

        if ($isApiRequest) {
            // validar token
            if ($tokenRecebido !== env('FLASK_API_TOKEN')) {
                return response('Não autorizado', 401);
            }

        }

        // Verifica se o usuário está autenticado
        $matriculations = DB::table("matriculations")
        ->where("id", $matriculation)
        ->whereNull("deleted_at")
        ->select(["lective_year", "id", "user_id"])
        ->orderBy("lective_year", "asc")
        ->first();

        if (!isset($matriculations->lective_year)) {
            return response("Nenhuma matrícula encontrada neste ano lectivo", 404);
        }

        // A partir daqui é igual para ambos os casos
        $courses = DB::table("user_courses")
            ->where("users_id", $matriculations->user_id)
            ->select(["courses_id"])
            ->first();

        $student_info = $this->get_matriculation_student($matriculations->lective_year, $matriculations->user_id);
        $disciplines = $this->get_disciplines($matriculations->lective_year, $matriculations->user_id);
        $percurso = BoletimNotas_Student($matriculations->lective_year, $courses->courses_id, $matriculations->id);

        $percurso =  $percurso->map(function ($grupo) {
            return $grupo->reject(function ($avl) use ($grupo) {
                $faltou = isset($avl->presence);
                $nota_normal = !isset($avl->segunda_chamada);
                $fez_segunda_chamada = $grupo->where('user_id', $avl->user_id)
                    ->where('Disciplia_id', $avl->Disciplia_id)
                    ->where('Avaliacao_aluno_Metrica', $avl->Avaliacao_aluno_Metrica)
                    ->where('Avaliacao_aluno_turma', $avl->Avaliacao_aluno_turma)
                    ->where('segunda_chamada', 1)
                    ->isNotEmpty();
                return $faltou && $nota_normal && $fez_segunda_chamada;
            });
        });

        $articles = $this->get_payments($matriculations->lective_year, $matriculations->user_id);
        $plano = $this->study_plain($matriculations->lective_year, $matriculations->user_id);
        $config = DB::table('avalicao_config')->where('lective_year', $matriculations->lective_year)->first();
        $melhoria_notas = get_melhoria_notas($matriculations->user_id, $matriculations->lective_year, 0);
        $classes = $this->matriculation_classes($matriculations->id);
        $institution = Institution::latest()->first();
        $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
        Log::info('CONFIG DEBUG2', ['config' => $config]);
        $pdf = PDF::loadView("Cms::initial.pdf.boletim", compact(
            "percurso", "articles", "plano", "matriculations",
            "disciplines", "student_info", "institution", "config",
            "classes", "melhoria_notas"
        ))
            ->setOption('margin-top', '2mm')
            ->setOption('margin-left', '2mm')
            ->setOption('margin-bottom', '13mm')
            ->setOption('margin-right', '2mm')
            ->setOption('footer-html', $footer_html)
            ->setPaper('a4', 'landscape');

    // aqui Ezequiel
    if ($isApiRequest){

        return response($pdf->output(), 200)->header('Content-Type', 'application/pdf')->header('Content-Disposition', 'inline; filename="Boletim.pdf"');

    }

    // termina aqui

        // Senão, devolve via stream (para navegador)
        return $pdf->stream('Boletim_de_notas_' . $student_info->matricula . '_' . $student_info->lective_year . '.pdf');
    }



    public static function study_plain($lective_year = null, $student = null)
    {

        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
            ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
            ->first();

        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
            ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
            ->first();
        $lectiveYearSelected_id = $lectiveYearSelected->id ?? 6;

        if (isset($lective_year)) {
            $lectiveYearSelected_id = $lective_year;
        }

        if (isset($student)) {
        } else {
            $student = auth()->user()->id;
        }


        $matriculations = DB::table("matriculations as mat")
            ->join("matriculation_classes as mat_class", 'mat.id', 'mat_class.matriculation_id')
            ->where("mat.user_id", $student)
            ->whereNull("mat.deleted_at")
            ->where("mat.lective_year", $lectiveYearSelected->id)
            ->select(["mat.lective_year", "mat.id", "mat_class.class_id"])
            ->orderBy("mat.lective_year", "asc")
            ->first();

        if (!isset($matriculations->lective_year)) {
            return "Nenhuma matrícula encontrada neste ano lectivo";
        }

        return $plano = DB::table('study_plans as plano')
            ->join("study_plan_translations as p_t", "p_t.study_plans_id", "=", "plano.id")
            ->join("study_plan_editions as spe", "spe.study_plans_id", "=", "plano.id")
            ->join("courses as curso", "curso.id", "=", "plano.courses_id")
            ->join("courses_translations as ct", 'ct.courses_id', 'curso.id')
            ->join("classes", "classes.courses_id", "=", "curso.id")
            ->join("period_type_translations as ptt", 'spe.period_type_id', 'ptt.period_types_id')
            ->where("ptt.active", 1)
            ->where("ptt.language_id", 1)
            ->where("p_t.active", 1)
            ->where("p_t.language_id", 1)
            ->where("ct.active", 1)
            ->where("ct.language_id", 1)
            // ->whereDate('spe.start_date', '<=', $currentData)
            // ->whereDate('spe.end_date', '>=', $currentData)
            ->where("spe.lective_years_id", $matriculations->lective_year)
            ->where("classes.id", $matriculations->class_id)
            ->whereNull("plano.deleted_at")
            ->whereNull("ptt.deleted_at")
            ->whereNull("p_t.deleted_at")
            ->select([
                'spe.id as ed_id',
                'plano.courses_id as curso_id',
                'p_t.display_name as nome_plano',
                'ct.display_name as nome_curso',
                'curso.code as curso_code',
                'spe.period_type_id as semestre_id',
                'spe.course_year as year',
                "ptt.display_name as semestre",
                "classes.display_name as turma"
            ])
            ->orderBy("ptt.display_name", "desc")
            ->get();
    }


    //ATENÇAÕ REGIÃO CRÍTICA

    public function get_classes_grades($class_id,$lectiveYearSelected){


        try{

            $matriculations = $this->get_all_matriculation_student($lectiveYearSelected, $class_id);
            $data = [];
            foreach($matriculations as $key=>$item){
              $result = $this->get_boletim_student_new($lectiveYearSelected, $item->user_id);
                 Log::info('CONFIG DEBUG3', ['config' => $result]);
              if(!empty($result))
              $data[$key] = [
                "user_id"=> $item->user_id,
                "boletim" => $result,
              ];
            }



         return response()->json($data);

        }
       catch(Exception $e){
          dd($e);
       }
  }

  public function update_percurso_grades(Request $request){



    try{
        DB::beginTransaction();
        $data = $request->json()->all();

        foreach ($data as $key => $aluno) {
            $userId = $aluno['user_id'];

            foreach ($aluno['boletim'] as $disciplina) {
                $disciplinaId = $disciplina['discipline_id'];
                $codigo = $disciplina['codigo'];
                $nomeDisciplina = $disciplina['disciplina'];
                $notaFinal = $disciplina['nota_final'];
                $notaPercurso = $disciplina['nota_percurso'];


                $Percurso = DB::table('new_old_grades')->updateOrInsert(
                    [
                        'user_id' => $userId,
                        'discipline_id' => $disciplinaId,
                    ],
                    [
                        'grade' => $notaFinal,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                        "created_by" => 23
                    ]
                );
            }
        }
        DB::commit();
        // Retorno de sucesso
        return response()->json([
            'success' => true,
            'message' => 'Dados processados com sucesso.'
        ], 200);

    }
   catch(Exception $e){
    DB::rollBack();
    Log::error($e);
    return response()->json($e->getMessage(), 500);
   }
}


  private function get_all_matriculation_student($lective_year=null, $class_id){




      $emolumento_confirma_prematricula= mainController::pre_matricula_confirma_emolumento($lective_year);

      return $model = Matriculation::leftJoin('matriculation_classes as mc', 'mc.matriculation_id', '=', 'matriculations.id')
              ->join('classes as cl', function ($join)  {
                  $join->on('cl.id', '=', 'mc.class_id');
                  $join->on('mc.matriculation_id', '=', 'matriculations.id');
                  $join->on('matriculations.course_year', '=', 'cl.year');
              })

            ->leftJoin('article_requests as art_requests',function ($join) use($emolumento_confirma_prematricula)
              {
                  $join->on('art_requests.user_id','=','matriculations.user_id')
                  ->whereIn('art_requests.article_id', $emolumento_confirma_prematricula);
              })

              ->join('matriculation_disciplines as mat_disc','mat_disc.matriculation_id','matriculations.id')


              ->select([
                  'matriculations.*'
              ])
              ->where('art_requests.deleted_by', null)
              ->where('art_requests.deleted_at', null)
              ->where('matriculations.lective_year', $lective_year)
              ->where('mc.class_id',$class_id)


              ->distinct('matriculations.user_id')
              ->get();
  }


  public function get_boletim_student_new($lective_year=null, $student=null){

     $currentData = Carbon::now();
       $lectiveYearSelected = DB::table('lective_years')
      ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
      ->first();
      $lectiveYearSelected_id = $lectiveYearSelected->id ?? 6;

      if(isset($lective_year)){
          $lectiveYearSelected_id = $lective_year;
      }

      $matriculations = DB::table("matriculations")
      ->where("user_id",$student)
      ->whereNull("deleted_at")
      ->where("lective_year",$lectiveYearSelected_id)
      ->select(["lective_year","id", "user_id"])
      ->orderBy("lective_year","asc")
      ->first();

      if(!isset($matriculations->lective_year)){
          return "Nenhuma matrícula encontrada neste ano lectivo";
      }

      $courses = DB::table("user_courses")
      ->where("users_id",$student)
      ->select(["courses_id"])
      ->first();

      if(!isset($courses->courses_id)){
          return "Nenhum curso associado";
      }


      $disciplines = $this->get_disciplines($lectiveYearSelected_id, $student);
      $percurso = BoletimNotas_Student($matriculations->lective_year, $courses->courses_id, $matriculations->user_id);
      $matriculations = $this->get_matriculation_student($lective_year, $student, $matriculations->user_id);
      $config = DB::table('avalicao_config')->where('lective_year',$lective_year)->first();
      $melhoria_notas = get_melhoria_notas($student, $lective_year, 0);

     $notas_percurso = DB::table('new_old_grades as nog')
     ->where('nog.user_id',$student)
     ->whereIn('discipline_id',$disciplines->pluck('id_disciplina'))
     ->get();
      Log::info('CONFIG DEBUG2.1', ['config' => $config]);


      $semestres = ['1'];
      $tabelaPorSemestre = [];

      foreach ($semestres as $semestre) {
          $dadosSemestre = [];

          foreach ($disciplines as $index => $disciplina) {
              if ($index[3] != $semestre) continue;

              // Lógica individual por disciplina
              $avaliacoes = $percurso[$index] ?? [];

              $dadosDisciplina = $this->processarDisciplina(
                $student,
                  $disciplina,
                  $avaliacoes,
                  $index,
                  $melhoria_notas,
                  $config,
                  $notas_percurso
              );
               Log::info('CONFIG DEBUG2.2', ['config' => $config]);



              $temNotaFinal = $dadosDisciplina['nota_final'] !== null;

$temNotaPercurso = $dadosDisciplina['nota_percurso'] !== null;

$notasSaoDiferentes = $dadosDisciplina['nota_final'] != $dadosDisciplina['nota_percurso'];

if (
    (
        ($temNotaFinal && !$temNotaPercurso) ||
        ($temNotaPercurso && $notasSaoDiferentes)
    )
){
                  $dadosSemestre[] = $dadosDisciplina;
              }

          }

      }

      return $dadosSemestre
      ;
  }

  protected function processarDisciplina($student,$disciplina, $avaliacoes, $index, $melhoria_notas, $config, $notas_percurso)
  {
      $notas = [
          'pf1' => null, 'pf2' => null, 'oa' => null,
          'neen' => null, 'oral' => null,
          'recurso' => null, 'especial' => null,
          'mac' => null, 'mac_estado' => '-', 'mac_cor' => '',
          'final' => '-', 'final_estado' => '-', 'final_cor' => '',
          'cf' => '-', "melhoria_nota" => null,'extra_nota' => null
      ];
       Log::info('CONFIG DEBUG2.3', ['config' => $config]);

      if($melhoria_notas->contains('discipline_id',$disciplina->id_disciplina)){
        $m = $melhoria_notas->where('discipline_id',$disciplina->id_disciplina)->first();

        $notas['melhoria_nota'] = !is_null($m->new_grade) ? $m->new_grade : null;
    }

    if($notas['melhoria_nota'] === null){
      $percentuais = ['pf1' => 0, 'pf2' => 0, 'oa' => 0];
      $mac_percent = $config->percentagem_mac / 100;
      $oral_percent = $config->percentagem_oral / 100;

      foreach ($avaliacoes as $aval) {
          $code = strtolower($aval->MT_CodeDV);
          $nota = $aval->nota_anluno !== null ? floatval($aval->nota_anluno) : null;
          $percentual = $aval->percentagem_metrica / 100;

          switch ($code) {
              case 'pf1':
                  $notas['pf1'] = $nota;
                  $percentuais['pf1'] = $percentual;
                  break;

              case 'pf2':
                  $notas['pf2'] = $nota;
                  $percentuais['pf2'] = $percentual;
                  break;

              case 'oa':
                  $notas['oa'] = $nota;
                  $percentuais['oa'] = $percentual;
                  break;

              case 'neen':
                  $notas['neen'] = round($nota ?? 0);
                  break;

              case 'oral':
                  $notas['oral'] = round($nota ?? 0);
                  break;

              case 'recurso':
                  $notas['recurso'] = round($nota ?? 0);
                  break;

              case 'exame_especial':
                  $notas['especial'] = round($nota ?? 0);
                  break;
              case 'extraordinario':
                    $notas['extra_nota'] = round($nota ?? 0);
                    break;
              default:
                  // Nenhuma ação necessária
                  break;
          }

      }

      // Calcular MAC
      $mac_calculado = (
          ($notas['pf1'] * $percentuais['pf1']) +
          ($notas['pf2'] * $percentuais['pf2']) +
          ($notas['oa'] * $percentuais['oa'])
      );

      $mac_calculado = round($mac_calculado);
      $notas['mac'] = $mac_calculado;
      $notas['final'] = $mac_calculado;

      if ($mac_calculado >= $config->mac_nota_dispensa) {
          $notas['mac_estado'] = 'Aprovado(a)';
          $notas['mac_cor'] = 'for-green';
          $notas['final_estado'] = 'Aprovado(a)';
          $notas['final_cor'] = 'for-green';
      } elseif ($mac_calculado >= $config->exame_nota_inicial) {
          $notas['mac_estado'] = 'Exame';
          $notas['mac_cor'] = 'for-yellow';
          $notas['final_estado'] = 'Exame';
          $notas['final_cor'] = 'for-yellow';
      } else {
          $notas['mac_estado'] = 'Recurso';
          $notas['mac_cor'] = 'for-red';
          $notas['final_estado'] = 'Recurso';
          $notas['final_cor'] = 'for-red';

      }

      // Calcular final se necessário
      if ($notas['mac_estado'] === 'Exame' && $notas['neen'] !== null) {
          $classificacao = round(
              ($mac_calculado * $mac_percent) +
              ($notas['neen'] * $oral_percent)
          );

          $notas['final'] = $classificacao;
          $notas['cf'] = $classificacao;

          if ($classificacao >= $config->exame_nota) {
              $notas['final_estado'] = 'Aprovado(a)';
              $notas['final_cor'] = 'for-green';
          } else {
              $notas['final_estado'] = 'Recurso';
              $notas['final_cor'] = 'for-red';
          }

      }

      if ($notas['final_estado'] === 'Recurso' && $notas['recurso'] !== null) {
          $classificacao = $notas['recurso'];

          $notas['final'] = $classificacao;
          if ($classificacao >= $config->exame_nota) {
              $notas['final_estado'] = 'Aprovado(a)';
              $notas['final_cor'] = 'for-green';
          } else {
              $notas['final_estado'] = 'Reprovado(a)';
              $notas['final_cor'] = 'for-red';
          }

      }

      if ($notas['final_estado'] === 'Reprovado(a)' && $notas['especial'] !== null) {
          $classificacao = $notas['especial'];
          $notas['final'] = $classificacao;
          if ($classificacao >= $config->exame_nota) {
              $notas['final_estado'] = 'Aprovado(a)';
              $notas['final_cor'] = 'for-green';
          } else {
              $notas['final_estado'] = 'Reprovado(a)';
              $notas['final_cor'] = 'for-red';
          }

      }

      if ($notas['final_estado'] === 'Reprovado(a)' && $notas['extra_nota'] !== null) {
        $classificacao = $notas['extra_nota'];

        $notas['final'] = $classificacao;
        if ($classificacao >= $config->exame_nota) {
            $notas['final_estado'] = 'Aprovado(a)';
            $notas['final_cor'] = 'for-green';
        } else {
            $notas['final_estado'] = 'Reprovado(a)';
            $notas['final_cor'] = 'for-red';
        }


        }

   }
   else{
    $notas['final'] = $notas['melhoria_nota'];
   }

      $nota_percurso = $notas_percurso->filter(function($item) use($disciplina){
          return $item->discipline_id == $disciplina->id_disciplina;
    })->first();


      return [
        'discipline_id' => $disciplina->id_disciplina,
          'codigo' => $index,
          'disciplina' => $disciplina->nome_disciplina,
          'nota_final' => $notas['final'],
          'nota_percurso' => $nota_percurso->grade ?? null,
      ];
  }
}
