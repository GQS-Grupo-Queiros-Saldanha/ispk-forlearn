<?php

namespace App\Modules\GA\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\OptionalGroup;
use App\Modules\GA\Models\OptionalGroupTranslation;
use App\Modules\GA\Requests\OptionalGroupRequest;
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

class OptionalGroupsController extends Controller
{

    public function index()
    {
        try {
            return view('GA::optional-groups.index');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {

            $model = OptionalGroup::join('users as u1', 'u1.id', '=', 'optional_groups.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'optional_groups.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'optional_groups.deleted_by')
                ->leftJoin('optional_groups_translations as ogt', function ($join) {
                    $join->on('ogt.optional_groups_id', '=', 'optional_groups.id');
                    $join->on('ogt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ogt.active', '=', DB::raw(true));
                })
                ->select(['optional_groups.*', 'u1.name as created_by', 'u2.name as updated_by', 'u3.name as deleted_by', 'ogt.display_name']);
            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('GA::optional-groups.datatables.actions')->with('item', $item);
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
            $data = [
                'action' => 'create',
                'languages' => Language::whereActive(true)->get()
            ];
            return view('GA::optional-groups.optional-group')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param OptionalGroupRequest $request
     * @return Response
     */
    public function store(OptionalGroupRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create
            $optional_group = OptionalGroup::create([
                'code' => $request->get('code')
            ]);
            $optional_group->save();

            // Create translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {

                $optional_group_translations[] = [
                    'optional_groups_id' => $optional_group->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id],
                    'description' => $request->get('description')[$language->id],
                    'abbreviation' => $request->get('abbreviation')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => 1,
                    'active' => true
                ];
            }

            if (!empty($optional_group_translations)) {
                OptionalGroupTranslation::insert($optional_group_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::optional-groups.store_success_message'), __('toastr.success'));
            return redirect()->route('optional-groups.index');

        } catch (Exception | Throwable $e) {
            Toastr::error($e->getMessage(), __('toastr.error'));
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function show($id)
    {
        try {
            return $this->fetch($id, 'show');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    private function fetch($id, $action)
    {
        try {
            // Find
            $optional_group = OptionalGroup::whereId($id)->with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                }
            ])->firstOrFail();

            $data = [
                'action' => $action,
                'optional_group' => $optional_group,
                'translations' => $optional_group->translations->keyBy('language_id')->toArray(),
                'languages' => Language::whereActive(true)->get()
            ];
            return view('GA::optional-groups.optional-group')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::optional-groups.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return abort(500);
        }
    }

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
     * @param OptionalGroupRequest $request
     * @param  int $id
     * @return Response
     */
    public function update(OptionalGroupRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Fetch the default language
            $default_language = Language::whereDefault(true)->firstOrFail();

            // Find and update
            $optional_group = OptionalGroup::whereId($id)->firstOrFail();
            $optional_group->code = $request->get('code');
            $optional_group->save();

            // Disable previous translations
            OptionalGroupTranslation::where('optional_groups_id', $optional_group->id)->update(['active' => false]);

            $version = OptionalGroupTranslation::where('optional_groups_id', $optional_group->id)->whereLanguageId($default_language->id)->count() + 1;

            // Associated translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {
                $optional_group_translations[] = [
                    'optional_groups_id' => $optional_group->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id] ?? null,
                    'description' => $request->get('description')[$language->id] ?? null,
                    'abbreviation' => $request->get('abbreviation')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => $version,
                    'active' => true,
                ];
            }

            if (!empty($optional_group_translations)) {
                OptionalGroupTranslation::insert($optional_group_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::optional-groups.update_success_message'), __('toastr.success'));
            return redirect()->route('optional-groups.show', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::optional-groups.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
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
            $optional_group = OptionalGroup::whereId($id)->firstOrFail();
            $optional_group->delete();

            DB::commit();

            // Success message
            Toastr::success(__('GA::optional-groups.destroy_success_message'), __('toastr.success'));
            return redirect()->route('optional-groups.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::optional-groups.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
}
