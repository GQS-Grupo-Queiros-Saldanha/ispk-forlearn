<?php

namespace App\Modules\GA\Controllers;

use App\Modules\Users\Models\User;
use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\Event;
use App\Modules\GA\Models\EventOption;
use App\Modules\GA\Models\EventTranslation;
use App\Modules\GA\Models\EventType;
use App\Modules\GA\Requests\EventRequest;
use Carbon\Carbon;
use DataTables;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Log;
use Throwable;
use Toastr;

class BudgetTypeController extends Controller
{
    /** 
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        try {
            return view('GA::budget-type.index');
        } catch (Exception | Throwable $e) {
            Log::error($e);

            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {

            $budget_type = DB::table('bd_budget_type as orcamento_tipo')
                ->join('users as u0', 'u0.id', "=", 'orcamento_tipo.created_by')
                ->leftjoin('users as u1', 'u1.id', "=", 'orcamento_tipo.updated_by')
                ->whereNull('orcamento_tipo.deleted_at')
                ->whereNull('orcamento_tipo.deleted_by')
                ->select(['orcamento_tipo.id', 'orcamento_tipo.name', 'orcamento_tipo.description', 'u0.name as created_by', 'u1.name as updated_by', 'orcamento_tipo.created_at', 'orcamento_tipo.updated_at']);

            return Datatables::queryBuilder($budget_type)
                ->addColumn('actions', function ($item) {
                    return view('GA::budget-type.datatables.actions')->with('item', $item);
                })
                ->rawColumns(['actions'])
                ->addIndexColumn()
                ->toJson();
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {

            // Listar todos os tipos de eventos


            $data = [
                'action' => 'create'

            ];
            return view('GA::budget-type.budget_type')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function my_events()
    {
        try {

            // Listar todos os tipos de eventos



            $data = [
                'action' => 'create'

            ];
            return view('GA::events.myevent')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {



            

            // Cadastrar um determinado orçamento

            $id_budget = DB::table('bd_budget_type')->insertGetId(
                [
                    'name' => $request->get('name'),
                    'description' => $request->get('description'),
                    'created_by' => auth()->user()->id
                ]
            );



            // Success message
            Toastr::success(__('Tipo de orçamento criado com sucesso'), __('toastr.success'));
            return redirect()->route('budget_type.index');
        } catch (Exception | Throwable $e) {
            Toastr::error($e->getMessage(), __('toastr.error'));
            Log::error($e);
            return $e;
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    private function fetch($id, $action)
    {
        try {


            $budget_type = DB::table('bd_budget_type')
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->whereNull('deleted_by')
                ->select(['id', 'name', 'description'])
                ->first();

            $data = [
                'budget_type' => $budget_type,
                'action' => $action
            ];

            return view('GA::budget-type.budget_type')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::events.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return abort(500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            return $this->fetch($id, 'show');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            return $this->fetch($id, 'edit');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {


            $update = DB::table('bd_budget_type')
                ->where('id', "=", $id)
                ->update(
                    [
                        'name' => $request->get('name'),
                        'description' => $request->get('description'),
                        "updated_by" => auth()->user()->id,
                        "updated_at" => Carbon::now()
                    ]
                );



            // Success message
            Toastr::success(__('Orçamento actualizado com sucesso'), __('toastr.success'));
            return redirect()->route('budget_type.show', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::events.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return $e;
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try {



            $update = DB::table('bd_budget_type')
                ->where('id', "=", $request->get("id"))
                ->update(
                    [
                        "deleted_by" => auth()->user()->id,
                        "deleted_at" => Carbon::now()
                    ]
                );

            // Success message
            Toastr::success(__('Eliminado com sucesso'), __('toastr.success')); 
            return redirect()->route('budget_type.index');
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::budget-type.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
}
