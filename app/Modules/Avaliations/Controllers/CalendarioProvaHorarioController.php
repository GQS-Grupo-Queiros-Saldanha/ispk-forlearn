<?php

namespace App\Modules\Avaliations\Controllers;

use App\Modules\Avaliations\Models\CalendarioProvaHorarioJuris;
use App\Modules\Avaliations\util\CalendarioProvaHorarioUtil;
use App\Modules\Avaliations\Models\CalendarioProvaHorario;
use App\Modules\Avaliations\Models\PlanoEstudoAvaliacao;
use Yajra\DataTables\Facades\DataTables;
use App\Modules\GA\Models\LectiveYear;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use App\Modules\GA\Models\Course;
use App\Helpers\LanguageHelper;
use Illuminate\Http\Request;
use App\Model\Institution;
use Carbon\Carbon;
use Exception;
use DateTime;
use Auth;
use PDF;
use DB;

class CalendarioProvaHorarioController extends Controller
{

    private function currentDate()
    {
        $currentData = Carbon::now();
        $lectiveYears = LectiveYear::with(['currentTranslation'])->get();
        $lectiveYearSelected = DB::table('lective_years')->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')->first();
        return (object)["currentData" => $currentData, "lectiveYears" => $lectiveYears, "lectiveYearSelected" => $lectiveYearSelected];
    }

    private function user_teacher()
    {
        return DB::table('users as usuario')
            ->join('model_has_roles as usuario_cargo', 'usuario.id', '=', 'usuario_cargo.model_id')
            ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')
            ->join('user_parameters as up', 'up.users_id', '=', 'usuario.id')
            ->where('up.parameters_id', 1)
            ->where('usuario_cargo.role_id', '=', '1')
            ->whereNull('usuario.deleted_at')
            ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
            ->select(['usuario.id as id_usuario', 'usuario.email as email_usuario', 'up.value as nome_usuario', 'cargo.id as cargo_usuario'])
            ->orderBy('nome_usuario')
            ->groupBy('id_usuario')
            ->get();
    }

    public function index()
    {
        $timeData = $this->currentDate();
        return view("Avaliations::calendario-horario.index", [
            'lectiveYears' => $timeData->lectiveYears,
            'lectiveYearSelected' => $timeData->lectiveYearSelected
        ]);
    }

    public function calendario()
    {
        $institution = Institution::latest()->first();
        return view("Avaliations::calendario-horario.calendario-prova-slc", [
            "institution" => $institution
        ]);
    }

    public function search_prova(Request $request)
    {
        $timeData = $this->currentDate();
        $courses = Course::with('currentTranslation')->get();
        return view("Avaliations::calendario-horario.search_prova", [
            'lectiveYears' => $timeData->lectiveYears,
            'lectiveYearSelected' => $timeData->lectiveYearSelected,
            'courses' => $courses,
        ]);
    }

