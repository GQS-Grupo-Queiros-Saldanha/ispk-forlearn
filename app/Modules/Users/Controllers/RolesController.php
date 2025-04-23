<?php

namespace App\Modules\Users\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\Users\Models\Permission;
use App\Modules\Users\Models\Role;
use App\Modules\Users\Models\RoleTranslation;
use App\Modules\Users\Requests\RoleRequest;
use Carbon\Carbon;
use DataTables;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Log;
use Throwable;
use Toastr;
use Auth;

class RolesController extends Controller
{

    public function index()
    {
        try {
            return view('Users::roles.index');
        } catch (Exception | Throwable $e) {
            logError($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {

            $model = Role::join('users as u1', 'u1.id', '=', 'roles.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'roles.updated_by')
                ->leftJoin('role_translations as rt', function ($join) {
                    $join->on('rt.role_id', '=', 'roles.id');
                    $join->on('rt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('rt.active', '=', DB::raw(true));
                })
                //->where('roles.id','!=',2)
                ->select([
                    'roles.*',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'rt.display_name'
                ]);

            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('Users::roles.datatables.actions')->with('item', $item);
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
            logError($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function create()
    {
        try {
            $data = [
                'action' => 'create',
                'languages' => Language::whereActive(true)->get()
            ];
            return view('Users::roles.role')->with($data);
        } catch (Exception | Throwable $e) {
            logError($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param RoleRequest $request
     * @return Response
     */
    public function store(RoleRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create
            $role = Role::create([
                'name' => $request->get('name'),
                'guard_name' => 'web',
                'created_by' => auth()->user()->id
            ]);
            $role->save();

            // Create translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {

                $role_translations[] = [
                    'role_id' => $role->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id],
                    'description' => $request->get('description')[$language->id],
                    'created_at' => Carbon::now(),
                    'version' => 1,
                    'active' => true
                ];
            }

            if (!empty($role_translations)) {
                RoleTranslation::insert($role_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('Users::roles.store_success_message'), __('toastr.success'));

            return redirect()->route('roles.index');
        } catch (Exception | Throwable $e) {
            dd($e);
            logError($e);
            Toastr::error($e->getMessage(), __('toastr.success'));
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    private function fetch($id, $action)
    {
        try {
            // Find
            $role = Role::whereId($id)->with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                }
            ])->firstOrFail();

            $data = [
                'action' => $action,
                'role' => $role,
                'translations' => $role->translations->keyBy('language_id')->toArray(),
                'languages' => Language::whereActive(true)->get()
            ];
            return view('Users::roles.role')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Users::roles.not_found_message'), __('toastr.error'));
            logError($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            logError($e);
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
            logError($e);
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
            logError($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param RoleRequest $request
     * @param int $id
     * @return Response
     */
    public function update(RoleRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Fetch the default language
            $default_language = Language::whereDefault(true)->firstOrFail();

            // Find and update
            $role = Role::whereId($id)->firstOrFail();
            $role->name = $request->get('name');
            $role->save();

            // Disable previous translations
            RoleTranslation::whereRoleId($id)->update(['active' => false]);

            $version = RoleTranslation::whereRoleId($role->id)->whereLanguageId($default_language->id)->count() + 1;

            // Associated translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {
                $role_translations[] = [
                    'role_id' => $role->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id] ?? null,
                    'description' => $request->get('description')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => $version,
                    'active' => true,
                ];
            }

            if (!empty($role_translations)) {
                RoleTranslation::insert($role_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('Users::roles.update_success_message'), __('toastr.success'));
            return redirect()->route('roles.show', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Users::roles.not_found_message'), __('toastr.error'));
            logError($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            logError($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     * @throws Exception
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            // Find and delete
            $role = Role::whereId($id)->firstOrFail();
            $role->translations()->forceDelete();
            $role->delete();

            DB::commit();

            // Success message
            Toastr::success(__('Users::roles.destroy_success_message'), __('toastr.success'));
            return redirect()->route('roles.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Users::roles.not_found_message'), __('toastr.error'));
            logError($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            logError($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function permissions($id)
    {
        try {
            // Find
            $role = Role::whereId($id)->firstOrFail();

            $data = [
                'role' => $role
            ];
            return view('Users::roles.permissions')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Users::roles.not_found_message'), __('toastr.error'));
            logError($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            logError($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function permissionsAjax($id)
    {
        try {
            // Fetch the role with permissions
            $role = Role::whereId($id)->with(['permissions.createdBy', 'permissions.updatedBy'])->firstOrFail();

            if(Auth::check() && Auth::user()->hasRole('superadmin')) {

            // Prepare the object to use to compare
            $permissions = $role->permissions->pluck('id')->toArray();

            $model = Permission::join('users as u1', 'u1.id', '=', 'permissions.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'permissions.updated_by')
                ->leftJoin('permission_translations as pt', function ($join) {
                    $join->on('pt.permission_id', '=', 'permissions.id');
                    $join->on('pt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('pt.active', '=', DB::raw(true));
                })
                ->where("pt.display_name","!=",".")
                ->select(['permissions.*', 'u1.name as created_by', 'u2.name as updated_by', 'pt.display_name']);

            // Return the datatable
            return Datatables::eloquent($model)
                ->addColumn('select', function ($item) use ($permissions) {
                    return view('Users::roles.datatables.select', ['id' => $item->id, 'checked' => in_array($item->id, $permissions, true)]);
                })
                ->editColumn('created_at', function ($item) {
                    return TimeHelper::time_elapsed_string($item->created_at);
                })
                ->editColumn('updated_at', function ($item) {
                    return TimeHelper::time_elapsed_string($item->updated_at);
                })
                ->rawColumns(['select'])
                ->toJson();
            }else{
                            // Prepare the object to use to compare
            $permissions = $role->permissions->pluck('id')->toArray();

            $model = Permission::join('users as u1', 'u1.id', '=', 'permissions.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'permissions.updated_by')
                ->leftJoin('permission_translations as pt', function ($join) {
                    $join->on('pt.permission_id', '=', 'permissions.id');
                    $join->on('pt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('pt.active', '=', DB::raw(true));
                })
                ->where("pt.display_name","!=",".")
                ->where("permissions.name","!=","super-admin")
                ->select(['permissions.*', 'u1.name as created_by', 'u2.name as updated_by', 'pt.display_name']);

            // Return the datatable
            return Datatables::eloquent($model)
                ->addColumn('select', function ($item) use ($permissions) {
                    return view('Users::roles.datatables.select', ['id' => $item->id, 'checked' => in_array($item->id, $permissions, true)]);
                })
                ->editColumn('created_at', function ($item) {
                    return TimeHelper::time_elapsed_string($item->created_at);
                })
                ->editColumn('updated_at', function ($item) {
                    return TimeHelper::time_elapsed_string($item->updated_at);
                })
                ->rawColumns(['select'])
                ->toJson();
            }
        } catch (Exception | Throwable $e) {
            logError($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function permissionsSave(Request $request, $id)
    {
        try {
            // Find
            $role = Role::whereId($id)->firstOrFail();

            $role->syncPermissions($request->get('items'));

            // Success message
            Toastr::success(__('Users::roles.permissions_success_message'), __('toastr.success'));
            return redirect()->route('roles.permissions', $role->id);

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Users::roles.not_found_message'), __('toastr.error'));
            logError($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            logError($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

}
