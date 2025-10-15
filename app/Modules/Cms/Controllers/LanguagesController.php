<?php

namespace App\Modules\Cms\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\Cms\Requests\LanguageRequest;
use DataTables;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Log;
use Throwable;
use Toastr;

class LanguagesController extends Controller
{

    public function index()
    {
        try {
            return view('Cms::languages.index');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {

            $model = Language::join('users as u1', 'u1.id', '=', 'languages.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'languages.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'languages.deleted_by')
                ->select(['languages.*', 'u1.name as created_by', 'u2.name as updated_by', 'u3.name as deleted_by']);

            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('Cms::languages.datatables.actions')->with('item', $item);
                })
                ->editColumn('active', function ($item) {
                    return $item->active ? __('common.yes') : __('common.no');
                })
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
            ];
            return view('Cms::languages.language')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param LanguageRequest $request
     * @return Response
     */
    public function store(LanguageRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create
            $language = Language::create([
                'code' => $request->get('code'),
                'name' => $request->get('name'),
                'active' => $request->get('active'),
                'default' => false,
            ]);
            $language->save();

            DB::commit();

            // Success message
            Toastr::success(__('Cms::languages.store_success_message'), __('toastr.success'));
            return redirect()->route('languages.index');

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
            $language = Language::whereId($id)->firstOrFail();

            $data = [
                'action' => $action,
                'language' => $language,
            ];
            return view('Cms::languages.language')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Cms::languages.not_found_message'), __('toastr.error'));
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
     * @return Response
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
     * @return Response
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
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            // Find and update
            $language = Language::whereId($id)->firstOrFail();
            $language->code = $request->get('code');
            $language->name = $request->get('name');
            $language->active = $request->get('active');
            $language->save();

            DB::commit();

            // Success message
            Toastr::success(__('Cms::languages.update_success_message'), __('toastr.success'));
            return redirect()->route('languages.show', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Cms::languages.not_found_message'), __('toastr.error'));
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

            // Find and delete
            $language = Language::whereId($id)->firstOrFail();
            $language->delete();

            DB::commit();

            // Success message
            Toastr::success(__('Cms::languages.destroy_success_message'), __('toastr.success'));
            return redirect()->route('languages.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Cms::languages.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function default($id)
    {
        try {
            DB::beginTransaction();

            // Find and update
            $language = Language::whereId($id)->firstOrFail();
            Language::whereDefault(true)->update(['default' => false]);
            $language->default = true;
            $language->save();

            DB::commit();

            // Success message
            Toastr::success(__('Cms::languages.update_success_message'), __('toastr.success'));
            return redirect()->route('languages.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Cms::languages.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
}
