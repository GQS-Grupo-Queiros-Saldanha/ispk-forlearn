<?php

namespace App\Modules\Payments\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Payments\Models\CurrentAccountObservations;
use App\Modules\Users\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class CurrentAccountObservationsController extends Controller
{
    public function index()
    {
    }

    public function show($id)
    {
        $data = [
            'user' => User::whereId($id)->firstOrFail(),
            'observations' => CurrentAccountObservations::observations($id)->orderBy('created_at')->get()
        ];
        return view("Payments::transactions.partials.observations")->with($data);
    }

    public function store(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $file = $request->file('files');
                $latestId = CurrentAccountObservations::latest()->first() ?? 0;

                $latestId = 0 ? $latestId : $latestId->id;

                if ($request->has('store')) {
                    $observation = new CurrentAccountObservations([
                        'user_id' => $request->get('user_id'),
                        'observation' => $request->get("observation"),
                    ]);

                    if ($request->hasFile('files')) {
                        $filename = $latestId + 1 . '_' . $file->getClientOriginalName();
                        $observation->file = "/storage/attachment/". $filename;
                        $file->storeAs('attachment', $filename);
                    }

                    $observation->save();
                } else {
                    $observation = CurrentAccountObservations::whereId($request->get("id"))->firstOrFail();

                    $observation->observation = $request->get("observation");

                    if ($request->hasFile('files')) {
                        $filename = $latestId + 1 . '_' . $file->getClientOriginalName();
                        $observation->file = "/storage/attachment/". $filename;
                        $file->storeAs('attachment', $filename);
                    }

                    $observation->save();
                }
            });

            if ($request->has('store')) {
                Toastr::success("Observação criada com sucesso");
                return redirect()->route('requests.index');

            } else {
                Toastr::success("Observação editada com sucesso");
                return redirect()->route('requests.index');
            }
        } catch (Exception | Throwable $e) {
            Toastr::error($e->getMessage(), __('toastr.error'));

            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        $observation = CurrentAccountObservations::whereId($id)->firstOrFail();

        $observation->delete();

        DB::commit();

        // Success message
        Toastr::success("Observação apagada com sucesso");

        return redirect()->route('requests.index');

    }

    public function downloadFile($id)
    {
        $observation = CurrentAccountObservations::file($id)->first();

        return $observation ? $observation->file : null;
    }

    public function observation($id)
    {
        $observation = CurrentAccountObservations::whereId($id)->firstOrFail();

        return json_encode($observation);
    }

    public function countObservationsBy($userId)
    {
       $observations = DB::table('current_account_observations')
                            ->where('user_id', $userId)
                            ->count();

        return response()->json($observations);

    }
}
