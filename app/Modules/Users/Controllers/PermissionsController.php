<?php

namespace App\Modules\Users\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\Users\Models\Permission;
use App\Modules\Users\Models\PermissionTranslation;
use App\Modules\Users\Requests\PermissionRequest;
use Carbon\Carbon;
use DataTables;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Log;
use Throwable;
use Toastr;
use Auth;


class PermissionsController extends Controller
{

    public function index()
    {
        try {
            return view('Users::permissions.index');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {

            if(Auth::check() && Auth::user()->hasRole('superadmin')) {
                $model = Permission::
                    join('users as u1', 'u1.id', '=', 'permissions.created_by')
                        ->leftJoin('users as u2', 'u2.id', '=', 'permissions.updated_by')
                        ->leftJoin('permission_translations as pt', function ($join) {
                            $join->on('pt.permission_id', '=', 'permissions.id');
                            $join->on('pt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                            $join->on('pt.active', '=', DB::raw(true));
                        })
                        ->select([
                            'permissions.*',
                            'u1.name as created_by',
                            'u2.name as updated_by',
                            'pt.display_name'
                        ]);

                    return Datatables::eloquent($model)
                        ->addColumn('actions', function ($item) {
                            return view('Users::permissions.datatables.actions')->with('item', $item);
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
            }else{
                $model = Permission::join('users as u1', 'u1.id', '=', 'permissions.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'permissions.updated_by')
                ->leftJoin('permission_translations as pt', function ($join) {
                    $join->on('pt.permission_id', '=', 'permissions.id');
                    $join->on('pt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('pt.active', '=', DB::raw(true));
                })
                ->where("permissions.name","!=","super-admin")
                ->select([
                    'permissions.*',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'pt.display_name'
                ]);

            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('Users::permissions.datatables.actions')->with('item', $item);
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
            } 

            
        } catch (Exception | Throwable $e) {
            Log::error($e);
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
            return view('Users::permissions.permission')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PermissionRequest $request
     * @return Response
     */
    public function store(PermissionRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create permission
            $permission = Permission::create([
                'name' => $request->get('name'),
                'guard_name' => 'web',
                'created_by' => $request->user()->id
            ]);
            $permission->save();

            // Create translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {

                $permission_translations[] = [
                    'permission_id' => $permission->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id],
                    'description' => $request->get('description')[$language->id],
                    'created_at' => Carbon::now(),
                    'version' => 1,
                    'active' => true
                ];
            }

            if (!empty($permission_translations)) {
                PermissionTranslation::insert($permission_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('Users::permissions.store_success_message'), __('toastr.success'));
            return redirect()->route('permissions.index');

        } catch (Exception | Throwable $e) {
            Toastr::error($e->getMessage(), __('toastr.success'));
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    private function fetch($id, $action)
    {
        try {
            // Find permission
            $permission = Permission::whereId($id)->with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                }
            ])->firstOrFail();

            $data = [
                'action' => $action,
                'permission' => $permission,
                'translations' => $permission->translations->keyBy('language_id')->toArray(),
                'languages' => Language::whereActive(true)->get()
            ];
            return view('Users::permissions.permission')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Users::permissions.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return abort(500);
        }
    }

    public function show($id)
    {
        try {
            return $this->fetch($id, 'show');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

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
     * @param PermissionRequest $request
     * @param  int $id
     * @return Response
     */
    public function update(PermissionRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Fetch the default language
            $default_language = Language::whereDefault(true)->firstOrFail();

            // Find and update
            $permission = Permission::whereId($id)->firstOrFail();
            $permission->name = $request->get('name');
            $permission->save();

            // Disable previous translations
            PermissionTranslation::wherePermissionId($id)->update(['active' => false]);

            $version = PermissionTranslation::wherePermissionId($permission->id)->whereLanguageId($default_language->id)->count() + 1;

            // Associated translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {
                $permission_translations[] = [
                    'permission_id' => $permission->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id] ?? null,
                    'description' => $request->get('description')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => $version,
                    'active' => true
                ];
            }

            if (!empty($permission_translations)) {
                PermissionTranslation::insert($permission_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('Users::permissions.update_success_message'), __('toastr.success'));
            return redirect()->route('permissions.show', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Users::permissions.not_found_message'), __('toastr.error'));
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
     * @param  int $id
     * @return Response
     * @throws Exception
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            // Find and delete
            $permission = Permission::whereId($id)->firstOrFail();
            $permission->translations()->forceDelete();
            $permission->delete();

            DB::commit();

            // Success message
            Toastr::success(__('Users::permissions.destroy_success_message'), __('toastr.success'));
            return redirect()->route('permissions.index');
        } catch (QueryException $e) {
            // Integrity violation
            if ($e->getCode() === '23000') {
                Toastr::error(__('Users::permissions.destroy_integrity_violation_message'), __('toastr.error'));
            }
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Users::permissions.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function permissions($id)
    {
        try {
            // Find
            $permission = Permission::whereId($id)->firstOrFail();

            $data = [
                'permission' => $permission
            ];
            return view('Users::permissions.permissions')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Users::permissions.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function savePermissions(Request $request, $id)
    {
        try {
            // Find
            $permission = Permission::whereId($id)->firstOrFail();

            // Remove all permissions
            $permission->permissions()->detach();

            if ($request->has('permissions')) {

                $failed = 0;

                // Associate selected permissions
                foreach ($request->get('permissions') as $permission) {

                    $permission = Permission::find($permission);
                    if ($permission) {
                        $permission->permissions()->attach($permission);
                    } else {
                        $failed++;
                    }

                }

                // If all failed
                if ($failed === count($request->get('permissions'))) {
                    Toastr::error(__('Users::permissions.permissions_associate_all_error_message'), __('toastr.error'));
                    return redirect()->route('permissions.permissions', $permission->id);
                }

                // If some failed
                if ($failed > 0) {
                    Toastr::warning(__('Users::permissions.permissions_associate_some_error_message'), __('toastr.warning'));
                    return redirect()->route('permissions.permissions', $permission->id);
                }
            }

            // Success message
            Toastr::success(__('Users::permissions.permissions_success_message'), __('toastr.success'));
            return redirect()->route('permissions.permissions', $permission->id);

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Users::permissions.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
}
