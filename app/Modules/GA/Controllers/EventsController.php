<?php

namespace App\Modules\GA\Controllers;

use App\Modules\Users\Models\User;
use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\Event;
use App\Modules\GA\Models\EventOption;
use App\Modules\GA\Models\EventTranslation;
use App\Modules\GA\Models\EventType;
use App\Modules\GA\Requests\EventRequest;
use Carbon\Carbon;
use DataTables;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Log;
use Throwable;
use Toastr;

class EventsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        try {
            
            
            return DB::table("user_candidate")
            ->leftJoin('user_parameters as up', function ($join) {
                $join->on('user_candidate.user_id', '=', 'up.users_id')
                    ->where('up.parameters_id', 311)
                    ->where('up.parameter_group_id',11);
            })
            ->select(["user_candidate.user_id","user_candidate.code","up.value"])
            ->get();
            
            return "Mateus Massaqui Jungo";
            
            
            
            return view('GA::events.index');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {

            $model = Event::join('users as u1', 'u1.id', '=', 'events.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'events.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'events.deleted_by')
                ->leftJoin('event_types as et', 'et.id', 'events.event_type_id')
                ->leftJoin('event_type_translations as ett', function ($join) {
                    $join->on('ett.event_type_id', '=', 'et.id');
                    $join->on('ett.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ett.active', '=', DB::raw(true));
                })
                ->leftJoin('event_translations as etr', function ($join) {
                    $join->on('etr.event_id', '=', 'events.id');
                    $join->on('etr.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('etr.active', '=', DB::raw(true));
                })
                ->select([
                    'events.id',
                    'events.created_at',
                    'events.updated_at',
                    'events.start',
                    'events.start_time',
                    'events.end',
                    'events.end_time',
                    'events.all_day',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    'etr.display_name',
                    'etr.description',
                    'ett.display_name as type'
                ]);

            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('GA::events.datatables.actions')->with('item', $item);
                })
                ->editColumn('all_day', function ($item) {
                    return $item->all_day ? __('common.yes') : __('common.no');
                })
                ->rawColumns(['actions'])
                ->toJson();
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
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

            // Listar todos os tipos de eventos

            $event_types = DB::table('event_types')
                ->join('event_type_translations', 'event_type_translations.event_type_id', "=", 'event_types.id')
                ->whereNull('event_types.deleted_at')
                ->where('active', '1')
                ->whereNull('event_type_translations.deleted_at')
                ->select(['event_type_id', 'code', 'display_name', 'description'])
                ->get();

            // Listar todos os cargos

            $roles = DB::table('role_translations')
                ->whereNull('deleted_at')
                ->where('active', '1')
                // ->whereNotIn('role_translations.role_id',[2]) IMPORTANT RETIRAR ESTE COMENTÁRIO DEPOIS
                ->select(['role_id', 'display_name'])
                ->orderBy('display_name')
                ->get();


