<?php

namespace App\Modules\Payments\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\Payments\Models\Article;
use App\Modules\Payments\Models\Payment;
use App\Modules\Payments\Requests\PaymentRequest;
use App\Modules\Users\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class PaymentsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
//        try {
//            $users = auth()->user()->can('manage-payments-others') ? studentsSelectList([6]) : null;
//
//            $data = compact('users');
//
//            return view("Payments::payments.index")->with($data);
//        } catch (Exception | Throwable $e) {
//            Log::error($e);
//            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
//        }
    }

    public function ajax($userId)
    {
//        $userId = auth()->user()->can('manage-payments-others') ? $userId : auth()->user()->id;
//
//        try {
//            $model = Payment::whereUserId($userId)
//                ->join('users as u0', 'u0.id', '=', 'payments.user_id')
//                ->join('users as u1', 'u1.id', '=', 'payments.created_by')
//                ->leftJoin('users as u2', 'u2.id', '=', 'payments.updated_by')
//                ->leftJoin('users as u3', 'u3.id', '=', 'payments.deleted_by')
//                ->join('articles as a', 'a.id', '=', 'payments.article_id')
//                ->leftJoin('article_translations as at', function ($join) {
//                    $join->on('at.article_id', '=', 'a.id');
//                    $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
//                    $join->on('at.active', '=', DB::raw(true));
//                })
//                ->select([
//                    'payments.*',
//                    'u0.name as user',
//                    'u1.name as created_by',
//                    'u2.name as updated_by',
//                    'u3.name as deleted_by',
//                    'at.display_name as article',
//                ]);
//
//            return Datatables::eloquent($model)
//                ->addColumn('status', function ($item) {
//                    return $this->paymentStatus($item);
//                })
//                ->addColumn('actions', function ($item) {
//                    return view('Payments::payments.datatables.actions')->with('item', $item);
//                })
//                ->editColumn('article', function ($item) {
//                    $columnValue = $item->article;
//                    if ($item->month) {
//                        $month = getLocalizedMonths()[$item->month - 1]["display_name"];
//                        $columnValue .= " ($month)";
//                    }
//                    return $columnValue;
//                })
//                ->editColumn('fulfilled_at', function ($item) {
//                    return $item->fulfilled_at ? TimeHelper::time_elapsed_string($item->fulfilled_at) : null;
//                })
//                ->editColumn('created_at', function ($item) {
//                    return TimeHelper::time_elapsed_string($item->created_at);
//                })
//                ->editColumn('updated_at', function ($item) {
//                    return $item->updated_at ? TimeHelper::time_elapsed_string($item->updated_at) : null;
//                })
//                ->editColumn('deleted_at', function ($item) {
//                    return $item->deleted_at ? TimeHelper::time_elapsed_string($item->deleted_at) : null;
//                })
//                ->rawColumns(['actions'])
//                ->toJson();
//
//        } catch (Exception | Throwable $e) {
//            Log::error($e);
//            return response()->json($e->getMessage(), 500);
//        }
    }

    public function ajaxArticliesPerUser($id)
    {
//        try {
//            $user = User::findOrFail($id)->load('courses');
//            $userCourses = $user->courses->pluck('id');
//
//            $articles = Article::with([
//                'currentTranslation',
//                'extra_fees',
//                'monthly_charges'
//            ])
//                ->doesntHave('monthly_charges')
//                ->orWhereHas('monthly_charges', function ($q) use ($userCourses) {
//                    $q->whereIn('course_id', $userCourses);
//                })
//                ->get();
//
//            $articles->each(function ($item) {
//                $item->{'extraFeesAsText'} = $item->extraFeesAsText();
//            });
//
//            return $articles->sortBy('currentTranslation.display_name')->values();
//        } catch (Exception | Throwable $e) {
//            Log::error($e);
//            return response()->json($e->getMessage(), 500);
//        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
//        try {
//            $articles = Article::with([
//                'currentTranslation', 'extra_fees', 'monthly_charges'
//            ])->get();
//
//            $articles->each(function ($item) {
//                $item->{'extraFeesAsText'} = $item->extraFeesAsText();
//            });
//
//            $data = [
//                'action' => 'create',
//                'users' => auth()->user()->can('manage-payments-others') ? studentsSelectList([6]) : null,
//                'articles' => $articles,
//                'years' => getYearList(),
//                'months' => getLocalizedMonths(),
//                'languages' => Language::whereActive(true)->get()
//            ];
//
//            return view('Payments::payments.request')->with($data);
//        } catch (Exception | Throwable $e) {
//            Log::error($e);
//            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
//        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PaymentRequest $request
     * @return void
     */
    public function store(PaymentRequest $request)
    {
//        try {
//            DB::beginTransaction();
//
//            $article = Article::findOrFail($request->get('article'));
//
//            $userId = $request->user()->can('create-requests-others') && $request->get('user') ?
//                $request->get('user') : $request->user()->id;
//
//            // Create
//            $payment = new Payment([
//                'article_id' => $article->id,
//                'user_id' => $userId,
//                'year' => $request->get('year') ?: null,
//                'month' => $request->get('month') ?: null
//            ]);
//
//            $payment->save();
//
//            /*
//            $proxyPay = new proxyPay();
//            $paymentReference = $proxyPay->createPayment([
//                'amount' => (string)$article->base_value,
//                'end_datetime' => Carbon::now()->addDays(10)->toDateString(),
//                'custom_fields' => [
//                    'userId' => (string)$request->user()->id,
//                    'paymentId' => (string)$payment->id
//                ]
//            ]);
//            */
//
//            $payment->transaction_uid = '000000000';
//            $payment->save();
//
//            DB::commit();
//
////            Mail::to($request->user()->email)->queue(
////                new NewPayment($payment)
////            );
//
//            // Success message
//            Toastr::success(__('Payments::payments.store_success_message'), __('toastr.success'));
//            return redirect()->route('account.index');
//
//        } catch (Exception | Throwable $e) {
//            Log::error($e);
//            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
//        }
    }

    public function paymentManualUpdate(\Illuminate\Http\Request $request, $id)
    {
//        try {
//            /** @var Payment $payment */
//            $payment = Payment::findOrFail($id);
//            $checkPermission = auth()->user()->can('manage-manual-payments');
//            $newValue = $payment->total_paid + (double)$request->get('manual_value');
//            $checkOverPaid = $newValue <= $payment->total_value;
//
//            if ($checkPermission) {
//                if ($payment->free_text !== $request->get('free_text')) {
//                    $payment->free_text = $request->get('free_text');
//                }
//
//                if ($checkOverPaid) {
//                    $payment->total_paid = $newValue;
//
//                    if ($payment->total_paid >= $payment->total_value) {
//                        $payment->fulfilled_at = Carbon::now();
//                    }
//                } else {
//                    Toastr::error(__('Payments::payments.update_error_message'), __('toastr.error'));
//                    return redirect()->back();
//                }
//
//                $payment->save();
//
//                Toastr::success(__('Payments::payments.update_success_message'), __('toastr.success'));
//            } else {
//                Toastr::error(__('Payments::payments.update_error_message'), __('toastr.error'));
//            }
//
//            return redirect()->back();
//        } catch (Exception | Throwable $e) {
//            Log::error($e);
//            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
//        }
    }

    protected function paymentStatus(Payment $payment)
    {
//        $status = 'pending';
//        $type = 'info';
//
//        if ($payment->deleted_at !== null) {
//            $status = 'canceled';
//            $type = 'danger';
//        } elseif ($payment->fulfilled_at !== null) {
//            $status = 'total';
//            $type = 'success';
//        } elseif ($payment->total_paid !== 0.0) {
//            $status = 'partial';
//            $type = 'warning';
//        }
//
//        $text = __("Payments::payments.status.$status");
//
//        return "<span class='badge badge-$type text-uppercase'>$text</span>";
    }

    public function fetch($id, $action)
    {
//        try {
//            $payment = Payment::whereId($id)
//                ->with([
//                    'article' => function ($q) {
//                        $q->with([
//                            'currentTranslation'
//                        ]);
//                    },
//                    'user'
//                ])
//                ->firstOrFail();
//
//            $paymentStatus = $this->paymentStatus($payment);
//
//            $data = [
//                'action' => $action,
//                'payment' => $payment,
//                'payment_status' => $paymentStatus,
//                'article_extra_fees' => $payment->article && $payment->article->extraFeesAsText() ?
//                    $payment->article->extraFeesAsText() : null
//            ];
//
//            return view('Payments::payments.payment')->with($data);
//        } catch (ModelNotFoundException $e) {
//            Toastr::error(__('Payments::payments.not_found_message'), __('toastr.error'));
//            Log::error($e);
//            return redirect()->back() ?? abort(500);
//        } catch (Exception | Throwable $e) {
//            Log::error($e);
//            return abort(500);
//        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
//        try {
//            return $this->fetch($id, 'show');
//        } catch (Exception | Throwable $e) {
//            Log::error($e);
//            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
//        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
//        try {
//            $checkPermission = auth()->user()->can('manage-manual-payments');
//            $payment = Payment::whereId($id)->firstOrFail();
//
//            if ($payment->total_paid !== 0.0 || !$checkPermission) {
//                Toastr::error(__('Payments::payments.destroy_error_message'), __('toastr.error'));
//                return redirect()->back();
//            } else {
//                DB::beginTransaction();
//
//                // Delete translations
//                $payment->delete();
//
//                // update DB row to force update to delete_by
//                $payment->save();
//
//                DB::commit();
//
//                // Success message
//                Toastr::success(__('Payments::payments.destroy_success_message'), __('toastr.success'));
//                return redirect()->route('account.index');
//            }
//        } catch (ModelNotFoundException $e) {
//            Toastr::error(__('Payments::payments.not_found_message'), __('toastr.error'));
//            Log::error($e);
//            return redirect()->back() ?? abort(500);
//        } catch (Exception | Throwable $e) {
//            Log::error($e);
//            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
//        }
    }
}