    public function create()
    {
        $timeData = $this->currentDate();
        $courses = Course::with('currentTranslation')->get();
        $teachers = $this->user_teacher();
        return view("Avaliations::calendario-horario.formulario", [
            'lectiveYears' => $timeData->lectiveYears,
            'lectiveYearSelected' => $timeData->lectiveYearSelected,
            'courses' => $courses,
            'teachers' => $teachers,
            'action' => 'create',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            "calendario_prova_id" => "required",
            "hora_comeco" => "required",
            "hora_termino" => "required",
            "disciplina_id" => "required",
            "periodo" => "required",
            "turma_id" => "required",
            "data_prova_marcada" => "required",
        ]);
        try {
            $calenderProva = DB::table('calendario_prova')->where('calendario_prova.deleted_at', null)
                ->find($request->calendario_prova_id);

            $dataEnd = new DateTime($calenderProva->data_end);
            $dataStart = new DateTime($calenderProva->date_start);
            $dataMarcada = new DateTime($request->data_prova_marcada);

            if (isset($calenderProva->id) &&  !($dataStart <= $dataMarcada && $dataEnd >= $dataMarcada)) {
                Toastr::warning("A data marcada para a prova ($request->data_prova_marcada) não faz parte do intervalo de calendário de prova ({$calenderProva->date_start} à {$calenderProva->data_end}).", __('toastr.warning'));
                return redirect()->back();
            }

            $horaComeco = new DateTime($request->hora_comeco);
            $horaTermino = new DateTime($request->hora_termino);

            if ($horaComeco >= $horaTermino) {
                Toastr::warning("A hora de começo ({$request->hora_comeco}) não pode ser maior que a hora de termino ($request->hora_termino).", __('toastr.warning'));
                return redirect()->back();
            }

            $calenderHorario =  CalendarioProvaHorarioUtil::horaInteval($request, $request->hora_comeco);
            if (isset($calenderHorario->id)) {
                Toastr::warning("Esta hora de começo ({$request->hora_comeco}) nesta data ({$request->data_prova_marcada}) já se encontrada agendada na disciplina({$calenderHorario->discipline}) em {$calenderHorario->hora_comeco} à {$calenderHorario->hora_termino}", __('toastr.warning'));
                return redirect()->back();
            }

            $calenderHorario = CalendarioProvaHorarioUtil::horaInteval($request, $request->hora_termino);
            if (isset($calenderHorario->id)) {
                Toastr::warning("Esta hora de termino ({$request->hora_termino}) nesta data ({$request->data_prova_marcada}) já se encontrada agendada na disciplina({$calenderHorario->discipline}) em {$calenderHorario->hora_comeco} à {$calenderHorario->hora_termino}", __('toastr.warning'));
                return redirect()->back();
            }

            $calenderHorario = CalendarioProvaHorario::where([
                "disciplina_id" => $request->disciplina_id,
                "calendario_prova_id" => $request->calendario_prova_id,
                "periodo" => $request->periodo
            ])->whereNull('deleted_at')->whereNull('deleted_by')->first();

            if (isset($calenderHorario->id)) {
                Toastr::warning("Esta discipliana já se encontra agendado neste calendário.", __('toastr.warning'));
                return redirect()->back();
            }

            $diaDaSemana = date('N', strtotime($request->data_prova_marcada));

            if (!($diaDaSemana >= 1 && $diaDaSemana <= 5)) {
                Toastr::warning("O dia da semana da data marcada não é um dia de trabalho na semana, isto quer dizer que é (sábado ou domingo).", __('toastr.warning'));
                return redirect()->back();
            }

            $disciplina = DB::table('disciplines_translations')
                ->where('discipline_id', $request->disciplina_id)
                ->where('active', 1)
                ->first();

            $cursoAndTurma = DB::table('classes as c')->join('courses_translations as ct', 'c.courses_id', '=', 'ct.courses_id')
                ->where('c.id', $request->turma_id)
                ->where('ct.active', 1)
                ->select('c.display_name as turma', 'ct.display_name as curso', 'ct.courses_id as curso_id', 'c.id as turma_id', 'c.year')
                ->first();

            DB::transaction(function () use ($request, $calenderProva, $disciplina, $cursoAndTurma) {
                $data = $request->all();
                //dd($data);
                $calenderFind = CalendarioProvaHorario::where([
                    "turma_id" => $data["turma_id"],
                    "disciplina_id" => $data["disciplina_id"],
                    "calendario_prova_id" => $data["calendario_prova_id"],
                    "periodo" => $data["periodo"],
                ])->whereNull('deleted_at')->whereNull('deleted_by')->first();

                CalendarioProvaHorarioUtil::notificationMatriculationClass($calenderProva, $disciplina, $cursoAndTurma, $data['periodo']);
                CalendarioProvaHorarioUtil::notificationMatriculationDisciplinaClass($calenderProva, $disciplina, $cursoAndTurma, $data['periodo']);

                $id_calendario = null;
                if (!isset($calenderFind->id)) {
                    $data["created_at"] = Carbon::now();
                    $data["updated_at"] = Carbon::now();
                    $data["created_by"] = Auth::user()->id;
                    $data["updated_by"] = Auth::user()->id;
                    $calender = CalendarioProvaHorario::create($data);
                    $id_calendario = $calender->id;
                } else {
                    $data["created_by"] = Auth::user()->id;
                    $data["updated_by"] = Auth::user()->id;
                    $calenderFind->update($data);
                    $id_calendario = $calenderFind->id;
                }

                if ($id_calendario != null && isset($request->juris)) {
                    foreach ($request->juris as $juri) {
                        $calenderJuri = CalendarioProvaHorarioJuris::where([
                            "user_id" => $juri,
                            "calendario_horario_id" => $id_calendario
                        ])->first();
                        $data = [];
                        if (!isset($calenderJuri->id)) {
                            $data["user_id"] = $juri;
                            $data["created_at"] = Carbon::now();
                            $data["updated_at"] = Carbon::now();
                            $data["created_by"] = Auth::user()->id;
                            $data["updated_by"] = Auth::user()->id;
                            $data["calendario_horario_id"] = $id_calendario;
                            CalendarioProvaHorarioJuris::create($data);
                            CalendarioProvaHorarioUtil::notificationJuri($calenderProva, $disciplina, $cursoAndTurma, $juri, $request->periodo);
                        } else {
                            $data = [
                                "user_id" => $juri,
                                "updated_at" => Carbon::now(),
                                "updated_by" => Auth::user()->id,
                            ];
                            $calenderJuri->update($data);
                        }
                    }
                }
            });
            Toastr::success("Agendado com successo.", __('toastr.success'));
            return redirect()->back();
        } catch (Exception $e) {
            dd($e);
            Toastr::error($e->getMessage(), __('toastr.error'));
            return redirect()->back();
        }
    }

