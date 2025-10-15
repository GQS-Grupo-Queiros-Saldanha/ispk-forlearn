<?php

namespace App\Modules\Users\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\Users\Models\Role;
use App\Modules\Users\Models\Parameter;
use App\Modules\Users\Models\ParameterGroup;
use App\Modules\Users\Models\ParameterRole;
use App\Modules\Users\Models\ParameterHasParameterGroup;
use App\Modules\Users\Models\ParameterGroupTranslation;
use App\Modules\Users\Models\ParameterOption;
use App\Modules\Users\Models\ParameterOptionTranslation;
use App\Modules\Users\Models\ParameterTranslation;
use App\Modules\Users\Requests\ParameterRequest;
use Carbon\Carbon;
use DataTables;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Log;
use Request;
use Throwable;
use Toastr;

class ParametersController extends Controller
{
    // Attributes
    private $types;
    private $show_options_when;

    // Constructor
    public function __construct()
    {
        $this->types = [
            'checkbox' => __('Users::parameters.checkbox'),
            'color' => __('Users::parameters.color'),
            'date' => __('Users::parameters.date'),
            'datetime-local' => __('Users::parameters.datetime-local'),
            'dropdown' => __('Users::parameters.dropdown'),
            'email' => __('Users::parameters.email'),
            'file_doc' => __('Users::parameters.file_doc'),
            'file_image' => __('Users::parameters.file_image'),
            'file_pdf' => __('Users::parameters.file_pdf'),
            'float' => __('Users::parameters.float'),
            'integer' => __('Users::parameters.integer'),
            'month' => __('Users::parameters.month'),
            'password' => __('Users::parameters.password'),
            'tel' => __('Users::parameters.tel'),
            'text' => __('Users::parameters.text'),
            'textarea' => __('Users::parameters.textarea'),
            'time' => __('Users::parameters.time'),
            'url' => __('Users::parameters.url'),
            'week' => __('Users::parameters.week'),
        ];
        $this->show_options_when = ['dropdown', 'checkbox'];
    }

    /**
     * Tells if a certain type has options or not
     * @param $type string Type
     * @return bool TRUE if yes, FALSE if no
     */
    private function hasOptions($type): bool
    {
        return in_array($type, $this->show_options_when, true);
    }


