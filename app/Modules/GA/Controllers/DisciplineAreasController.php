<?php

namespace App\Modules\GA\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\DisciplineArea;
use App\Modules\GA\Models\DisciplineAreaTranslation;
use App\Modules\GA\Requests\DisciplineAreaRequest;
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

class DisciplineAreasController extends Controller
{

    public function index()
    {
        try {
            return view('GA::discipline-areas.index');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {

            $model = DisciplineArea::join('users as u1', 'u1.id', '=', 'discipline_areas.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'discipline_areas.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'discipline_areas.deleted_by')
                ->leftJoin('discipline_areas_translations as dat', function ($join) {
                    $join->on('dat.discipline_areas_id', '=', 'discipline_areas.id');
                    $join->on('dat.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dat.active', '=', DB::raw(true));
                })
                ->select([
                    'discipline_areas.*',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    'dat.display_name'
                ]);

            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('GA::discipline-areas.datatables.actions')->with('item', $item);
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
            return view('GA::discipline-areas.discipline-area')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param DisciplineAreaRequest $request
     * @return Response
     */
    public function store(DisciplineAreaRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create
            $discipline_area = DisciplineArea::create([
                'code' => $request->get('code')
            ]);
            $discipline_area->save();

            // Create translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {

                $discipline_area_translations[] = [
                    'discipline_areas_id' => $discipline_area->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id],
                    'description' => $request->get('description')[$language->id],
                    'abbreviation' => $request->get('abbreviation')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => 1,
                    'active' => true
                ];
            }

            if (!empty($discipline_area_translations)) {
                DisciplineAreaTranslation::insert($discipline_area_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::discipline-areas.store_success_message'), __('toastr.success'));
            return redirect()->route('discipline-areas.index');

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
            $discipline_area = DisciplineArea::whereId($id)->with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                }
            ])->firstOrFail();

            $data = [
                'action' => $action,
                'discipline_area' => $discipline_area,
                'translations' => $discipline_area->translations->keyBy('language_id')->toArray(),
                'languages' => Language::whereActive(true)->get()
            ];
            return view('GA::discipline-areas.discipline-area')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::discipline-areas.not_found_message'), __('toastr.error'));
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
     * @param DisciplineAreaRequest $request
     * @param  int $id
     * @return Response
     */
    public function update(DisciplineAreaRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Fetch the default language
            $default_language = Language::whereDefault(true)->firstOrFail();

            // Find and update
            $discipline_area = DisciplineArea::whereId($id)->firstOrFail();
            $discipline_area->code = $request->get('code');
            $discipline_area->save();

            // Disable previous translations
            DisciplineAreaTranslation::where('discipline_areas_id', $discipline_area->id)->update(['active' => false]);

            $version = DisciplineAreaTranslation::where('discipline_areas_id', $discipline_area->id)->whereLanguageId($default_language->id)->count() + 1;

            // Associated translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {
                $discipline_area_translations[] = [
                    'discipline_areas_id' => $discipline_area->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id] ?? null,
                    'description' => $request->get('description')[$language->id] ?? null,
                    'abbreviation' => $request->get('abbreviation')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => $version,
                    'active' => true,
                ];
            }

            if (!empty($discipline_area_translations)) {
                DisciplineAreaTranslation::insert($discipline_area_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::discipline-areas.update_success_message'), __('toastr.success'));
            return redirect()->route('discipline-areas.show', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::discipline-areas.not_found_message'), __('toastr.error'));
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
            $discipline_area = DisciplineArea::whereId($id)->firstOrFail();
            $discipline_area->delete();

            DB::commit();

            // Success message
            Toastr::success(__('GA::discipline-areas.destroy_success_message'), __('toastr.success'));
            return redirect()->route('discipline-areas.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::discipline-areas.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
}
