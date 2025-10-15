<?php

namespace App\Modules\GA\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\YearTransitionRule;
use App\Modules\GA\Models\YearTransitionRuleTranslation;
use App\Modules\GA\Requests\YearTransitionRuleRequest;
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

class YearTransitionRulesController extends Controller
{

    public function index()
    {
        try {
            return view('GA::year-transition-rules.index');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {

            $model = YearTransitionRule::join('users as u1', 'u1.id', '=', 'year_transition_rules.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'year_transition_rules.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'year_transition_rules.deleted_by')
                ->leftJoin('year_transition_rule_translations as ytrt', function ($join) {
                    $join->on('ytrt.ytr_id', '=', 'year_transition_rules.id');
                    $join->on('ytrt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ytrt.active', '=', DB::raw(true));
                })
                ->select([
                    'year_transition_rules.*',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    'ytrt.display_name',
                ]);

            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('GA::year-transition-rules.datatables.actions')->with('item', $item);
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
                'languages' => Language::whereActive(true)->get(),
            ];
            return view('GA::year-transition-rules.year-transition-rule')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param YearTransitionRuleRequest $request
     * @return Response
     */
    public function store(YearTransitionRuleRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create
            $year_transition_rule = YearTransitionRule::create([
                'code' => $request->get('code'),
            ]);
            $year_transition_rule->save();

            // Create translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {

                $year_transition_rule_translations[] = [
                    'ytr_id' => $year_transition_rule->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id],
                    'description' => $request->get('description')[$language->id],
                    'abbreviation' => $request->get('abbreviation')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => 1,
                    'active' => true
                ];
            }

            if (!empty($year_transition_rule_translations)) {
                YearTransitionRuleTranslation::insert($year_transition_rule_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::year-transition-rules.store_success_message'), __('toastr.success'));
            return redirect()->route('year-transition-rules.index');

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
            $year_transition_rule = YearTransitionRule::whereId($id)->with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                },
            ])->firstOrFail();

            $data = [
                'action' => $action,
                'year_transition_rule' => $year_transition_rule,
                'translations' => $year_transition_rule->translations->keyBy('language_id')->toArray(),
                'languages' => Language::whereActive(true)->get()
            ];
            return view('GA::year-transition-rules.year-transition-rule')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::year-transition-rules.not_found_message'), __('toastr.error'));
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
     * @param YearTransitionRuleRequest $request
     * @param  int $id
     * @return Response
     */
    public function update(YearTransitionRuleRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Fetch the default language
            $default_language = Language::whereDefault(true)->firstOrFail();

            // Find and update
            $year_transition_rule = YearTransitionRule::whereId($id)->firstOrFail();
            $year_transition_rule->code = $request->get('code');
            $year_transition_rule->save();

            // Disable previous translations
            YearTransitionRuleTranslation::where('ytr_id', $year_transition_rule->id)->update(['active' => false]);

            $version = YearTransitionRuleTranslation::where('ytr_id', $year_transition_rule->id)->whereLanguageId($default_language->id)->count() + 1;

            // Associated translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {
                $year_transition_rule_translations[] = [
                    'ytr_id' => $year_transition_rule->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id] ?? null,
                    'description' => $request->get('description')[$language->id] ?? null,
                    'abbreviation' => $request->get('abbreviation')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => $version,
                    'active' => true,
                ];
            }

            if (!empty($year_transition_rule_translations)) {
                YearTransitionRuleTranslation::insert($year_transition_rule_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::year-transition-rules.update_success_message'), __('toastr.success'));
            return redirect()->route('year-transition-rules.show', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::year-transition-rules.not_found_message'), __('toastr.error'));
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
            $year_transition_rule = YearTransitionRule::whereId($id)->firstOrFail();
            $year_transition_rule->delete();

            DB::commit();

            // Success message
            Toastr::success(__('GA::year-transition-rules.destroy_success_message'), __('toastr.success'));
            return redirect()->route('year-transition-rules.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::year-transition-rules.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
}
