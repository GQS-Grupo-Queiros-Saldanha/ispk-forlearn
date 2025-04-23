<?php

namespace App\Modules\Users\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\Users\Models\ProfessionalState;
use App\Modules\Users\Models\ProfessionalStateTranslation;
use App\Modules\Users\Requests\ProfessionalStateRequest;
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

class ProfessionalStatesController extends Controller
{

    public function index()
    {
        try {
            return view('Users::professional-states.index');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {

            $model = ProfessionalState::join('users as u1', 'u1.id', '=', 'professional_states.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'professional_states.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'professional_states.deleted_by')
                ->leftJoin('professional_states_translations as pst', function ($join) {
                    $join->on('pst.professional_states_id', '=', 'professional_states.id');
                    $join->on('pst.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('pst.active', '=', DB::raw(true));
                })
                ->select([
                    'professional_states.*',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    'pst.display_name'
                ]);

            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('Users::professional-states.datatables.actions')->with('item', $item);
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
            return view('Users::professional-states.professional-state')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ProfessionalStateRequest $request
     * @return Response
     */
    public function store(ProfessionalStateRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create
            $professional_state = ProfessionalState::create([
                'code' => $request->get('code')
            ]);
            $professional_state->save();

            // Create translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {

                $professional_state_translations[] = [
                    'professional_states_id' => $professional_state->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id],
                    'description' => $request->get('description')[$language->id],
                    'created_at' => Carbon::now(),
                    'version' => 1,
                    'active' => true
                ];
            }

            if (!empty($professional_state_translations)) {
                ProfessionalStateTranslation::insert($professional_state_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('Users::professional-states.store_success_message'), __('toastr.success'));
            return redirect()->route('professional-states.index');

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
            $professional_state = ProfessionalState::whereId($id)->with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                }
            ])->firstOrFail();

            $data = [
                'action' => $action,
                'professional_state' => $professional_state,
                'translations' => $professional_state->translations->keyBy('language_id')->toArray(),
                'languages' => Language::whereActive(true)->get()
            ];
            return view('Users::professional-states.professional-state')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Users::professional-states.not_found_message'), __('toastr.error'));
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
     * @param ProfessionalStateRequest $request
     * @param  int $id
     * @return Response
     */
    public function update(ProfessionalStateRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Fetch the default language
            $default_language = Language::whereDefault(true)->firstOrFail();

            // Find and update
            $professional_state = ProfessionalState::whereId($id)->firstOrFail();
            $professional_state->code = $request->get('code');
            $professional_state->save();

            // Disable previous translations
            ProfessionalStateTranslation::where('professional_states_id', $professional_state->id)->update(['active' => false]);

            $version = ProfessionalStateTranslation::where('professional_states_id', $professional_state->id)->whereLanguageId($default_language->id)->count() + 1;

            // Associated translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {
                $professional_state_translations[] = [
                    'professional_states_id' => $professional_state->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id] ?? null,
                    'description' => $request->get('description')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => $version,
                    'active' => true,
                ];
            }

            if (!empty($professional_state_translations)) {
                ProfessionalStateTranslation::insert($professional_state_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('Users::professional-states.update_success_message'), __('toastr.success'));
            return redirect()->route('professional-states.show', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Users::professional-states.not_found_message'), __('toastr.error'));
            Log::error($e);
            return abort(500);
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
            $professional_state = ProfessionalState::whereId($id)->firstOrFail();
            $professional_state->delete();

            DB::commit();

            // Success message
            Toastr::success(__('Users::professional-states.destroy_success_message'), __('toastr.success'));
            return redirect()->route('professional-states.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Users::professional-states.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
}
