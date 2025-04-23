<?php

namespace App\Modules\Payments\Controllers;

use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\Payments\Models\Bank;
use App\Modules\Payments\Requests\BankRequest;
use DataTables;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Request;
use Throwable;
use Toastr;
use Auth;
use App\Model\Institution;
use Log;
use PDF;

class BanksController extends Controller
{

    public function index()
    {
        try {
            return view("Payments::banks.index");
        } catch (Exception | Throwable $e) {
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {
            $model = Bank::join('users as u1', 'u1.id', '=', 'banks.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'banks.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'banks.deleted_by')
                ->select([
                    'banks.*',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                ])
            ->whereNull('banks.type_conta_entidade');
            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('Payments::banks.datatables.actions')->with('item', $item);
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
            logError($e);
            return response()->json($e->getMessage(), 500);
        }
    }
    
     public function generatePDF()
    {
        try {
            // Retrieve bank data with associated user information
            $model = Bank::join('users as u1', 'u1.id', '=', 'banks.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'banks.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'banks.deleted_by')
                ->select([
                    'banks.*',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                ])
                ->whereNull('banks.type_conta_entidade')
                ->orderBy('display_name', 'ASC')
                ->get();

            // Set the document title
            $titulo_documento = "Bancos";
            $institution = Institution::latest()->first();

            // Generate the PDF using the specified view
            $pdf = PDF::loadView(
                'Payments::banks.pdf-relatorio', // Ensure this is the correct path to your view
                compact(
                    'model',
                    'titulo_documento',
                    'institution'
                )
            );

            // PDF configurations
            $pdf->setOption('margin-top', '3.4mm');
            $pdf->setOption('margin-left', '5mm');
            $pdf->setOption('margin-bottom', '5.7mm');
            $pdf->setOption('margin-right', '5mm');
            $pdf->setOption('enable-javascript', true);
            $pdf->setOption('debug-javascript', true);
            $pdf->setOption('javascript-delay', 3000);
            $pdf->setOption('enable-smart-shrinking', true);
            $pdf->setOption('no-stop-slow-scripts', true);
            $pdf->setPaper('a4', 'portrait');

            // PDF file name
            $pdf_name = "Lista de Bancos";

            // Return the generated PDF as a stream
            return $pdf->stream($pdf_name . '.pdf');

        } catch (Exception | Throwable $e) {
            logError($e);
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

            return view('Payments::banks.bank')->with($data);
        } catch (Exception | Throwable $e) {
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param BankRequest $request
     * @return Response
     */
    public function store(BankRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create
            $bank = new Bank([
                'code' => $request->get('code'),
                'display_name' => $request->get('display_name'),
                'account_number' => $request->get('account_number'),
                'iban' => $request->get('iban'),
            ]);

            $bank->save();

            DB::commit();

            // Success message
            Toastr::success(__('Payments::banks.store_success_message'), __('toastr.success'));
            return redirect()->route('banks.index');

        } catch (Exception | Throwable $e) {
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function fetch($id, $action)
    {
        try {
            // Find
            $bank = Bank::whereId($id)->firstOrFail();

            $data = [
                'action' => $action,
                'bank' => $bank,
                'languages' => Language::whereActive(true)->get()
            ];

            return view('Payments::banks.bank')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Payments::banks.not_found_message'), __('toastr.error'));
            logError($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            logError($e);
            return abort(500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        try {
            return $this->fetch($id, 'show');
        } catch (Exception | Throwable $e) {
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        try {
            return $this->fetch($id, 'edit');
        } catch (Exception | Throwable $e) {
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param BankRequest $request
     * @param int $id
     * @return Response
     */
    public function update(BankRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Find
            $bank = Bank::whereId($id)->firstOrFail();

            // Update
            $bank->code = $request->get('code');
            $bank->display_name = $request->get('display_name');
            $bank->account_number = $request->get('account_number');
            $bank->iban = $request->get('iban');

            $bank->save();

            DB::commit();

            // Success message
            Toastr::success(__('Payments::banks.update_success_message'), __('toastr.success'));
            return redirect()->route('banks.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Payments::banks.not_found_message'), __('toastr.error'));
            logError($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        try {
            $bank = Bank::whereId($id)->firstOrFail();

            DB::beginTransaction();

            $bank->delete();

            // null out values so they can be used again and
            $bank->code = null;
            $bank->account_number = null;
            $bank->iban = null;

            $bank->deleted_by = Auth::user()->id;

            // update DB row to force update to delete_by
            $bank->save();

            DB::commit();

            // Success message
            Toastr::success(__('Payments::banks.destroy_success_message'), __('toastr.success'));
            return redirect()->route('banks.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Payments::banks.not_found_message'), __('toastr.error'));
            logError($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
}
