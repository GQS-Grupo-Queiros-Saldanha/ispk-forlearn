<?php

namespace App\Modules\Users\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\Users\Models\Parameter;
use App\Modules\Users\Models\ParameterGroup;
use App\Modules\Users\Models\ParameterGroupTranslation;
use App\Modules\Users\Models\Role;
use App\Modules\Users\Requests\ParameterGroupRequest;
use Carbon\Carbon;
use DataTables;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Log;
use Illuminate\Http\Request;
use Throwable;
use Toastr;

class ParameterGroupsController extends Controller
{

    public function index()
    {
        try {
            return view('Users::parameter-groups.index');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {

            $model = ParameterGroup::join('users as u1', 'u1.id', '=', 'parameter_groups.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'parameter_groups.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'parameter_groups.deleted_by')
                ->leftJoin('parameter_group_translations as pgt', function ($join) {
                    $join->on('pgt.parameter_group_id', '=', 'parameter_groups.id');
                    $join->on('pgt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('pgt.active', '=', DB::raw(true));
                })
                ->select([
                    'parameter_groups.*',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    'pgt.display_name'
                ]);

            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('Users::parameter-groups.datatables.actions')->with('item', $item);
                })
              /*  ->editColumn('created_at', function ($item) {
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

            // Roles
            $roles = Role::with([
                'currentTranslation'
            ])->get();

            $data = [
                'action' => 'create',
                'roles' => $roles,
                'languages' => Language::whereActive(true)->get()
            ];
            return view('Users::parameter-groups.parameter-group')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ParameterGroupRequest $request
     * @return Response
     */
    public function store(ParameterGroupRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create
            $parameter_group = ParameterGroup::create([
                'code' => $request->get('code')
            ]);
            $parameter_group->save();

            // Create translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {

                $parameter_group_translations[] = [
                    'parameter_group_id' => $parameter_group->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id],
                    'description' => $request->get('description')[$language->id],
                    'created_at' => Carbon::now(),
                    'version' => 1,
                    'active' => true
                ];
            }

            if (!empty($parameter_group_translations)) {
                ParameterGroupTranslation::insert($parameter_group_translations);
            }

            // Roles
            if ($request->has('roles')) {
                $parameter_group->syncRoles($request->get('roles'));
            }

            DB::commit();

            // Success message
            Toastr::success(__('Users::parameter-groups.store_success_message'), __('toastr.success'));
            return redirect()->route('parameter-groups.index');

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
            $parameter_group = ParameterGroup::whereId($id)->with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                }
            ])->firstOrFail();

            // Roles
            $roles = Role::with([
                'currentTranslation'
            ])->get();

            $data = [
                'action' => $action,
                'parameter_group' => $parameter_group,
                'roles' => $roles,
                'translations' => $parameter_group->translations->keyBy('language_id')->toArray(),
                'languages' => Language::whereActive(true)->get()
            ];

            return view('Users::parameter-groups.parameter-group')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Users::parameter-groups.not_found_message'), __('toastr.error'));
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
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
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
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ParameterGroupRequest $request
     * @param int $id
     * @return Response
     */
    public function update(ParameterGroupRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Fetch the default language
            $default_language = Language::whereDefault(true)->firstOrFail();

            // Find and update
            $parameter_group = ParameterGroup::whereId($id)->firstOrFail();
            $parameter_group->code = $request->get('code');
            $parameter_group->save();

            // Disable previous translations
            ParameterGroupTranslation::where('parameter_group_id', $parameter_group->id)->update(['active' => false]);

            $version = ParameterGroupTranslation::where('parameter_group_id', $parameter_group->id)->whereLanguageId($default_language->id)->count() + 1;

            // Associated translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {
                $parameter_group_translations[] = [
                    'parameter_group_id' => $parameter_group->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id] ?? null,
                    'description' => $request->get('description')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => $version,
                    'active' => true,
                ];
            }

            if (!empty($parameter_group_translations)) {
                ParameterGroupTranslation::insert($parameter_group_translations);
            }

            // Roles
            if ($request->has('roles')) {
                $parameter_group->syncRoles($request->get('roles'));
            } else {
                $parameter_group->syncRoles([]);
            }

            DB::commit();

            // Success message
            Toastr::success(__('Users::parameter-groups.update_success_message'), __('toastr.success'));
            return redirect()->route('parameter-groups.show', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Users::parameter-groups.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            // Find and delete
            $parameter_group = ParameterGroup::whereId($id)->firstOrFail();
            $parameter_group->parameters->sync([]);
            $parameter_group->translations()->forceDelete();
            $parameter_group->delete();

            DB::commit();

            // Success message
            Toastr::success(__('Users::parameter-groups.destroy_success_message'), __('toastr.success'));
            return redirect()->route('parameter-groups.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Users::parameter-groups.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function order()
    {
        try {
            $parameter_groups = ParameterGroup::with([
                'translation' => function ($q) {
                    $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
                }
            ])->orderBy('order')->get();

            $data = [
                'parameter_groups' => $parameter_groups
            ];

            return view('Users::parameter-groups.order')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function saveOrder(Request $request)
    {
        try {

            DB::beginTransaction();

            if ($request->has('parameter_groups')) {
                foreach ($request->get('parameter_groups') as $order => $parameter_group_id) {
                    $parameter_group = ParameterGroup::findOrFail($parameter_group_id);
                    $parameter_group->order = $order;
                    $parameter_group->save();
                }
            }

            DB::commit();

            return \response()->json([]);

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Users::parameter-groups.not_found_message'), __('toastr.error'));
            Log::error($e);
            //return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function parameterOrder($id)
    {
        try {
            $parameter_group = ParameterGroup::with([
                'parameters' => function ($q) {
                    $q->with([
                        'currentTranslation'
                    ])->orderBy('order');
                }
            ])->findOrFail($id);

            $data = [
                'parameter_group' => $parameter_group
            ];

            return view('Users::parameter-groups.parameter-order')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Users::parameter-groups.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function saveParameterOrder($id, Request $request)
    {
        try {

            DB::beginTransaction();

            $parameter_group = ParameterGroup::with(['parameters'])->findOrFail($id);

            if ($request->has('parameters')) {
                foreach ($request->get('parameters') as $order => $parameter_id) {
                    $parameter_group->parameters()->updateExistingPivot($parameter_id, ['order' => $order]);
                }
            }

            $parameter_group->save();

            DB::commit();

            return \response()->json($parameter_group->parameters);

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Users::parameter-groups.not_found_message'), __('toastr.error'));
            Log::error($e);
            //return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
}