    public function edit($id)
    {
        $timeData = $this->currentDate();
        $courses = Course::with('currentTranslation')->get();
        $teachers = $this->user_teacher();

        $calendarioHorario = CalendarioProvaHorario::with('calendario_horario_juris')
            ->join('calendario_prova', 'calendario_prova_id', '=', 'calendario_prova.id')
            ->join('classes', 'turma_id', '=', 'classes.id')
            ->select("calendario_horario.*", "classes.courses_id as course_id","calendario_prova.simestre","classes.year")
            ->find($id);
        
        //dd($calendarioHorario);

        $juris = $calendarioHorario->calendario_horario_juris->map(function ($item) {
            return $item->user_id;
        })->all();

        return view("Avaliations::calendario-horario.formulario", [
            'lectiveYears' => $timeData->lectiveYears,
            'lectiveYearSelected' => $timeData->lectiveYearSelected,
            'courses' => $courses,
            'teachers' => $teachers,
            'calendarioHorario' => $calendarioHorario,
            'action' => 'edit',
            'juris' => $juris,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            "calendario_prova_id" => "required",
            "hora_comeco" => "required",
            "hora_termino" => "required",
            "disciplina_id" => "required",
            "periodo" => "required",
            "turma_id" => "required",
            "data_prova_marcada" => "required",
        ]);

        try {
            $calenderProva = DB::table('calendario_prova')->where('calendario_prova.deleted_at', null)
                ->find($request->calendario_prova_id);

            $dataEnd = new DateTime($calenderProva->data_end);
            $dataStart = new DateTime($calenderProva->date_start);
            $dataMarcada = new DateTime($request->data_prova_marcada);

            $calenderFind = CalendarioProvaHorario::find($id);

            if (isset($calenderProva->id) &&  !($dataStart <= $dataMarcada && $dataEnd >= $dataMarcada)) {
                Toastr::warning("A data marcada para a prova ($request->data_prova_marcada) não faz parte do intervalo de calendário de prova ({$calenderProva->date_start} à {$calenderProva->data_end}).", __('toastr.warning'));
                return redirect()->back();
            }

            $horaComeco = new DateTime($request->hora_comeco);
            $horaTermino = new DateTime($request->hora_termino);

            if ($horaComeco >= $horaTermino) {
                Toastr::warning("A hora de começo ({$request->hora_comeco}) não pode ser maior que a hora de termino ($request->hora_termino).", __('toastr.warning'));
                return redirect()->back();
            }

            $calenderHorario = CalendarioProvaHorarioUtil::horaInteval($request, $request->hora_comeco);
            if (isset($calenderHorario->id) && $calenderHorario->id != $calenderFind->id) {
                Toastr::warning("Esta hora de começo ({$request->hora_comeco}) nesta data ({$request->data_prova_marcada}) já se encontrada agendada na disciplina({$calenderHorario->discipline}) em {$calenderHorario->hora_comeco} à {$calenderHorario->hora_termino}", __('toastr.warning'));
                return redirect()->back();
            }

            $calenderHorario = CalendarioProvaHorarioUtil::horaInteval($request, $request->hora_termino);
            if (isset($calenderHorario->id) && $calenderHorario->id != $calenderFind->id) {
                Toastr::warning("Esta hora de termino ({$request->hora_termino}) nesta data ({$request->data_prova_marcada}) já se encontrada agendada na disciplina({$calenderHorario->discipline}) em {$calenderHorario->hora_comeco} à {$calenderHorario->hora_termino}", __('toastr.warning'));
                return redirect()->back();
            }

            $calenderHorario = CalendarioProvaHorario::where([
                "disciplina_id" => $request->disciplina_id,
                "calendario_prova_id" => $request->calendario_prova_id,
                "periodo" => $request->periodo
            ])->whereNull('deleted_at')->whereNull('deleted_by')->first();

            if (isset($calenderHorario->id) && $calenderHorario->id != $calenderFind->id) {
                Toastr::warning("Esta discipliana já se encontra agendado neste calendário.", __('toastr.warning'));
                return redirect()->back();
            }

            $diaDaSemana = date('N', strtotime($request->data_prova_marcada));

            if (!($diaDaSemana >= 1 && $diaDaSemana <= 5)) {
                Toastr::warning("O dia da semana da data marcada não é um dia de trabalho na semana, isto quer dizer que é (sábado ou domingo).", __('toastr.warning'));
                return redirect()->back();
            }

            DB::transaction(function () use ($request, $calenderFind) {
                $data = $request->all();
                $data["created_by"] = Auth::user()->id;
                $data["updated_by"] = Auth::user()->id;
                $calenderFind->update($data);
                if (isset($request->juris)) {
                    foreach ($request->juris as $juri) {
                        $calenderJuri = CalendarioProvaHorarioJuris::where([
                            "user_id" => $juri,
                            "calendario_horario_id" => $calenderFind->id
                        ])->whereNull('deleted_at')->whereNull('deleted_by')->first();

                        $data = [];
                        if (!isset($calenderJuri->id)) {
                            $data["user_id"] = $juri;
                            $data["created_at"] = Carbon::now();
                            $data["updated_at"] = Carbon::now();
                            $data["created_by"] = Auth::user()->id;
                            $data["updated_by"] = Auth::user()->id;
                            $data["calendario_horario_id"] = $calenderFind->id;
                            CalendarioProvaHorarioJuris::create($data);
                        } else {
                            $data = [
                                "user_id" => $juri,
                                "updated_at" => Carbon::now(),
                                "updated_by" => Auth::user()->id,
                            ];
                            $calenderJuri->update($data);
                        }
                    }
                }
            });

            Toastr::success("Actualizado com successo.", __('toastr.success'));
            return redirect()->back();
        } catch (Exception $e) {
            Toastr::error($e->getMessage(), __('toastr.error'));
            return redirect()->back();
        }
    }

