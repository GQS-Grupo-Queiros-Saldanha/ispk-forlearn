<?php
namespace App\Modules\Users\Controllers;

use App\Helpers\LanguageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentState;
use App\Modules\Payments\Models\ArticleRequest;
use App\Modules\Users\Models\Matriculation;
use App\Modules\Users\Models\SchedulingState;
use App\Modules\Users\Models\State;
use App\Modules\Users\Models\User;
use App\Modules\Users\Models\UserState;
use App\Modules\Users\Models\UserStateHistoric;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class StudentStatesController extends Controller
{
    public function index()
    {
        try {
            return view('Users::states.student-state.index');
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {
            $students = UserState::leftJoin('users as u0', 'users_states.user_id', '=', 'u0.id')
                                 ->leftJoin('states', 'users_states.state_id', '=', 'states.id')
                                 ->leftJoin('users as u1', 'users_states.created_by', '=', 'u1.id')
                                 ->leftJoin('users as u2', 'users_states.updated_by', '=', 'u2.id')
                                 ->leftJoin('user_parameters as u_p', function ($join) {
                                     $join->on('u0.id', '=', 'u_p.users_id')
                                        ->where('u_p.parameters_id', 1);
                                 })
                                 ->leftJoin('user_courses', 'user_courses.users_id', '=', 'u0.id')
                                 ->leftJoin('courses_translations as ct', function ($join) {
                                     $join->on('ct.courses_id', '=', 'user_courses.courses_id');
                                     $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                                     $join->on('ct.active', '=', DB::raw(true));
                                 })
                                 ->leftJoin('user_parameters as u_p0', function ($join) {
                                     $join->on('u0.id', '=', 'u_p0.users_id')
                                        ->where('u_p0.parameters_id', 19);
                                 })
                                 ->leftJoin('matriculations', 'matriculations.user_id', '=', 'users_states.user_id')
                                 ->select('users_states.user_id as id', 'u_p.value as name', 'users_states.state_id as states_id', 'u_p0.value as n_matriculation', 'u0.email as email', 'states.name as state', 'ct.display_name as course', 'users_states.occurred_at as occurred_at', 'u1.name as created_by', 'u2.name as updated_by', 'user_courses.courses_id as course_id', 'users_states.courses_id as id_course', 'matriculations.id as matriculation_id');
                                 
            return DataTables::of($students)
                                 ->addColumn('states', function ($item) {
                                     return view('Users::states.student-state.datatables.states')->with('item', $item);
                                 })
                                 ->addColumn('actions', function ($item) {
                                     return view('Users::states.student-state.datatables.actions')->with('item', $item);
                                 })
                                    ->rawColumns(['states','actions'])
                                    ->addIndexColumn()
                                    ->make(true);
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function create()
    {
        try {
            $students = User::whereHas('roles', function ($q) {
                $q->where('id', 6);
            })
            ->whereHas('courses')
            ->whereHas('matriculation')
            ->with(['parameters' => function ($q) {
                $q->whereIn('code', ['nome', 'n_mecanografico']);
            }])
            ->orderBy('name')
            ->get()
            ->map(function ($student) {
                $displayName = $this->formatStudentName($student);
                return ['id' => $student->id , 'display_name' => $displayName];
            });
            
            return view('Users::states.student-state.create', compact('students'));
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function store(StoreStudentState $request)
    {
        try {
            DB::transaction(function () use ($request) {
                UserState::updateOrCreate(
                    ['user_id' => $request->get('user_id')],
                    ['state_id' => $request->get('state_id')]
                );
                UserStateHistoric::create([
                    'user_id' => $request->get('user_id'),
                    'state_id' => $request->get('state_id')
                ]);
            });
            Toastr::success(__('Registo inserido com sucesso'), __('toastr.success'));
            return redirect()->route('states.matriculation');
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function generatePayment($id)
    {
        /**
         *variaveis [dia, artigo, ultimo mes pago]
         */
        /** cursos - articleid
         * biologia - 4
         * Ciências Económicas e Empresariais - 5
         * Licenciatura em CEE (Contabilidade e Auditoria) - 45
         * Licenciatura em Direito - 15
         * Licenciatura em CEE (Economia) - 46
         * Licenciatura em Educação Física e Desporto - 18
         * Licenciatura em CEE (Gestão de empresas) - 47
         * Licenciatura em Ensino de Geografia - 11
         * Licenciatura em Gestão de Recursos Humanos - 16
         * Licenciatura em Ensino de História - 12
         * Licenciatura em Ensino de Pedagogia - 10
         * Licenciatura em Ensino de Psicologia - 13
         * Licenciatura em Psicologia (Psicologia Jurídica) - 44
         * Licenciatura em Relações Internacionais - 14
         * Licenciatura em Ensino de Sociologia - 9
         * Licenciatura em Engenharia Informática -17
         */
        try {
            //avaliar todos os emolumentos por pagar
            $getAllPayments = ArticleRequest::whereUserId($id)
                             ->where('status', 'pending')
                             ->get();
            return $getAllPayments;

            //caso tiver emolumento de propina a pagar
            $data = ArticleRequest::whereUserId($id)
                                ->whereNotNull('month')
                                ->where('status', 'pending')
                                ->get();

            if ($data->isEmpty()) {
                return "Empty!";
            }
            /* DB::transaction(function () use($data){
                 foreach ($data as $value) {
                     $user = User::findOrFail($value->user_id);
                     $course_id = $user->courses()->first()->id;

                     UserState::updateOrCreate(
                         ['user_id' => $value->user_id],
                         ['state_id' => 5, 'courses_id' => $course_id] // 4 => Aguardar matrícula
                     );
                     UserStateHistoric::create([
                                 'user_id' => $value->user_id,
                                 'state_id' => 5
                             ]);
                 }
             });*/
            // return "done!";
            return redirect()->route('states.matriculation');
        } catch (Exception | Throwable $e) {
            logError($e);
        }
    }

    public function getStudentState($studentId)
    {
        $studentState = UserState::join('states', 'users_states.state_id', '=', 'states.id')
                                    ->select('states.id')
                                    ->where('users_states.user_id', $studentId)
                                    ->get();
        $states = State::get()
                    ->map(function ($state) {
                        return ['id' => $state->id, 'display_name' => $state->name];
                    });
        $data = [
            'student' => $studentState,
            'states' => $states
        ];
        return response()->json($data);
    }

    public function indexSchedulingState()
    {
        return view('Users::states.scheduling.index', [
            'tasks' => SchedulingState::all()
        ]);
    }

    public function editSchedulingState($id)
    {
        //$id = 1;
        $task = SchedulingState::findOrFail($id);
        return view('Users::states.scheduling.scheduling', compact('task'));
    }
    public function updateSchedulingState(Request $request, $id)
    {
        $task = SchedulingState::findOrFail($id);
        $task->task = $request->get('task');
        $task->first_date = $request->get('first_date');
        $task->first_month = $request->get('first_month');
        $task->second_date = $request->get('second_date');
        $task->second_month = $request->get('second_month');
        $task->past_day = $request->get('past_day') ?: null;
        $task->save();

        return redirect()->route('indexScheduling.state');
    }

    protected function formatStudentName($student)
    {
        $fullNameParameter = $student->parameters->firstWhere('code', 'nome');
        $fullName = $fullNameParameter && $fullNameParameter->pivot->value ?
            $fullNameParameter->pivot->value : $student->name;

        $studentNumberParameter = $student->parameters->firstWhere('code', 'n_mecanografico');
        $studentNumber = $studentNumberParameter && $studentNumberParameter->pivot->value ?
            $studentNumberParameter->pivot->value : "000";

        return "$fullName #$studentNumber ($student->email)";
    }
}
