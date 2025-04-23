<?php

namespace App\Modules\GA\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\Building;
use App\Modules\GA\Models\BuildingTranslation;
use App\Modules\GA\Requests\BuildingRequest;
use Carbon\Carbon;
use DataTables;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Log;
use Request;
use Throwable;
use Toastr;

class BuildingsController extends Controller
{
    public function index()
    {
        try {
            return view('GA::buildings.index');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {

            $model = Building::join('users as u1', 'u1.id', '=', 'buildings.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'buildings.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'buildings.deleted_by')
                ->leftJoin('building_translations as bt', function ($join) {
                    $join->on('bt.building_id', '=', 'buildings.id');
                    $join->on('bt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('bt.active', '=', DB::raw(true));
                })
                ->select([
                    'buildings.*',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    'bt.display_name'
                ]);

            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('GA::buildings.datatables.actions')->with('item', $item);
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
            return response()->json($e->getMessage());
        }
    }

    public function create()
    {
        try {
            $data = [
                'action' => 'create',
                'languages' => Language::whereActive(true)->get()
            ];
            return view('GA::buildings.building')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function store(BuildingRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create
            $building = Building::create([
                'code' => $request->get('code')
            ]);
            $building->save();

            // Translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {
                $building_translations[] = [
                    'building_id' => $building->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id],
                    'description' => $request->get('description')[$language->id],
                    'created_at' => Carbon::now(),
                    'version' => 1,
                    'active' => true
                ];
            }
            if (!empty($building_translations)) {
                BuildingTranslation::insert($building_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::buildings.store_success_message'), __('toastr.success'));
            return redirect()->route('buildings.index');

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
            $building = Building::whereId($id)->with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                }
            ])->firstOrFail();

            $data = [
                'action' => $action,
                'building' => $building,
                'translations' => $building->translations->keyBy('language_id')->toArray(),
                'languages' => Language::whereActive(true)->get()
            ];
            return view('GA::buildings.building')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::buildings.not_found_message'), __('toastr.error'));
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

    public function update(BuildingRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Fetch the default language
            $default_language = Language::whereDefault(true)->firstOrFail();

            // Find and update
            $building = Building::whereId($id)->firstOrFail();
            $building->code = $request->get('code');
            $building->save();

            // Disable previous translations
            BuildingTranslation::where('building_id', $building->id)->update(['active' => false]);
            $version = BuildingTranslation::where('building_id', $building->id)->whereLanguageId($default_language->id)->count() + 1;

            // Associated translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {
                $building_translations[] = [
                    'building_id' => $building->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id] ?? null,
                    'description' => $request->get('description')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => $version,
                    'active' => true,
                ];
            }
            if (!empty($building_translations)) {
                BuildingTranslation::insert($building_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::buildings.update_success_message'), __('toastr.success'));
            return redirect()->route('buildings.show', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::buildings.not_found_message'), __('toastr.error'));
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

            $building = Building::whereId($id)->with(['translations'])->firstOrFail();
            $building->translations()->delete();
            $building->delete();

            DB::commit();

            // Success message
            Toastr::success(__('GA::buildings.destroy_success_message'), __('toastr.success'));
            return redirect()->route('buildings.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::buildings.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function rooms($id)
    {
        $building = Building::with([
            'rooms' => function ($q) {
                $q->with([
                    'currentTranslation'
                ]);
            }
        ])->findOrFail($id);

        return $building->rooms;
    }
}