    public function juri_delete(Request $request)
    {
        try {
            $calendarioHorarioJuri = CalendarioProvaHorarioJuris::find($request->juri);
            $calendarioHorarioJuri->update([
                "deleted_at" => Carbon::now(),
                "deleted_by" => Auth::user()->id
            ]);
            Toastr::success("Juri eliminado com successo.", __('toastr.success'));
            return redirect()->back();
        } catch (Exception $e) {
            Toastr::error($e->getMessage(), __('toastr.error'));
            return redirect()->back();
        }
    }

    public function delete(Request $request)
    {
        try {
            $calendarioHorario = CalendarioProvaHorario::find($request->prova_horario);
            $calendarioHorario->update([
                "deleted_at" => Carbon::now(),
                "deleted_by" => Auth::user()->id
            ]);
            Toastr::success("Prova eliminado com successo.", __('toastr.success'));
            return redirect()->back();
        } catch (Exception $e) {
            Toastr::error($e->getMessage(), __('toastr.error'));
            return redirect()->back();
        }
    }

    public function ajax_juris(Request $request)
    {
        return CalendarioProvaHorarioUtil::juris($request->prova_horario ?? null, $request->periodo ?? null);
    }

    public function ajax_calendario_prova(Request $request)
    {
        return DB::table('calendario_prova as cp')
            ->where('cp.lectiveYear', $request->year_id ?? null)
            ->get();
    }

