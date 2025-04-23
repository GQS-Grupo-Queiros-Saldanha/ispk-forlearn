<?php

namespace App\Modules\Users\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\Users\Models\Profession;
use App\Modules\Users\Models\ProfessionTranslation;
use App\Modules\Users\Requests\ProfessionRequest;
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

class ProfessionsController extends Controller
{

    public function index()
    {
        try {
            return view('Users::professions.index');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {

            $model = Profession::join('users as u1', 'u1.id', '=', 'professions.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'professions.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'professions.deleted_by')
                ->leftJoin('professions_translations as pt', function ($join) {
                    $join->on('pt.professions_id', '=', 'professions.id');
                    $join->on('pt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('pt.active', '=', DB::raw(true));
                })
                ->select(['professions.*', 'u1.name as created_by', 'u2.name as updated_by', 'u3.name as deleted_by', 'pt.display_name']);
            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('Users::professions.datatables.actions')->with('item', $item);
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
            $data = [
                'action' => 'create',
                'languages' => Language::whereActive(true)->get()
            ];
            return view('Users::professions.profession')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ProfessionRequest $request
     * @return Response
     */
    public function store(ProfessionRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create
            $profession = Profession::create([
                'code' => $request->get('code')
            ]);
            $profession->save();

            // Create translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {

                $profession_translations[] = [
                    'professions_id' => $profession->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id],
                    'description' => $request->get('description')[$language->id],
                    'created_at' => Carbon::now(),
                    'version' => 1,
                    'active' => true
                ];
            }

            if (!empty($profession_translations)) {
                ProfessionTranslation::insert($profession_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('Users::professions.store_success_message'), __('toastr.success'));
            return redirect()->route('professions.index');

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
            $profession = Profession::whereId($id)->with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                }
            ])->firstOrFail();

            $data = [
                'action' => $action,
                'profession' => $profession,
                'translations' => $profession->translations->keyBy('language_id')->toArray(),
                'languages' => Language::whereActive(true)->get()
            ];
            return view('Users::professions.profession')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Users::professions.not_found_message'), __('toastr.error'));
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
     * @param ProfessionRequest $request
     * @param int $id
     * @return Response
     */
    public function update(ProfessionRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Fetch the default language
            $default_language = Language::whereDefault(true)->firstOrFail();

            // Find and update
            $profession = Profession::whereId($id)->firstOrFail();
            $profession->code = $request->get('code');
            $profession->save();

            // Disable previous translations
            ProfessionTranslation::where('professions_id', $profession->id)->update(['active' => false]);

            $version = ProfessionTranslation::where('professions_id', $profession->id)->whereLanguageId($default_language->id)->count() + 1;

            // Associated translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {
                $profession_translations[] = [
                    'professions_id' => $profession->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id] ?? null,
                    'description' => $request->get('description')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => $version,
                    'active' => true,
                ];
            }

            if (!empty($profession_translations)) {
                ProfessionTranslation::insert($profession_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('Users::professions.update_success_message'), __('toastr.success'));
            return redirect()->route('professions.show', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Users::professions.not_found_message'), __('toastr.error'));
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
            $profession = Profession::whereId($id)->firstOrFail();
            $profession->delete();

            DB::commit();

            // Success message
            Toastr::success(__('Users::professions.destroy_success_message'), __('toastr.success'));
            return redirect()->route('professions.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Users::professions.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
}
