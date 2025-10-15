<?php

namespace App\Modules\GA\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\Building;
use App\Modules\GA\Models\Room;
use App\Modules\GA\Models\RoomTranslation;
use App\Modules\GA\Requests\RoomRequest;
use Carbon\Carbon;
use DataTables;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Log;
use Throwable;
use Toastr;
use Auth;

class RoomsController extends Controller
{
    public function index()
    {
        try {
            return view('GA::rooms.index');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {
            $model = Room::withTrashed()
                ->join('users as u1', 'u1.id', '=', 'rooms.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'rooms.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'rooms.deleted_by')
                ->leftJoin('building_translations as bt', function ($join) {
                    $join->on('bt.building_id', '=', 'rooms.building_id');
                    $join->on('bt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('bt.active', '=', DB::raw(true));
                })
                ->leftJoin('room_translations as rt', function ($join) {
                    $join->on('rt.room_id', '=', 'rooms.id');
                    $join->on('rt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('rt.active', '=', DB::raw(true));
                })
                ->select([
                    'rooms.*',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    'rt.display_name',
                    'bt.display_name as building'
                ])
                ->whereNull('rooms.deleted_by');

            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('GA::rooms.datatables.actions')->with('item', $item);
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
            $buildings = Building::with([
                'currentTranslation'
            ])->get();

            $data = [
                'action' => 'create',
                'buildings' => $buildings,
                'languages' => Language::whereActive(true)->get()
            ];
            return view('GA::rooms.room')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function store(RoomRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create
            $room = Room::create([
                'code' => $request->get('code'),
                'building_id' => $request->get('building')
            ]);
            $room->save();

            // Translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {
                $room_translations[] = [
                    'room_id' => $room->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id],
                    'description' => $request->get('description')[$language->id],
                    'created_at' => Carbon::now(),
                    'version' => 1,
                    'active' => true
                ];
            }
            if (!empty($room_translations)) {
                RoomTranslation::insert($room_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::rooms.store_success_message'), __('toastr.success'));
            return redirect()->route('rooms.index');

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
            $room = Room::whereId($id)->with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                }
            ])->firstOrFail();

            $buildings = Building::with([
                'currentTranslation'
            ])->get();

            $data = [
                'action' => $action,
                'buildings' => $buildings,
                'room' => $room,
                'translations' => $room->translations->keyBy('language_id')->toArray(),
                'languages' => Language::whereActive(true)->get()
            ];
            return view('GA::rooms.room')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::rooms.not_found_message'), __('toastr.error'));
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

    public function update(RoomRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Fetch the default language
            $default_language = Language::whereDefault(true)->firstOrFail();

            // Find and update
            $room = Room::whereId($id)->firstOrFail();
            $room->code = $request->get('code');
            $room->building_id = $request->get('building');
            $room->save();

            // Disable previous translations
            RoomTranslation::where('room_id', $room->id)->update(['active' => false]);
            $version = RoomTranslation::where('room_id', $room->id)->whereLanguageId($default_language->id)->count() + 1;

            // Associated translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {
                $room_translations[] = [
                    'room_id' => $room->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id] ?? null,
                    'description' => $request->get('description')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => $version,
                    'active' => true,
                ];
            }
            if (!empty($room_translations)) {
                RoomTranslation::insert($room_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::rooms.update_success_message'), __('toastr.success'));
            return redirect()->route('rooms.show', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::rooms.not_found_message'), __('toastr.error'));
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

            $room = Room::whereId($id)->with(['translations'])->firstOrFail();
            $room->translations()->delete();
            $room->delete();

            $room->deleted_by = Auth::user()->id;
            $room->save();

            DB::commit();

            // Success message
            Toastr::success(__('GA::rooms.destroy_success_message'), __('toastr.success'));
            return redirect()->route('rooms.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::rooms.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
}