            $data = [
                'action' => 'create',
                'roles' => $roles,
                'event_types' => $event_types,
                'languages' => Language::whereActive(true)->get(),
            ];
            return view('GA::events.event')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function my_events()
    {
        try {

            // Listar todos os tipos de eventos



            $data = [
                'action' => 'create'

            ];
            return view('GA::events.myevent')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(EventRequest $request)
    {
        try {





            // Cadastrar um determinado evento

            $id_event = DB::table('events')->insertGetId(
                [
                    'start' => Carbon::parse($request->get('start')),
                    'end' => Carbon::parse($request->get('end')),
                    'start_time' => Carbon::parse($request->get('start_time')),
                    'end_time' => Carbon::parse($request->get('end_time')),
                    'all_day' => "1",
                    // 'url' => $request->get('url'),
                    'event_type_id' => $request->get('event_type'),
                    'created_by' => auth()->user()->id
                ]
            );

            // Cadastrar a tradução de um determinado evento

            $id_event_translations = DB::table('event_translations')->insertGetId(
                [
                    'event_id' => $id_event,
                    'language_id' => 1,
                    'display_name' => $request->display_name[1],
                    'description' => $request->description[1],
                    'active' => "1",
                    'version' => "1",
                ]
            );

            $usuarios = collect($request->users_group)->map(function ($item) {
                $array = explode("-", $item);
                return ["user_roles" => $array[1], "id_user" => $array[0]];
            });

            $to = array();

            foreach ($usuarios as $item) {

                $to[] = $item["id_user"];

                $id_event_uses = DB::table('events_users')->insertGetId(
                    [
                        'event_id' => $id_event,
                        'roles_id' => $item["user_roles"],
                        'users_id' => $item["id_user"]

                    ]
                );
            }


            // Pegar o tipo de evento

            $tipo_de_evento = DB::table('event_types')
                ->join('event_type_translations', 'event_type_translations.event_type_id', "=", 'event_types.id')
                ->whereNull('event_types.deleted_at')
                ->where('active', '1')
                ->where('event_types.id', $request->get('event_type'))
                ->whereNull('event_type_translations.deleted_at')
                ->select(['code', 'display_name'])
                ->get();

            // Icon

            $icon = "fa fa-calendar";

            // Nome da notificação

            $subject = $request->display_name[1] . " [ " . $request->start . " ] ";

            // Corpo da notificação

            if ($request->start == $request->end) {

                $body = $request->description[1] .
                    "<br><p style='border-left: 6px solid #6cb2eb;text-indent: 10px;font-size: 16px;margin-bottom: 3px;'>Tipo de evento: <strong>" . $tipo_de_evento[0]->display_name . " </strong><i class='fas fa-star' style='font-size:14px;text-indent:7px;color:#6cb2eb;'></i></p>" .
                    "<p style='border-left: 6px solid #6cb2eb;text-indent: 10px;font-size: 16px;margin-bottom: 3px;'>Data: <strong>" . Carbon::parse($request->start)->format('d-m-Y') . " </strong> <i class='fas fa-calendar' style='font-size:14px;text-indent:7px;color:#6cb2eb;'></i></p>" .
                    "<p style='border-left: 6px solid #6cb2eb;text-indent: 10px;font-size: 16px;margin-bottom: 3px;'>Hora de início: <strong>" . $request->start_time . " </strong> <i class='fas fa-clock' style='font-size:14px;text-indent:7px;color:#6cb2eb;'></i>   Hora de fim: <strong>" . $request->end_time . "</strong><i class='fas fa-clock' style='font-size:14px;text-indent:7px;color:#6cb2eb;'></i>";
            } else {

                $body = $request->description[1] .
                    "<br><p style='border-left: 6px solid #6cb2eb;text-indent: 10px;font-size: 16px;margin-bottom: 3px;'>Tipo de evento: <strong>" . $tipo_de_evento[0]->display_name . " </strong><i class='fas fa-star' style='font-size:14px;text-indent:7px;color:#6cb2eb;'></i></p>" .
                    "<p style='border-left: 6px solid #6cb2eb;text-indent: 10px;font-size: 16px;margin-bottom: 3px;'>Data de início: <strong>" . Carbon::parse($request->start)->format('d-m-Y') . " </strong><i class='fas fa-calendar' style='font-size:14px;text-indent:7px;color:#6cb2eb;'></i> | Data de fim: <strong>" . Carbon::parse($request->end_time)->format('d-m-Y') . "</strong> <i class='fa fas-calendar' style='font-size:14px;text-indent:7px;color:#6cb2eb;'></i></p>" .
                    "<p style='border-left: 6px solid #6cb2eb;text-indent: 10px;font-size: 16px;margin-bottom: 3px;'>Hora de início: <strong>" . $request->start_time . " </strong><i class='fas fa-clock' style='font-size:14px;text-indent:7px;color:#6cb2eb;'></i>   Hora de fim: <strong>" . $request->end_time . "</strong> <i class='fas fa-clock' style='font-size:14px;text-indent:7px;color:#6cb2eb;'></i>";
            }


            // Destinatários 



            notification("fa fa-calendar", $subject, $body, $to, null, null);



            // Success message
            Toastr::success(__('GA::events.store_success_message'), __('toastr.success'));
            return view('GA::events.index');
        } catch (Exception | Throwable $e) {
            Toastr::error($e->getMessage(), __('toastr.error'));
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    private function fetch($id, $action)
    {
        try {
            // Find
            $event = Event::whereId($id)->with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                },
                'options',
                'type' => function ($q) {
                    $q->with([
                        'currentTranslation'
                    ]);
                },
                'createdBy'
            ])->firstOrFail();

            // Usuários associados a um determinado evento

            $events_users = DB::table('events_users')
                ->where('event_id', $id)
                ->select(['users_id', 'roles_id'])
                ->get();

            $events_roles = DB::table('events_users')
                ->where('event_id', $id)
                ->select(['roles_id'])
                ->groupBy('roles_id')
                ->get();

            $events_users_event = DB::table('events_users')
                ->where('event_id', $id)
                ->select(['users_id'])
                ->groupBy('users_id')
                ->get();



            // Utilizadores 

            $cargos = array();
            $usuarios_id = array();

            foreach ($events_roles as $item) {

                $cargos[] = $item->roles_id;
            }
            foreach ($events_users_event as $item) {

                $usuarios_id[] = $item->users_id;
            }



            $usuarios_cargos = DB::table('users as usuario')
            ->join('model_has_roles as usuario_cargo', 'usuario.id', '=', 'usuario_cargo.model_id')
            ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')
            ->whereIn('usuario_cargo.role_id', $cargos)
            ->whereNull('usuario.deleted_at')
            ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
            ->select(['usuario.id as id_usuario', 'usuario.email as email_usuario', 'usuario.name as nome_usuario', 'cargo.id as cargo_usuario'])
            ->orderBy('nome_usuario')
            ->groupBy('id_usuario')
            ->get();


            $roles = DB::table('role_translations')
                ->whereNull('deleted_at')
                ->where('active', '1')
                // ->whereNotIn('role_translations.role_id',[2]) IMPORTANT RETIRAR ESTE COMENTÁRIO DEPOIS
                ->select(['role_id', 'display_name'])
                ->orderBy('display_name')
                ->get();

            $event->start = Carbon::parse($event->start)->format('Y-m-d');
            $event->start_time = Carbon::parse($event->start_time)->format('H:i:s');
            $event->end = Carbon::parse($event->end)->format('Y-m-d');
            $event->end_time = Carbon::parse($event->end_time)->format('H:i:s');

            $event_types = DB::table('event_types')
                ->join('event_type_translations', 'event_type_translations.event_type_id', "=", 'event_types.id')
                ->whereNull('event_types.deleted_at')
                ->where('active', '1')
                ->whereNull('event_type_translations.deleted_at')
                ->select(['event_type_id', 'code', 'display_name', 'description'])
                ->get();


            $data = [
                'action' => $action,
                'event' => $event,
                'events_users' => $events_users,
                'cargos' => $cargos,
                'usuarios_id' => $usuarios_id,
                'roles' => $roles,
                'usuarios_cargos' => $usuarios_cargos,
                'event_types' => $event_types,
                'options' => $event->options->pluck('value', 'key')->toArray(),
                'translations' => $event->translations->keyBy('language_id')->toArray(),
                'languages' => Language::whereActive(true)->get()
            ];




            return view('GA::events.event')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::events.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return abort(500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            return $this->fetch($id, 'show');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            return $this->fetch($id, 'edit');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {

            $request;

            DB::beginTransaction();

            // Fetch the default language
            $default_language = Language::whereDefault(true)->firstOrFail();

            // Find and update
            $event = Event::whereId($id)->firstOrFail();
            $event->start = Carbon::parse($request->get('start'));
            $event->start_time = Carbon::parse($request->get('start_time'));
            $event->end = Carbon::parse($request->get('end'));
            $event->end_time = Carbon::parse($request->get('end_time'));
            $event->all_day = $request->has('all_day');
            $event->url = $request->get('url');
            $event->event_type_id = $request->get('event_type');
            $event->save();

            $event->options()->delete();
            if ($request->has('options')) {
                $options = [];
                foreach ($request->get('options') as $key => $value) {
                    $options[] = [
                        'event_id' => $event->id,
                        'key' => $key,
                        'value' => $value
                    ];
                }

                if (!empty($options)) {
                    EventOption::insert($options);
                }
            }

            // Disable previous translations
            EventTranslation::where('event_id', $event->id)->update(['active' => false]);
            $version = EventTranslation::where('event_id', $event->id)->whereLanguageId($default_language->id)->count() + 1;

            // Associated translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {
                $event_translations[] = [
                    'event_id' => $event->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id] ?? null,
                    'description' => $request->get('description')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => $version,
                    'active' => true,
                ];
            }

            if (!empty($event_translations)) {
                EventTranslation::insert($event_translations);
            }

            DB::commit();

            // Retorna




            $usuarios = collect($request->users_group)->map(function ($item) {
                $array = explode("-", $item);
                return ["user_roles" => $array[1], "id_user" => $array[0]];
            });

            $to = array();

            foreach ($usuarios as $item) {

                $to[] = $item["id_user"];


                if (DB::table('events_users')
                    ->where('event_id', "=", $id)
                    ->where('users_id', "=", $item["id_user"])

                    ->exists()
                ) {
                } else {

                    $id_event_uses = DB::table('events_users')->insertGetId(
                        [
                            'event_id' => $id,
                            'roles_id' => $item["user_roles"],
                            'users_id' => $item["id_user"]

                        ]
                    );
                }
            }


            // Pegar o tipo de evento

            $tipo_de_evento = DB::table('event_types')
                ->join('event_type_translations', 'event_type_translations.event_type_id', "=", 'event_types.id')
                ->whereNull('event_types.deleted_at')
                ->where('active', '1')
                ->where('event_types.id', $request->get('event_type'))
                ->whereNull('event_type_translations.deleted_at')
                ->select(['code', 'display_name'])
                ->get();

            // Icon

            $icon = "fa fa-calendar";

            // Nome da notificação

            $subject = $request->display_name[1] . " [ " . $request->start . " ] ";

            // Corpo da notificação

            if ($request->start == $request->end) {

                $body = $request->description[1] .
                    "<br><p style='border-left: 6px solid #6cb2eb;text-indent: 10px;font-size: 16px;margin-bottom: 3px;'>Tipo de evento: <strong>" . $tipo_de_evento[0]->display_name . " </strong><i class='fas fa-star' style='font-size:14px;text-indent:7px;color:#6cb2eb;'></i></p>" .
                    "<p style='border-left: 6px solid #6cb2eb;text-indent: 10px;font-size: 16px;margin-bottom: 3px;'>Data: <strong>" . Carbon::parse($request->start)->format('d-m-Y') . " </strong> <i class='fas fa-calendar' style='font-size:14px;text-indent:7px;color:#6cb2eb;'></i></p>" .
                    "<p style='border-left: 6px solid #6cb2eb;text-indent: 10px;font-size: 16px;margin-bottom: 3px;'>Hora de início: <strong>" . $request->start_time . " </strong> <i class='fas fa-clock' style='font-size:14px;text-indent:7px;color:#6cb2eb;'></i>   Hora de fim: <strong>" . $request->end_time . "</strong><i class='fas fa-clock' style='font-size:14px;text-indent:7px;color:#6cb2eb;'></i>" .
                    "<p style='border-left: 6px solid #eba26c;text-indent: 10px;font-size: 16px;margin-bottom: 3px;'> <strong> Este evento foi actualizado </strong> <i class='fas fa-done' style='font-size:14px;text-indent:7px;color:#6cb2eb;'></i>   </p>";
            } else {

                $body = $request->description[1] .
                    "<br><p style='border-left: 6px solid #6cb2eb;text-indent: 10px;font-size: 16px;margin-bottom: 3px;'>Tipo de evento: <strong>" . $tipo_de_evento[0]->display_name . " </strong><i class='fas fa-star' style='font-size:14px;text-indent:7px;color:#6cb2eb;'></i></p>" .
                    "<p style='border-left: 6px solid #6cb2eb;text-indent: 10px;font-size: 16px;margin-bottom: 3px;'>Data de início: <strong>" . Carbon::parse($request->start)->format('d-m-Y') . " </strong><i class='fas fa-calendar' style='font-size:14px;text-indent:7px;color:#6cb2eb;'></i> | Data de fim: <strong>" . Carbon::parse($request->end_time)->format('d-m-Y') . "</strong> <i class='fa fas-calendar' style='font-size:14px;text-indent:7px;color:#6cb2eb;'></i></p>" .
                    "<p style='border-left: 6px solid #6cb2eb;text-indent: 10px;font-size: 16px;margin-bottom: 3px;'>Hora de início: <strong>" . $request->start_time . " </strong><i class='fas fa-clock' style='font-size:14px;text-indent:7px;color:#6cb2eb;'></i>   Hora de fim: <strong>" . $request->end_time . "</strong> <i class='fas fa-clock' style='font-size:14px;text-indent:7px;color:#6cb2eb;'></i>" .
                    "<p style='border-left: 6px solid #eba26c;text-indent: 10px;font-size: 16px;margin-bottom: 3px;'> <strong> Este evento foi actualizado </strong><i class='fas fa-done' style='font-size:14px;text-indent:7px;color:#6cb2eb;'></i>   </p>";
            }


            // Destinatários 



            notification("fa fa-calendar", $subject, $body, $to, null, null);



            // Success message
            Toastr::success(__('GA::events.update_success_message'), __('toastr.success'));
            return redirect()->route('events.show', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::events.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            // Find and delete
            $event = Event::whereId($id)->firstOrFail();
            $event->options()->forceDelete();
            $event->translations()->forceDelete();
            $event->delete();

            DB::commit();

            // Success message
            Toastr::success(__('GA::events.destroy_success_message'), __('toastr.success'));
            return redirect()->route('events.index');
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::events.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    public function ajax_users($role_id)
    {

        $cargos = explode(',', $role_id);


          $usuarios_cargos = DB::table('users as usuario')
            ->join('model_has_roles as usuario_cargo', 'usuario.id', '=', 'usuario_cargo.model_id')
            ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')
            ->join('user_parameters as up', 'up.users_id', '=', 'usuario.id')                    
            ->where('up.parameters_id', 1)
            ->whereIn('usuario_cargo.role_id', $cargos)
            ->where('usuario_cargo.role_id',"!=",2)
            ->whereNull('usuario.deleted_at')
            ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
            ->select(['usuario.id as id_usuario', 'usuario.email as email_usuario', 'up.value as nome_usuario', 'cargo.id as cargo_usuario'])
            ->orderBy('nome_usuario')
            ->groupBy('id_usuario')
            ->get();

        return response()->json($usuarios_cargos);
    }
    
        public static function getRoles($model_id)
    {




        $cargo = DB::table('users as usuario')
            ->join('model_has_roles as usuario_cargo', 'usuario.id', '=', 'usuario_cargo.model_id')

            ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')
            ->join('role_translations as roles_user', 'roles_user.role_id', '=', 'cargo.id')
            ->join('user_parameters as up', 'up.users_id', '=', 'usuario.id')
            ->where('up.parameters_id', 1)
            ->where('roles_user.active', 1)
            ->where('usuario_cargo.model_id', "=", $model_id)
            ->whereNull('usuario.deleted_at')
            ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
            ->select(['roles_user.display_name as role_user'])
            ->get();




        // echo "Error 502";

        echo $cargo[0]->role_user;
        if(count($cargo)==1){
        }else{
            echo "+"; 
        }

    }
    
}
