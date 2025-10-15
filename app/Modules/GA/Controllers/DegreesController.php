<?php

namespace App\Modules\GA\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\Degree;
use App\Modules\GA\Models\DegreeTranslation;
use App\Modules\GA\Requests\DegreeRequest;
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

class DegreesController extends Controller
{

    public function index()
    {
        try {
            return view('GA::degrees.index');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {

            $model = Degree::join('users as u1', 'u1.id', '=', 'degrees.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'degrees.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'degrees.deleted_by')
                ->leftJoin('degree_translations as dt', function ($join) {
                    $join->on('dt.degrees_id', '=', 'degrees.id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                })
                ->select(['degrees.*', 'u1.name as created_by', 'u2.name as updated_by', 'u3.name as deleted_by', 'dt.display_name']);

            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('GA::degrees.datatables.actions')->with('item', $item);
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
            return view('GA::degrees.degree')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function store(DegreeRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create
            $degree = Degree::create([
                'code' => $request->get('code')
            ]);
            $degree->save();

            // Create translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {

                $degree_translations[] = [
                    'degrees_id' => $degree->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id],
                    'description' => $request->get('description')[$language->id],
                    'created_at' => Carbon::now(),
                    'version' => 1,
                    'active' => true
                ];
            }

            if (!empty($degree_translations)) {
                DegreeTranslation::insert($degree_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::degrees.store_success_message'), __('toastr.success'));
            return redirect()->route('degrees.index');

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
            $degree = Degree::whereId($id)->with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                }
            ])->firstOrFail();

            $data = [
                'action' => $action,
                'degree' => $degree,
                'translations' => $degree->translations->keyBy('language_id')->toArray(),
                'languages' => Language::whereActive(true)->get()
            ];
            return view('GA::degrees.degree')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::degrees.not_found_message'), __('toastr.error'));
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

    public function update(DegreeRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Fetch the default language
            $default_language = Language::whereDefault(true)->firstOrFail();

            // Find and update
            $degree = Degree::whereId($id)->firstOrFail();
            $degree->code = $request->get('code');
            $degree->save();

            // Disable previous translations
            DegreeTranslation::where('degrees_id', $degree->id)->update(['active' => false]);

            $version = DegreeTranslation::where('degrees_id', $degree->id)->whereLanguageId($default_language->id)->count() + 1;

            // Associated translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {
                $degree_translations[] = [
                    'degrees_id' => $degree->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id] ?? null,
                    'description' => $request->get('description')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => $version,
                    'active' => true,
                ];
            }

            if (!empty($degree_translations)) {
                DegreeTranslation::insert($degree_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::degrees.update_success_message'), __('toastr.success'));
            return redirect()->route('degrees.show', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::degrees.not_found_message'), __('toastr.error'));
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
            $degree = Degree::whereId($id)->firstOrFail();
            $degree->delete();

            DB::commit();

            // Success message
            Toastr::success(__('GA::degrees.destroy_success_message'), __('toastr.success'));
            return redirect()->route('degrees.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::degrees.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
}
