<?php

namespace App\Modules\Users\Controllers;

use App\Helpers\LanguageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreState;
use App\Http\Requests\StoreStateType;
use App\Modules\Payments\Models\Article;
use App\Modules\Payments\Models\ArticleRequest;
use App\Modules\Payments\Models\Transaction;
use App\Modules\Users\Models\Matriculation;
use App\Modules\Users\Models\State;
use App\Modules\Users\Models\StateType;
use App\Modules\Users\Models\User;
use App\Modules\Users\Models\UserStateHistoric;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;
use App\Modules\GA\Models\LectiveYear;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;


use App\Model\Institution;
use Error;
use LynX39\LaraPdfMerger\Facades\PdfMerger;
use PDF;


class StatesController extends Controller
{
    public function index()
    {
        try {

            return view('Users::states.index');
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }




    public function state_matriculation()
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

            $data = [
                'action' => 'create',
                'lectiveYears' => $lectiveYears,
                'lectiveYearSelected' => $lectiveYearSelected,
            ];

            return view('Users::states.state-matriculation.matriculation')->with($data);
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }






















    public function ajax_matriculation($ano)
    {
        try { 

            $tranf_type = 'payment';
            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->where("id",$ano)
                ->first();

        

                

            $model = Matriculation::join('users as u0', 'u0.id', '=', 'matriculations.user_id')
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

                //Estado do estudante
                ->leftJoin('users_states as u_state', 'u_state.user_id', '=', 'u0.id')
                ->leftJoin('states as states_studant', 'states_studant.id', '=', 'u_state.state_id')
                ->leftJoin('code_developer as code_dev','code_dev.id','=','states_studant.id_code_dev')
                ->leftJoin('user_parameters as up_meca', function ($join) {
                    $join->on('u0.id', '=', 'up_meca.users_id')
                        ->where('up_meca.parameters_id', 19);
                })
                ->leftJoin('user_parameters as up_bi', function ($join) {
                    $join->on('u0.id', '=', 'up_bi.users_id')
                        ->where('up_bi.parameters_id', 14);
                })


                // ->leftJoin('article_requests as art_requests', function ($join) {
                //     $join->on('art_requests.user_id', '=', 'u0.id')
                //         ->whereIn('art_requests.article_id', [117, 79]);
                // })

                // ->leftJoin('article_requests as art_reques', function ($join) {
                //     $join->on('art_reques.user_id', '=', 'u0.id')
                //         ->where('art_reques.year', "!=", null)
                //         ->where('art_reques.month', "!=", null)
                //         ->where('art_reques.discipline_id', "=", null);
                // })

                ->select([
                    'matriculations.*',
                    // 'art_reques.month as meses',
                    'u0.id as id_usuario',
                    'matriculations.code as code_matricula',
                    'up_meca.value as matricula',
                    // 'art_requests.status as state',
                    'up_bi.value as n_bi',
                    'cl.display_name as classe',
                    'u_p.value as student',
                    'states_studant.name as studant_state',
                    'states_studant.id as studant_state_id',
                    'u0.email as email',
                    'u1.name as criado_por',
                    'u2.name as actualizado_por',
                    'u3.name as deletador_por',
                    'ct.display_name as course',
                    'code_dev.code as code_dev'
                ])

                // ->where('art_requests.deleted_by', null)
                // ->where('art_requests.deleted_at', null)
                ->groupBy('u_p.value')
                ->distinct('id')
                ->where('matriculations.lective_year',$ano);
            // ->whereBetween('matriculations.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date]);
            // ->get();

            // return view('Users::states.datatables.student_states',compact('model'));

            return Datatables::of($model)
                ->addColumn('actions', function ($item) {
                    return view('Users::states.datatables.historic')->with('item', $item);
                })
                ->addColumn('student_states', function ($student_state) {
                    return view('Users::states.datatables.student_states')->with('student_state', $student_state);
                })
                ->rawColumns(['actions', 'student_states', 'months'])
                // ->rawColumns(['actions'])
                ->addIndexColumn()
                ->toJson();
        } catch (Exception | Throwable $e) {
            logError($e);
            return response()->json($e->getMessage(), 500);
        }
    }










