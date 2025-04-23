<?php

namespace App\Modules\GA\Controllers;

use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\Precedence;
use App\Modules\GA\Requests\PrecedenceRequest;
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

class PrecedencesController extends Controller
{

    public function index()
    {
        try {
            return view('GA::precedences.index');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {

            $model = Precedence::join('users as u1', 'u1.id', '=', 'precedences.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'precedences.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'precedences.deleted_by')
                ->select([
                    'precedences.*',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                ]);
            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('GA::precedences.datatables.actions')->with('item', $item);
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
                'languages' => Language::whereActive(true)->get(),
            ];
            return view('GA::precedences.precedence')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PrecedenceRequest $request
     * @return Response
     */
    public function store(PrecedenceRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create
            $precedence = new Precedence([
                'code' => $request->get('code'),
            ]);

            $precedence->studyPlanEdition()->associate($request->get('study_plan_edition'));

            if ($request->has('discipline')) {
                $precedence->discipline()->associate($request->get('discipline'));
            }

            if ($request->has('precedence')) {
                $precedence->parent()->associate($request->get('precedence'));
            }

            $precedence->save();

            DB::commit();

            // Success message
            Toastr::success(__('GA::precedences.store_success_message'), __('toastr.success'));
            return redirect()->route('precedences.index');

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
            $precedence = Precedence::whereId($id)->with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                },
            ])->firstOrFail();

            $data = [
                'action' => $action,
                'precedence' => $precedence,
                'translations' => $precedence->translations->keyBy('language_id')->toArray(),
                'languages' => Language::whereActive(true)->get()
            ];
            return view('GA::precedences.precedence')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::precedences.not_found_message'), __('toastr.error'));
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
     * @param PrecedenceRequest $request
     * @param  int $id
     * @return Response
     */
    public function update(PrecedenceRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Find and update
            $precedence = Precedence::whereId($id)->firstOrFail();
            $precedence->code = $request->get('code');

            $precedence->studyPlanEdition()->dissociate();
            $precedence->studyPlanEdition()->associate($request->get('study_plan_edition'));

            $precedence->discipline()->dissociate();
            if ($request->has('discipline')) {
                $precedence->discipline()->associate($request->get('discipline'));
            }

            $precedence->parent()->dissociate();
            if ($request->has('precedence')) {
                $precedence->parent()->associate($request->get('precedence'));
            }

            $precedence->save();

            DB::commit();

            // Success message
            Toastr::success(__('GA::precedences.update_success_message'), __('toastr.success'));
            return redirect()->route('precedences.show', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::precedences.not_found_message'), __('toastr.error'));
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
            $precedence = Precedence::whereId($id)->firstOrFail();
            $precedence->delete();

            DB::commit();

            // Success message
            Toastr::success(__('GA::precedences.destroy_success_message'), __('toastr.success'));
            return redirect()->route('precedences.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::precedences.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
}