    public function index()
    {
        try {
            return view('Users::parameters.index');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {

            $model = Parameter::
            join('users as u1', 'u1.id', '=', 'parameters.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'parameters.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'parameters.deleted_by')
                ->leftJoin('parameter_translations as pt', function ($join) {
                    $join->on('pt.parameters_id', '=', 'parameters.id');
                    $join->on('pt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('pt.active', '=', DB::raw(true));
                })
                ->select([
                    'parameters.*',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    'pt.display_name'
                ]);

            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('Users::parameters.datatables.actions')->with('item', $item);
                })
                ->editColumn('type', function ($item) {
                    return $this->types[$item->type] ?? '';
                })
                ->editColumn('has_options', function ($item) {
                    return $item->has_options ? __('common.yes') : __('common.no');
                })
               /* ->editColumn('created_at', function ($item) {
                    return TimeHelper::time_elapsed_string($item->created_at);
                })
                ->editColumn('updated_at', function ($item) {
                    return TimeHelper::time_elapsed_string($item->updated_at);
                })
                ->editColumn('deleted_at', function ($item) {
                    return TimeHelper::time_elapsed_string($item->deleted_at);
                })*/
                ->rawColumns(['actions'])
                ->toJson();

        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function create()
    {
        try {

            $parameter_groups = ParameterGroup::with([
                'currentTranslation',
                'roles'
            ])->get();

            $roles = Role::with([
                'currentTranslation'
            ])->get();

            $parameters = Parameter::with([
                'currentTranslation',
                'groups' => function($q) {
                    $q->with([
                        'currentTranslation'
                    ]);
                }
            ])->get();

            $data = [
                'action' => 'create',
                'languages' => Language::whereActive(true)->get(),
                'parameters' => $parameters,
                'parameter_groups' => $parameter_groups,
                'roles' => $roles,
                'show_options_when' => $this->show_options_when,
                'types' => $this->types,
            ];

            return view('Users::parameters.parameter')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ParameterRequest $request
     * @return Response
     */
    public function store(ParameterRequest $request)
    {
        try {


            DB::beginTransaction();

            // Check if it has options
            $has_options = $this->hasOptions($request->get('type'));

            // Create
            $parameter = Parameter::create([
                'code' => $request->get('code'),
                'type' => $request->get('type'),
                'required' => $request->has('required'),
                'has_options' => $has_options,
            ]);

            $parameter->save();

            // Groups
            if ($request->has('groups')) {
                $parameter->groups()->sync($request->get('groups'));
            }

            // Roles
            if ($request->has('roles')) {
                $parameter->syncRoles($request->get('roles'));
            }

            // Create translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {

                $parameter_translations[] = [
                    'parameters_id' => $parameter->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id],
                    'description' => $request->get('description')[$language->id],
                    'created_at' => Carbon::now(),
                    'version' => 1,
                    'active' => true
                ];
            }

            if (!empty($parameter_translations)) {
                ParameterTranslation::insert($parameter_translations);
            }

            // If it has options
            if ($has_options && $request->has('options')) {

                // Create parameter options
                foreach ($request->get('options') as $option) {
                    $parameter_option = ParameterOption::create([
                        'parameters_id' => $parameter->id,
                        'code' => $option['value'],
                        'has_related_parameters' => isset($option['related_parameters'])
                    ]);
                    $parameter_option->save();

                    // Create translations
                    $parameter_options_translations = [];
                    foreach ($languages as $language) {

                        $parameter_options_translations[] = [
                            'parameter_options_id' => $parameter_option->id,
                            'language_id' => $language->id,
                            'display_name' => $option['display_name'][$language->id],
                            'description' => $option['description'][$language->id],
                            'created_at' => Carbon::now(),
                            'version' => 1,
                            'active' => true,
                        ];
                    }

                    if (!empty($parameter_options_translations)) {
                        ParameterOptionTranslation::insert($parameter_options_translations);
                    }

                    // Related parameters
                    if (isset($option['related_parameters'])) {

                        // Add order
                        $related_parameters = [];
                        foreach ($option['related_parameters'] as $order => $related_id) {
                            $related_parameters[$related_id] = [
                                'order' => $order
                            ];
                        }

                        $parameter_option->relatedParameters()->sync($related_parameters);
                    }
                }
            }

            DB::commit();

            // Success message
            Toastr::success(__('Users::parameters.store_success_message'), __('toastr.success'));
            return redirect()->route('parameters.index');

        } catch (Exception | Throwable $e) {
            Toastr::error($e->getMessage(), __('toastr.error'));
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    private function fetch($id, $action)
    {
        try {
            // Find
            $parameter = Parameter::whereId($id)->with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                },
                'groups' => function ($q) {
                    $q->with([
                        'currentTranslation'
                    ]);
                },
                'options' => function ($q) {
                    $q->with([
                        'relatedParameters',
                        'translations'
                    ]);
                }
            ])->firstOrFail();

            $parameters = Parameter::with([
                'currentTranslation'
            ])->where('id', '<>', $parameter->id)->get();

            $parameter_groups = ParameterGroup::with([
                'currentTranslation'
            ])->get();

            $roles = Role::with([
                'currentTranslation'
            ])->get();

            $data = [
                'action' => $action,
                'parameter' => $parameter,
                'parameters' => $parameters,
                'parameter_groups' => $parameter_groups,
                'roles' => $roles,
                'show_options_when' => $this->show_options_when,
                'translations' => $parameter->translations->keyBy('language_id')->toArray(),
                'types' => $this->types,
                'languages' => Language::whereActive(true)->get()
            ];

            return view('Users::parameters.parameter')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Users::parameters.not_found_message'), __('toastr.error'));
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
     * @return Response
     */
    public function show($id)
    {
        try {
            return $this->fetch($id, 'show');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        try {
            return $this->fetch($id, 'edit');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ParameterRequest $request
     * @param int $id
     * @return Response
     */
    public function update(ParameterRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            
            // Check if it has options
            $has_options = $this->hasOptions($request->get('type'));

            // Fetch the default language
            $default_language = Language::whereDefault(true)->firstOrFail();

            // Find and update
            $parameter = Parameter::whereId($id)->firstOrFail();
            $parameter->code = $request->get('code');
            $parameter->type = $request->get('type');
            $parameter->required = $request->has('required');
            $parameter->has_options = $has_options;
            $parameter->save();

            // Disable previous translations
            ParameterTranslation::where('parameters_id', $parameter->id)->update(['active' => false]);

            $version = ParameterTranslation::where('parameters_id', $parameter->id)->whereLanguageId($default_language->id)->count() + 1;

            // Associated translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {
                $parameter_translations[] = [
                    'parameters_id' => $parameter->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id] ?? null,
                    'description' => $request->get('description')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => $version,
                    'active' => true,
                ];
            }

            if (!empty($parameter_translations)) {
                ParameterTranslation::insert($parameter_translations);
            }

            // TODO: 1. alterar os que existem
            // TODO: 2. adicionar os novos
            // TODO: 3. eliminar os removidos
            if ($has_options && $request->has('options')) {

                // 1.
                $parameter_options_to_update = $parameter->options->whereIn('id', array_column($request->get('options'), 'id'));
                foreach ($parameter_options_to_update as $parameter_to_update) {

                    foreach ($request->get('options') as $index => $option) {
                        if ((int)$option['id'] === $parameter_to_update->id) {

                            // Update option
                            $parameter_to_update->update([
                                'code' => $option['value'],
                                'has_related_parameters' => !empty($option['related_parameters']),
                            ]);

                            // Update option translations
                            ParameterOptionTranslation::whereParameterOptionsId($parameter_to_update->id)->update(['active' => false]);
                            $version = ParameterOptionTranslation::whereParameterOptionsId($parameter_to_update->id)->whereLanguageId($default_language->id)->count() + 1;
                            $parameter_options_translations = [];
                            foreach ($languages as $language) {

                                $parameter_options_translations[] = [
                                    'parameter_options_id' => $parameter_to_update->id,
                                    'language_id' => $language->id,
                                    'display_name' => $option['display_name'][$language->id],
                                    'description' => $option['description'][$language->id],
                                    'created_at' => Carbon::now(),
                                    'version' => $version,
                                    'active' => true,
                                ];
                            }
                            if (!empty($parameter_options_translations)) {
                                ParameterOptionTranslation::insert($parameter_options_translations);
                            }

                            // Update related parameters
                            if (!empty($option['related_parameters'])) {

                                // Add order
                                $related_parameters = [];
                                foreach ($option['related_parameters'] as $order => $related_id) {
                                    $related_parameters[$related_id] = [
                                        'order' => $order
                                    ];
                                }

                                $parameter_to_update->relatedParameters()->sync($related_parameters);
                            } else {
                                $parameter_to_update->relatedParameters()->sync([]);
                            }

                            unset($request->has('options')[$index]);
                            break;
                        }
                    }
                }

                // 3.
                $parameter_options_to_delete = $parameter->options->whereNotIn('id', $parameter_options_to_update->pluck('id'));
                foreach ($parameter_options_to_delete as $parameter_to_delete) {
                    //$parameter_to_delete->translations()->delete();
                    $parameter_to_delete->delete();
                }

                // 2.
                $parameter_options_to_insert = collect($request->get('options'))->filter(function ($item) {
                    return !isset($item['id']);
                });
                foreach ($parameter_options_to_insert as $option) {
                    $parameter_option = ParameterOption::create([
                        'parameters_id' => $parameter->id,
                        'code' => $option['value'],
                        'has_related_parameters' => !empty($option['related_parameters']),
                    ]);
                    $parameter_option->save();

                    // Create translations
                    $parameter_options_translations = [];
                    foreach ($languages as $language) {

                        $parameter_options_translations[] = [
                            'parameter_options_id' => $parameter_option->id,
                            'language_id' => $language->id,
                            'display_name' => $option['display_name'][$language->id],
                            'description' => $option['description'][$language->id],
                            'created_at' => Carbon::now(),
                            'version' => 1,
                            'active' => true,
                        ];
                    }

                    if (!empty($parameter_options_translations)) {
                        ParameterOptionTranslation::insert($parameter_options_translations);
                    }

                    // Related parameters
                    if (!empty($option['related_parameters'])) {

                        // Add order
                        $related_parameters = [];
                        foreach ($option['related_parameters'] as $order => $related_id) {
                            $related_parameters[$related_id] = [
                                'order' => $order
                            ];
                        }

                        $parameter_option->relatedParameters()->sync($related_parameters);
                    } else {
                        $parameter_option->relatedParameters()->sync([]);
                    }
                }

            }
           
            // Roles
            if ($request->has('roles')) {
                $parameter->syncRoles($request->get('roles'));
            } else {
                $parameter->syncRoles([]);
            }

            // Groups
            if ($request->has('groups')) {
                $parameter->groups()->sync($request->get('groups'));
            } else {
                $parameter->groups()->sync([]);
            }

            DB::commit();

            // Success message
            Toastr::success(__('Users::parameters.update_success_message'), __('toastr.success'));
            return redirect()->route('parameters.show', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Users::parameters.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                Toastr::error(__('Users::parameters.being_used_message'), __('toastr.error'));
                Log::error($e);
                return redirect()->back() ?? abort(500);
            }
            throw $e;
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            // Find and delete
            $parameter = Parameter::whereId($id)->firstOrFail();
            $parameter->translations()->forceDelete();

            foreach ($parameter->options as $option) {
                $option->translations()->forceDelete();
                $option->relatedParameters()->sync([]);
            }
            $parameter->options()->forceDelete();

            $parameter->groups()->sync([]);
            $parameter->users()->sync([]);
            $parameter->forceDelete();

            DB::commit();

            // Success message
            Toastr::success(__('Users::parameters.destroy_success_message'), __('toastr.success'));
            return redirect()->route('parameters.index');
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Users::parameters.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function exists(\Illuminate\Http\Request $request)
    {
        $json = [];
        if ($request->has('field') && $request->has('value')) {
            $parameter = Parameter::where($request->get('field'), '=', $request->get('value'));

            if (!empty($request->get('ignored_id'))) {
                $parameter = $parameter->whereKeyNot($request->get('ignored_id'));
            }

            $json['success'] = !$parameter->exists();
        }
        return \response()->json($json);
    }

    public function optionExists(\Illuminate\Http\Request $request)
    {
        $json = [];
        if ($request->has('code')) {
            $json['success'] = !ParameterOption::whereCode($request->get('code'))->exists();
        }
        return \response()->json($json);
    }
}