    public function ajax()
    {
        $states = State::leftJoin('users as u1', 'u1.id', '=', 'states.created_by')
            ->leftJoin('users as u2', 'u2.id', '=', 'states.updated_by')
            ->join('states_type', 'states.states_type', '=', 'states_type.id')
            ->select([
                'states.*',
                'u1.name as created_by',
                'u2.name as updated_by',
                'states_type.name as state_type'
            ]);

        return DataTables::of($states)
            ->addColumn('actions', function ($item) {
                return view('Users::states.datatables.actions')->with('item', $item);
            })
            ->rawColumns(['actions'])
            ->addIndexColumn()
            ->toJson();
    }











    public function types()
    {
        $types = StateType::all();
        return response()->json($types);
    }

    public function create()
    {
        try {
            $states_type = StateType::get()
                ->map(function ($type) {
                    return ['id' => $type->id, 'display_name' => $type->name];
                });
            return view('Users::states.create', compact('states_type'));
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function store(StoreState $request)
    {
        try {
            State::create([
                'initials' => $request->get('initials'),
                'name' => $request->get('name'),
                'states_type' => $request->get('states_type'),
                'created_by' => Auth::user()->id,
            ]);
            Toastr::success("Estado criado com sucesso");
            // return redirect()->route('states.index');
            return redirect()->route('states.matriculation');
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function show($id)
    {
        try {
            $state = State::join('states_type', 'states_type.id', '=', 'states.states_type')
                ->select('states.id as id', 'states.initials as initials', 'states.name as name', 'states_type.name as type')
                ->where('states.id', $id)
                ->first();
            return view('Users::states.show', compact('state'));
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function edit($id)
    {
        try {
            $state = State::join('states_type', 'states_type.id', '=', 'states.states_type')
                ->select('states.id as id', 'states.initials as initials', 'states.name as name', 'states_type.name as type', 'states_type.id as type_id')
                ->where('states.id', $id)
                ->first();

            return view('Users::states.edit', compact('state'));
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function update(Request $request)
    {
        try {
            $state = State::find($request->get('id'));
            $state->initials = $request->get('initials');
            $state->name = $request->get('name');
            $state->states_type = $request->get('states_type');

            $state->save();

            Toastr::success("Estado atualizado com sucesso");
            return redirect()->route('states.index');
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function destroy($id)
    {
        try {
            $state = State::find($id)->delete();
            Toastr::success("Estado removido com sucesso");
            return redirect()->route('states.index');
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function typeIndex()
    {
        try {
            return view('Users::states.types.index');
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
    public function typeAjax()
    {
        $types = StateType::leftJoin('users as u1', 'u1.id', '=', 'states_type.created_by')
            ->leftJoin('users as u2', 'u2.id', '=', 'states_type.updated_by')
            ->select([
                'states_type.*',
                'u1.name as created_by',
                'u2.name as updated_by'
            ]);
        return DataTables::of($types)
            ->addColumn('actions', function ($item) {
                return view('Users::states.types.datatables.actions')->with('item', $item);
            })
            ->rawColumns(['actions'])
            ->addIndexColumn()
            ->toJson();
    }

    public function typeCreate()
    {
        try {
            return view('Users::states.types.create');
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function typeStore(StoreStateType $request)
    {
        try {
            StateType::create([
                'name' => $request->get('name')
            ]);
            Toastr::success("Tipo de estado criado com sucesso");
            return redirect()->route('types.index');
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function typeShow($id)
    {
        try {
            $type = StateType::findOrFail($id);
            return view('Users::states.types.show', compact('type'));
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function typeEdit($id)
    {  
        try {
            $type = StateType::findOrFail($id);
            return view('Users::states.types.edit', compact('type'));
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function typeUpdate(StoreStateType $request)
    {
        try {
            $type = StateType::findOrFail($request->get('id'));
            $type->name = $request->get('name');
            $type->save();
            Toastr::success("Tipo de estado editado com sucesso");
            return redirect()->route('types.index');
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function typeDestroy($id)
    {
        try {
            // return "Ola!";
            $type = StateType::findOrFail($id);
            $type->delete();
            Toastr::success("Tipo de estado removido com sucesso");
            return redirect()->route('types.index');
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function stateHistoric()
    {
        return view('Users::states.state_historic');
    }

    public function stateHistoricAjax($id_user)
    {
        $statesHistoric = UserStateHistoric::join('states', 'states.id', '=', 'users_states_historic.state_id')
            ->join('users', 'users.id', '=', 'users_states_historic.user_id')
            ->join('states_type', 'states_type.id', '=', 'states.states_type')
            ->where('users.id',$id_user)
            
            ->select([
                'users_states_historic.*',
                'users_states_historic.occurred_at as occurred_at',
                'users.name as user_name',
                'states.initials as initials',
                'states.name as studant_state',
                'states_type.name as state_type'
            ]);

        return DataTables::of($statesHistoric)
            ->addColumn('actions', function ($item) {
                return view('Users::states.datatables.actions')->with('item', $item);
            })
            ->addColumn('student_states', function ($student_state) {
                return view('Users::states.datatables.student_states')->with('student_state', $student_state);
            })
            ->rawColumns(['actions','student_states'])
            ->addIndexColumn()
            ->toJson();
    }

    public function generateEmolument($course, $month, $year)
    {

        // return "escape!";
        try {
            //IMPORTANTE: ANTES TESTAR COM O CURSO DE CEE (3 ano UP);
            $date = date($year . '-' . $month . '-01');
            // 1247

            //5092
            //c.gabriel@ispm.co.ao
            //c.aurelio@ispm.co.ao
            $ids = [1672, 1623];

            //  return $users = User::whereHas('courses', function($q){$q->where('id', 11); })
            //             ->whereHas('matriculation')
            //             ->with(['courses',
            //                     'matriculation' => function ($q) {
            //                 $q->with('disciplines');
            //             }])->take(10)->get();

            $users = User::whereHas('courses', function ($q) use ($course) {
                $q->where('id', $course);
            })
                ->whereHas('matriculation')
                ->get();

            foreach ($users as $user) {
                DB::transaction(function () use ($user, $course, $month, $year, $date) {


                    if ($user->courses->first()->id == 11) { //Ensino de Biologia
                        $article = Article::findOrFail(4);
                        //Create
                        $articleRequest = new ArticleRequest([
                            'user_id' => $user->id,
                            'article_id' => $article->id,
                            'year' => $year ?: null,
                            'month' => $month ?: null,
                            'base_value' => $article->base_value,
                            'created_at' => $date,
                            'updated_at' => $date
                        ]);

                        $articleRequest->save();

                        // create debit with article base value
                        $transaction = Transaction::create([
                            'type' => 'debit',
                            'value' => $articleRequest->base_value,
                            'notes' => 'Débito inicial do valor base',
                            'created_at' => $date,
                            'updated_at' => $date
                        ]);

                        $transaction->article_request()
                            ->attach($articleRequest->id, ['value' => $articleRequest->base_value]);
                    } elseif ($user->courses->first()->id == 20) { //Direito
                        $article = Article::findOrFail(15);

                        //Create
                        $articleRequest = new ArticleRequest([
                            'user_id' => $user->id,
                            'article_id' => $article->id,
                            'year' => $year ?: null,
                            'month' => $month ?: null,
                            'base_value' => $article->base_value,
                            'created_at' => $date,
                            'updated_at' => $date
                        ]);

                        $articleRequest->save();

                        // create debit with article base value
                        $transaction = Transaction::create([
                            'type' => 'debit',
                            'value' => $articleRequest->base_value,
                            'notes' => 'Débito inicial do valor base',
                            'created_at' => $date,
                            'updated_at' => $date
                        ]);

                        $transaction->article_request()
                            ->attach($articleRequest->id, ['value' => $articleRequest->base_value]);
                    } elseif ($user->courses->first()->id == 19) { // Educação Física e Desportos
                        $article = Article::findOrFail(18);

                        //Create
                        $articleRequest = new ArticleRequest([
                            'user_id' => $user->id,
                            'article_id' => $article->id,
                            'year' => $year ?: null,
                            'month' => $month ?: null,
                            'base_value' => $article->base_value,
                            'created_at' => $date,
                            'updated_at' => $date
                        ]);

                        $articleRequest->save();

                        // create debit with article base value
                        $transaction = Transaction::create([
                            'type' => 'debit',
                            'value' => $articleRequest->base_value,
                            'notes' => 'Débito inicial do valor base',
                            'created_at' => $date,
                            'updated_at' => $date
                        ]);

                        $transaction->article_request()
                            ->attach($articleRequest->id, ['value' => $articleRequest->base_value]);
                    } elseif ($user->courses->first()->id == 26) { // Ensino de Geografia
                        $article = Article::findOrFail(11);

                        //Create
                        $articleRequest = new ArticleRequest([
                            'user_id' => $user->id,
                            'article_id' => $article->id,
                            'year' => $year ?: null,
                            'month' => $month ?: null,
                            'base_value' => $article->base_value,
                            'created_at' => $date,
                            'updated_at' => $date
                        ]);

                        $articleRequest->save();

                        // create debit with article base value
                        $transaction = Transaction::create([
                            'type' => 'debit',
                            'value' => $articleRequest->base_value,
                            'notes' => 'Débito inicial do valor base',
                            'created_at' => $date,
                            'updated_at' => $date
                        ]);

                        $transaction->article_request()
                            ->attach($articleRequest->id, ['value' => $articleRequest->base_value]);
                    } elseif ($user->courses->first()->id == 27) { // Gestão de Recursos Humanos

                        $article = Article::findOrFail(16);

                        //Create
                        $articleRequest = new ArticleRequest([
                            'user_id' => $user->id,
                            'article_id' => $article->id,
                            'year' => $year ?: null,
                            'month' => $month ?: null,
                            'base_value' => $article->base_value,
                            'created_at' => $date,
                            'updated_at' => $date
                        ]);

                        $articleRequest->save();

                        // create debit with article base value
                        $transaction = Transaction::create([
                            'type' => 'debit',
                            'value' => $articleRequest->base_value,
                            'notes' => 'Débito inicial do valor base',
                            'created_at' => $date,
                            'updated_at' => $date
                        ]);

                        $transaction->article_request()
                            ->attach($articleRequest->id, ['value' => $articleRequest->base_value]);
                    } elseif ($user->courses->first()->id == 16) { // Ensino de História
                        $article = Article::findOrFail(12);

                        //Create
                        $articleRequest = new ArticleRequest([
                            'user_id' => $user->id,
                            'article_id' => $article->id,
                            'year' => $year ?: null,
                            'month' => $month ?: null,
                            'base_value' => $article->base_value,
                            'created_at' => $date,
                            'updated_at' => $date
                        ]);

                        $articleRequest->save();

                        // create debit with article base value
                        $transaction = Transaction::create([
                            'type' => 'debit',
                            'value' => $articleRequest->base_value,
                            'notes' => 'Débito inicial do valor base',
                            'created_at' => $date,
                            'updated_at' => $date
                        ]);

                        $transaction->article_request()
                            ->attach($articleRequest->id, ['value' => $articleRequest->base_value]);
                    } elseif ($user->courses->first()->id == 28) { // Engenharia Informática
                        $article = Article::findOrFail(17);

                        //Create
                        $articleRequest = new ArticleRequest([
                            'user_id' => $user->id,
                            'article_id' => $article->id,
                            'year' => $year ?: null,
                            'month' => $month ?: null,
                            'base_value' => $article->base_value,
                            'created_at' => $date,
                            'updated_at' => $date
                        ]);

                        $articleRequest->save();

                        // create debit with article base value
                        $transaction = Transaction::create([
                            'type' => 'debit',
                            'value' => $articleRequest->base_value,
                            'notes' => 'Débito inicial do valor base',
                            'created_at' => $date,
                            'updated_at' => $date
                        ]);

                        $transaction->article_request()
                            ->attach($articleRequest->id, ['value' => $articleRequest->base_value]);
                    } elseif ($user->courses->first()->id == 22) { // Ensino de Pedagogia
                        $article = Article::findOrFail(10);

                        //Create
                        $articleRequest = new ArticleRequest([
                            'user_id' => $user->id,
                            'article_id' => $article->id,
                            'year' => $year ?: null,
                            'month' => $month ?: null,
                            'base_value' => $article->base_value,
                            'created_at' => $date,
                            'updated_at' => $date
                        ]);

                        $articleRequest->save();

                        // create debit with article base value
                        $transaction = Transaction::create([
                            'type' => 'debit',
                            'value' => $articleRequest->base_value,
                            'notes' => 'Débito inicial do valor base',
                            'created_at' => $date,
                            'updated_at' => $date
                        ]);

                        $transaction->article_request()
                            ->attach($articleRequest->id, ['value' => $articleRequest->base_value]);
                    } elseif ($user->courses->first()->id == 18) { // Ensino de Psicologia
                        $article = Article::findOrFail(13);

                        //Create
                        $articleRequest = new ArticleRequest([
                            'user_id' => $user->id,
                            'article_id' => $article->id,
                            'year' => $year ?: null,
                            'month' => $month ?: null,
                            'base_value' => $article->base_value,
                            'created_at' => $date,
                            'updated_at' => $date
                        ]);

                        $articleRequest->save();

                        // create debit with article base value
                        $transaction = Transaction::create([
                            'type' => 'debit',
                            'value' => $articleRequest->base_value,
                            'notes' => 'Débito inicial do valor base',
                            'created_at' => $date,
                            'updated_at' => $date
                        ]);

                        $transaction->article_request()
                            ->attach($articleRequest->id, ['value' => $articleRequest->base_value]);
                    } elseif ($user->courses->first()->id == 15) { // Psicologia (Psicologia Jurídica)
                        $article = Article::findOrFail(44);

                        //Create
                        $articleRequest = new ArticleRequest([
                            'user_id' => $user->id,
                            'article_id' => $article->id,
                            'year' => $year ?: null,
                            'month' => $month ?: null,
                            'base_value' => $article->base_value,
                            'created_at' => $date,
                            'updated_at' => $date
                        ]);

                        $articleRequest->save();

                        // create debit with article base value
                        $transaction = Transaction::create([
                            'type' => 'debit',
                            'value' => $articleRequest->base_value,
                            'notes' => 'Débito inicial do valor base',
                            'created_at' => $date,
                            'updated_at' => $date
                        ]);

                        $transaction->article_request()
                            ->attach($articleRequest->id, ['value' => $articleRequest->base_value]);
                    } elseif ($user->courses->first()->id == 23) { // Relações Internacionais
                        $article = Article::findOrFail(14);

                        //Create
                        $articleRequest = new ArticleRequest([
                            'user_id' => $user->id,
                            'article_id' => $article->id,
                            'year' => $year ?: null,
                            'month' => $month ?: null,
                            'base_value' => $article->base_value,
                            'created_at' => $date,
                            'updated_at' => $date
                        ]);

                        $articleRequest->save();

                        // create debit with article base value
                        $transaction = Transaction::create([
                            'type' => 'debit',
                            'value' => $articleRequest->base_value,
                            'notes' => 'Débito inicial do valor base',
                            'created_at' => $date,
                            'updated_at' => $date
                        ]);

                        $transaction->article_request()
                            ->attach($articleRequest->id, ['value' => $articleRequest->base_value]);
                    } elseif ($user->courses->first()->id == 21) { // Ensino de Sociologia
                        $article = Article::findOrFail(9);

                        //Create
                        $articleRequest = new ArticleRequest([
                            'user_id' => $user->id,
                            'article_id' => $article->id,
                            'year' => $year ?: null,
                            'month' => $month ?: null,
                            'base_value' => $article->base_value,
                            'created_at' => $date,
                            'updated_at' => $date
                        ]);

                        $articleRequest->save();

                        // create debit with article base value
                        $transaction = Transaction::create([
                            'type' => 'debit',
                            'value' => $articleRequest->base_value,
                            'notes' => 'Débito inicial do valor base',
                            'created_at' => $date,
                            'updated_at' => $date
                        ]);

                        $transaction->article_request()
                            ->attach($articleRequest->id, ['value' => $articleRequest->base_value]);
                    } elseif ($user->courses->first()->id == 25) { //Ciencias economicas empresariais

                        $disciplineCode = $user->matriculation->disciplines->first()->code;

                        if (Str::contains($disciplineCode, "GEE")) {
                            $article = Article::findOrFail(47);
                            //Create
                            $articleRequest = new ArticleRequest([
                                'user_id' => $user->id,
                                'article_id' => $article->id,
                                'year' => $year ?: null,
                                'month' => $month ?: null,
                                'base_value' => $article->base_value,
                                'created_at' => $date,
                                'updated_at' => $date
                            ]);

                            $articleRequest->save();

                            // create debit with article base value
                            $transaction = Transaction::create([
                                'type' => 'debit',
                                'value' => $articleRequest->base_value,
                                'notes' => 'Débito inicial do valor base',
                                'created_at' => $date,
                                'updated_at' => $date
                            ]);

                            $transaction->article_request()
                                ->attach($articleRequest->id, ['value' => $articleRequest->base_value]);
                        } elseif (Str::contains($disciplineCode, "COA")) {
                            $article = Article::findOrFail(45);
                            //Create
                            $articleRequest = new ArticleRequest([
                                'user_id' => $user->id,
                                'article_id' => $article->id,
                                'year' => $year ?: null,
                                'month' => $month ?: null,
                                'base_value' => $article->base_value,
                                'created_at' => $date,
                                'updated_at' => $date
                            ]);

                            $articleRequest->save();

                            // create debit with article base value
                            $transaction = Transaction::create([
                                'type' => 'debit',
                                'value' => $articleRequest->base_value,
                                'notes' => 'Débito inicial do valor base',
                                'created_at' => $date,
                                'updated_at' => $date
                            ]);

                            $transaction->article_request()
                                ->attach($articleRequest->id, ['value' => $articleRequest->base_value]);
                        } elseif (Str::contains($disciplineCode, "ECO")) {
                            $article = Article::findOrFail(46);
                            //Create
                            $articleRequest = new ArticleRequest([
                                'user_id' => $user->id,
                                'article_id' => $article->id,
                                'year' => $year ?: null,
                                'month' => $month ?: null,
                                'base_value' => $article->base_value,
                                'created_at' => $date,
                                'updated_at' => $date
                            ]);

                            $articleRequest->save();

                            // create debit with article base value
                            $transaction = Transaction::create([
                                'type' => 'debit',
                                'value' => $articleRequest->base_value,
                                'notes' => 'Débito inicial do valor base',
                                'created_at' => $date,
                                'updated_at' => $date
                            ]);

                            $transaction->article_request()
                                ->attach($articleRequest->id, ['value' => $articleRequest->base_value]);
                        } else if (Str::contains($disciplineCode, "CEE")) {
                            $article = Article::findOrFail(5);
                            //Create
                            $articleRequest = new ArticleRequest([
                                'user_id' => $user->id,
                                'article_id' => $article->id,
                                'year' => $year ?: null,
                                'month' => $month ?: null,
                                'base_value' => $article->base_value,
                                'created_at' => $date,
                                'updated_at' => $date
                            ]);

                            $articleRequest->save();

                            // create debit with article base value
                            $transaction = Transaction::create([
                                'type' => 'debit',
                                'value' => $articleRequest->base_value,
                                'notes' => 'Débito inicial do valor base',
                                'created_at' => $date,
                                'updated_at' => $date
                            ]);

                            $transaction->article_request()
                                ->attach($articleRequest->id, ['value' => $articleRequest->base_value]);
                        }
                    }
                });
            }







            // return "executado!";

        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }





        //Emolumentos de propinas
        // emol-curso / id

        // Propina - Licenciatura em Ciências Económicas e Empresariais - 5
        // Propina - Licenciatura em CEE (Contabilidade e Auditoria) - 45
        //
        // Propina - Licenciatura em CEE (Economia) - 46

        // Propina - Licenciatura em CEE (Gestão de empresas) - 47

        // -------------------------------------------------------------------
        // Ciências Económicas e Empresariais - 25
    }


    public function pdfStates_historic($user_id)
    {
        // return $user_id;
        try{
         $getStudent=DB::table('users as use')
        ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'use.id')
                ->join('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'uc.courses_id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })
        ->leftJoin('user_parameters as u_p', function ($join) {
            $join->on('use.id', '=', 'u_p.users_id')
                ->where('u_p.parameters_id', 1);
        })
        ->leftJoin('user_parameters as matricula', function ($join) {
            $join->on('use.id', '=', 'matricula.users_id')
                ->where('matricula.parameters_id', 19);
        })
        ->select([
            'use.email as email',
            'ct.display_name as curso',
            'u_p.value as full_name',
            'matricula.value as matricula',
        ])
         ->where('use.id',$user_id)
        ->first();
        
        $statesHistoric = UserStateHistoric::join('states as state', 'state.id', '=', 'users_states_historic.state_id')
            ->join('users as use', 'use.id', '=', 'users_states_historic.user_id')
            ->join('states_type as state_type', 'state_type.id', '=', 'state.states_type')
            ->where('use.id',$user_id)
            ->select([
                'users_states_historic.*',
                'users_states_historic.occurred_at as occurred_at',
                'use.name as user_name',
                'state.initials as initials',
                'state.name as studant_state',
                'state_type.name as state_type',
            ])
            ->get();


            $institution = Institution::latest()->first();
            $titulo_documento = "DOCUMENTO";
            $documentoGerado_documento = "Documento gerado a";
            $documentoCode_documento = 1;
             $dados=[
                'institution'=>$institution,
                'statesHistoric'=>$statesHistoric,
                'getStudent'=>$getStudent
            ];
            

            $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
            $pdf = PDF::loadView("Users::states.state-matriculation.pdfState_historic", $dados);              
            $pdf->setOption('margin-top', '3mm');
            $pdf->setOption('margin-left', '3mm');
            $pdf->setOption('margin-bottom', '1.5cm');
            $pdf->setOption('margin-right', '3mm');
            $pdf->setOption('enable-javascript', true);
            $pdf->setOption('debug-javascript', true);
            $pdf->setOption('javascript-delay', 1000);
            $pdf->setOption('enable-smart-shrinking', true);
            $pdf->setOption('no-stop-slow-scripts', true);
            $pdf->setOption('footer-html', $footer_html);
            // $pdf->setPaper('a4','landscape');       
            return $pdf->stream('Forlearn | Estado Matrícula');
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }

    }
}