    private function get_class($request, $course, $year_course)
    {
        $query = DB::table('classes as c')
            ->join('courses_translations as ct', 'c.courses_id', '=', 'ct.courses_id')
            ->leftjoin('room_translations as rt', 'c.room_id', '=', 'rt.room_id')
            ->where('c.lective_year_id', $request->year ?? null)
            ->where('ct.active', 1)
            ->where('rt.active', 1)
            ->where('c.courses_id', $course)
            ->select('c.id as class_id', 'c.display_name as class', 'ct.display_name as course', 'rt.display_name as sala', 'ct.courses_id');
        if (isset($year_course)) {
            $query = $query->where('c.year', $year_course);
        }
        return $query->get();
    }

    public function ajax_class(Request $request)
    {
        $turma =  DB::table('classes as c')
            ->join('courses_translations as ct', 'c.courses_id', '=', 'ct.courses_id')
            ->leftjoin('room_translations as rt', 'c.room_id', '=', 'rt.room_id')
            ->where('c.lective_year_id', $request->year ?? null)
            ->where('ct.active', 1)
            ->where('rt.active', 1)
            ->select('c.id as class_id', 'c.display_name as class', 'ct.display_name as course', 'rt.display_name as sala', 'ct.courses_id')
            ->distinct();

        if (isset($request->year_course)) {
            $turma = $turma->where('c.year', $request->year_course);
        }

        if (isset($request->course)) {
            $turma = $turma->where('c.courses_id', $request->course);
        }
        if (isset($request->courses)) {
            $courses = explode(",", $request->courses);

            $array = [];
            foreach ($courses as $course) {
                $courseFind = Course::with('currentTranslation')->find($course);
                $array[] = (object)[
                    "course" => $courseFind,
                    "turmas" => $this->get_class($request, $course, $request->year_course ?? null)
                ];
            }
            return $array;
        }

        return $turma->get();
    }

    public function ajax_discipline(Request $request)
    {
        return PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
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
            ->leftJoin('user_disciplines', 'user_disciplines.disciplines_id', '=', 'dp.id')
            ->select([
                'crs.id as course_id',
                'ct.display_name as course_name',
                'dp.id as discipline_id',
                'dp.code as code',
                'dt.display_name as dt_display_name',
            ])
            ->where('stpeid.course_year', $request->yearCourse)
            ->where('dp.courses_id', $request->couser_id ?? null)
            ->distinct()
            ->get();
    }

