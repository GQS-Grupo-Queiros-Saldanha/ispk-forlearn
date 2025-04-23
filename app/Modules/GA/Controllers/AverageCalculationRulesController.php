<?php

namespace App\Modules\GA\Controllers;

use App\Helpers\LanguageHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\AverageCalculationRule;
use App\Modules\GA\Models\AverageCalculationRuleTranslation;
use App\Modules\GA\Requests\AverageCalculationRuleRequest;
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

class AverageCalculationRulesController extends Controller
{

    public function index()
    {
        try {
            return view('GA::average-calculation-rules.index');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {

            $model = AverageCalculationRule::join('users as u1', 'u1.id', '=', 'average_calculation_rules.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'average_calculation_rules.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'average_calculation_rules.deleted_by')
                ->leftJoin('average_calculation_rule_translations as acrt', function ($join) {
                    $join->on('acrt.acr_id', '=', 'average_calculation_rules.id');
                    $join->on('acrt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('acrt.active', '=', DB::raw(true));
                })
                ->select([
                    'average_calculation_rules.*',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    'acrt.display_name',
                ]);
            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('GA::average-calculation-rules.datatables.actions')->with('item', $item);
                })
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
            return view('GA::average-calculation-rules.average-calculation-rule')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AverageCalculationRuleRequest $request
     * @return Response
     */
    public function store(AverageCalculationRuleRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create
            $average_calculation_rule = AverageCalculationRule::create([
                'code' => $request->get('code'),
            ]);
            $average_calculation_rule->save();

            // Create translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {

                $average_calculation_rule_translations[] = [
                    'acr_id' => $average_calculation_rule->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id],
                    'description' => $request->get('description')[$language->id],
                    'abbreviation' => $request->get('abbreviation')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => 1,
                    'active' => true
                ];
            }

            if (!empty($average_calculation_rule_translations)) {
                AverageCalculationRuleTranslation::insert($average_calculation_rule_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::average-calculation-rules.store_success_message'), __('toastr.success'));
            return redirect()->route('average-calculation-rules.index');

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
            $average_calculation_rule = AverageCalculationRule::whereId($id)->with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                },
            ])->firstOrFail();

            $data = [
                'action' => $action,
                'average_calculation_rule' => $average_calculation_rule,
                'translations' => $average_calculation_rule->translations->keyBy('language_id')->toArray(),
                'languages' => Language::whereActive(true)->get()
            ];
            return view('GA::average-calculation-rules.average-calculation-rule')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::average-calculation-rules.not_found_message'), __('toastr.error'));
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
     * @param AverageCalculationRuleRequest $request
     * @param  int $id
     * @return Response
     */
    public function update(AverageCalculationRuleRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Fetch the default language
            $default_language = Language::whereDefault(true)->firstOrFail();

            // Find and update
            $average_calculation_rule = AverageCalculationRule::whereId($id)->firstOrFail();
            $average_calculation_rule->code = $request->get('code');
            $average_calculation_rule->save();

            // Disable previous translations
            AverageCalculationRuleTranslation::where('acr_id', $average_calculation_rule->id)->update(['active' => false]);

            $version = AverageCalculationRuleTranslation::where('acr_id', $average_calculation_rule->id)->whereLanguageId($default_language->id)->count() + 1;

            // Associated translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {
                $average_calculation_rule_translations[] = [
                    'acr_id' => $average_calculation_rule->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id] ?? null,
                    'description' => $request->get('description')[$language->id] ?? null,
                    'abbreviation' => $request->get('abbreviation')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => $version,
                    'active' => true,
                ];
            }

            if (!empty($average_calculation_rule_translations)) {
                AverageCalculationRuleTranslation::insert($average_calculation_rule_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::average-calculation-rules.update_success_message'), __('toastr.success'));
            return redirect()->route('average-calculation-rules.show', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::average-calculation-rules.not_found_message'), __('toastr.error'));
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
            $average_calculation_rule = AverageCalculationRule::whereId($id)->firstOrFail();
            $average_calculation_rule->delete();

            DB::commit();

            // Success message
            Toastr::success(__('GA::average-calculation-rules.destroy_success_message'), __('toastr.success'));
            return redirect()->route('average-calculation-rules.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::average-calculation-rules.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
}
