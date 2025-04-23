<?php

namespace App\Modules\Users\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\Users\Models\DegreeLevel;
use App\Modules\Users\Models\DegreeLevelTranslation;
use App\Modules\Users\Requests\DegreeLevelRequest;
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

class DegreeLevelsController extends Controller
{

    public function index()
    {
        try {
            return view('Users::degree-levels.index');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {

            $model = DegreeLevel::join('users as u1', 'u1.id', '=', 'degree_levels.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'degree_levels.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'degree_levels.deleted_by')
                ->leftJoin('degree_level_translations as dlt', function ($join) {
                    $join->on('dlt.degree_levels_id', '=', 'degree_levels.id');
                    $join->on('dlt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dlt.active', '=', DB::raw(true));
                })
                ->select([
                    'degree_levels.*',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    'dlt.display_name'
                ]);

            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('Users::degree-levels.datatables.actions')->with('item', $item);
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
            return view('Users::degree-levels.degree-level')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param DegreeLevelRequest $request
     * @return Response
     */
    public function store(DegreeLevelRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create
            $degree_level = DegreeLevel::create([
                'code' => $request->get('code')
            ]);
            $degree_level->save();

            // Create translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {

                $degree_level_translations[] = [
                    'degree_levels_id' => $degree_level->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id],
                    'description' => $request->get('description')[$language->id],
                    'created_at' => Carbon::now(),
                    'version' => 1,
                    'active' => true
                ];
            }

            if (!empty($degree_level_translations)) {
                DegreeLevelTranslation::insert($degree_level_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('Users::degree-levels.store_success_message'), __('toastr.success'));
            return redirect()->route('degree-levels.index');

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
            $degree_level = DegreeLevel::whereId($id)->with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                }
            ])->firstOrFail();

            $data = [
                'action' => $action,
                'degree_level' => $degree_level,
                'translations' => $degree_level->translations->keyBy('language_id')->toArray(),
                'languages' => Language::whereActive(true)->get()
            ];
            return view('Users::degree-levels.degree-level')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Users::degree-levels.not_found_message'), __('toastr.error'));
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
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
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
     * @param DegreeLevelRequest $request
     * @param  int $id
     * @return Response
     */
    public function update(DegreeLevelRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Fetch the default language
            $default_language = Language::whereDefault(true)->firstOrFail();

            // Find and update
            $degree_level = DegreeLevel::whereId($id)->firstOrFail();
            $degree_level->code = $request->get('code');
            $degree_level->save();

            // Disable previous translations
            DegreeLevelTranslation::where('degree_levels_id', $degree_level->id)->update(['active' => false]);

            $version = DegreeLevelTranslation::where('degree_levels_id', $degree_level->id)->whereLanguageId($default_language->id)->count() + 1;

            // Associated translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {
                $degree_level_translations[] = [
                    'degree_levels_id' => $degree_level->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id] ?? null,
                    'description' => $request->get('description')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => $version,
                    'active' => true,
                ];
            }

            if (!empty($degree_level_translations)) {
                DegreeLevelTranslation::insert($degree_level_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('Users::degree-levels.update_success_message'), __('toastr.success'));
            return redirect()->route('degree-levels.show', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Users::degree-levels.not_found_message'), __('toastr.error'));
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
            $degree_level = DegreeLevel::whereId($id)->firstOrFail();
            $degree_level->delete();

            DB::commit();

            // Success message
            Toastr::success(__('Users::degree-levels.destroy_success_message'), __('toastr.success'));
            return redirect()->route('degree-levels.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Users::degree-levels.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
}