    public function ajax_calendario_horario()
    {
        $model = CalendarioProvaHorario::join('calendario_prova', 'calendario_prova_id', '=', 'calendario_prova.id')
            ->leftjoin('disciplines_translations', 'discipline_id', '=', 'calendario_horario.disciplina_id')
            ->leftjoin('classes', 'turma_id', '=', 'classes.id')
            ->leftjoin('courses_translations', 'courses_translations.courses_id', '=', 'classes.courses_id')
            ->leftjoin('users as u1', 'u1.id', '=', 'calendario_horario.created_by')
            ->leftJoin('users as u2', 'u2.id', '=', 'calendario_horario.updated_by')
            ->where('disciplines_translations.active', 1)
            ->where('courses_translations.active', 1)
            ->whereNull('calendario_horario.deleted_at')
            ->whereNull('calendario_horario.deleted_by')
            ->select(
                "hora_termino",
                "hora_comeco",
                "simestre",
                "data_prova_marcada as data_marcada",
                "disciplines_translations.display_name as disciplina",
                "periodo",
                "classes.display_name as turma",
                "courses_translations.display_name as curso",
                "u1.name as us_created_by",
                "u2.name as us_updated_by",
                DB::raw("concat(date_start, ' à ' ,data_end) as intervalo"),
                "calendario_prova.display_name as regime",
                "calendario_horario.updated_at",
                "calendario_horario.created_at",
                "calendario_horario.id as id_horario",
                "classes.year"
            )->orderBy('calendario_horario.id', 'DESC')->get();
            return Datatables::of($model)->addColumn('actions', function ($item) {
                return view('Avaliations::calendario-horario.datatable.actions')->with('item', $item);
            })->rawColumns(['actions'])->addIndexColumn()->toJson();
    }

    public function search_prova_post(Request $request)
    {
        try {
            $horarioProvas = [];
            $institution = Institution::latest()->first();
            $simestre = $request->simestre;
            $anoCurricular = $request->year_course ?? 0;
            $dataJoin = explode("@", $request->turma);
            $curso = Course::with('currentTranslation')->find($dataJoin[1]);
            $turmaSala = DB::table('classes')->leftjoin('room_translations as rt', 'classes.room_id', '=', 'rt.room_id')
                ->where('rt.active', 1)->where('classes.id', $request->turma)
                ->select("classes.display_name as turma", 'rt.display_name as sala')
                ->first();

            $periodos = $request->periodos ?? (isset($request->periodo) ? [$request->periodo] : []);
            
            foreach ($periodos as $periodo) {
                $horarios = CalendarioProvaHorarioUtil::getMarcacao(
        $dataJoin[0],$anoCurricular,$periodo,$simestre ?? null
                );
                if(sizeof($horarios) > 0){
                    $horarioProvas[$periodo] =  (object)[
                        "curso" => $curso, "simestre" => $simestre, "turmaSala" => $turmaSala,
                        "anoCurricular" => $anoCurricular, "horarios" => $horarios
                    ];
                }
            }

            if(sizeof($horarioProvas) == 0){
                Toastr::warning("Não foi possível encontra uma prova marcada para a turma {$turmaSala->turma}.", __('toastr.warning'));
                return redirect()->back();
            }

            $pdf = PDF::loadView("Avaliations::calendario-horario.calendario-prova-slc", [
                "institution" => $institution,
                "horarioProvas" => $horarioProvas,
            ]);
            $pdf->setOption('margin-top', '1mm');
            $pdf->setOption('margin-left', '1mm');
            $pdf->setOption('margin-bottom', '2cm');
            $pdf->setOption('margin-right', '1mm');
            $pdf->setOption('enable-javascript', true);
            $pdf->setOption('debug-javascript', true);
            $pdf->setOption('javascript-delay', 1000);
            $pdf->setOption('enable-smart-shrinking', true);
            $pdf->setOption('no-stop-slow-scripts', true);
            $pdf->setPaper('a4', 'landscape');
            return $pdf->stream();
        } catch (Exception $e) {
            Toastr::error($e->getMessage(), __('toastr.error'));
            return redirect()->back();
        }
    }
    
}