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

class BudgetChaptersController extends Controller
{
    /** 
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        try {
            return view('GA::budget-chapters.index');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return $e;
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function budget_chapter($id)
    {

        try {
           
            $budget = DB::table('bd_budget')
            ->whereNull('deleted_at')
            ->whereNull('deleted_by')
            ->where('id',$id)
            ->select(['id','name']) 
            ->first();

            

            return view('GA::budget-chapters.index',compact('budget'));
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return $e;
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax($id)
    {
        try {

           

            $article_last = DB::table('bd_budget_articles')
            ->whereNull('deleted_at')
            ->whereNull('deleted_by')
            ->orderBy("code","desc") 
            ->select(['chapter_id','code'])
            ->get();

             $budget_articles = DB::table('bd_budget_articles')
                ->whereNull('deleted_at')
                ->whereNull('deleted_by')
                ->select(['chapter_id','code','quantidade','money']) 
                ->get();

            $budget_chapter = DB::table('bd_chapter as capitulo')
                ->join('bd_budget as orcamento', 'orcamento.id', "=", 'capitulo.budget_id')
                ->join('users as u0', 'u0.id', "=", 'capitulo.created_by')
                ->leftjoin('users as u1', 'u1.id', "=", 'capitulo.updated_by')
                ->whereNull('capitulo.deleted_at')
                ->where('orcamento.id',$id) 
                ->whereNull('capitulo.deleted_by')
                ->select(['capitulo.id','capitulo.code', 'capitulo.name', 'capitulo.description', 'orcamento.name as type', 'capitulo.state', 'u0.name as created_by', 'u1.name as updated_by', 'capitulo.created_at', 'capitulo.updated_at']);

            return Datatables::queryBuilder($budget_chapter)
                ->addColumn('actions', function ($item) use ($article_last,$budget_articles) {
                    return view('GA::budget-chapters.datatables.actions',compact('item','article_last','budget_articles'));
                })
                ->addColumn('articles', function ($item) use ($budget_articles) {
                    return view('GA::budget-chapters.datatables.chapters',compact('item','budget_articles'));
                })
                // ->addColumn('state', function ($item) {
                //     return view('GA::budget-chapters.datatables.state')->with('state', $item);
                // })
                ->addColumn('money', function ($item) use ($budget_articles) {
                    return view('GA::budget-chapters.datatables.money',compact('item','budget_articles'));
                })
                ->rawColumns(['actions', 'articles', 'money'/*,'state'*/])
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
    public function create($id)
    {
        try {

            // Listar todos os tipos de eventos
           
                $budgets = DB::table('bd_budget')
                ->whereNull('deleted_at')
                ->whereNull('deleted_by')
                ->where('id',$id)
                ->select(['id','name']) 
                ->first();

            $data = [
                'action' => 'create',
                'budgets' => $budgets
            ];
            return view('GA::budget-chapters.budget_chapter')->with($data);
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


            

            // Verificar o código do último orçamento

             $chapter = DB::table('bd_chapter')
            ->where('budget_id',$request->get('budget_id'))
            ->orderBy("code",'desc')
            ->select(["code"])
            ->first();

            $capitulo =1;

            if (isset($chapter->code)) {
              $capitulo = $chapter->code+1;
            } 

            
            


            $id_budget = DB::table('bd_chapter')->insertGetId(
                [
                    'name' => $request->get('name'),
                    'budget_id' => $request->get('budget_id'),
                    'description' => $request->get('description'),
                    'state' => "espera",
                    'code'=> $capitulo,
                    'created_by' => auth()->user()->id
                ]
            );

            $codigo = $request->get('budget_id');

            // Success message
            Toastr::success(__('Capítulo criado com sucesso'), __('toastr.success'));
            return redirect()->route('budget_chapter.budget',$codigo);
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


            $budget_chapter = DB::table('bd_chapter')
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->whereNull('deleted_by')
                ->select(['id', 'name', 'description', 'budget_id', 'state'])
                ->first();

            $budgets = DB::table('bd_budget as orcamento')
                ->join("bd_chapter as capitulo",'capitulo.budget_id','=','orcamento.id')
                ->whereNull('capitulo.deleted_at')
                ->whereNull('capitulo.deleted_by')
                ->where('capitulo.id',$budget_chapter->id)
                ->select(['orcamento.id','orcamento.name']) 
                ->first();

            $budget = DB::table('bd_budget')
                ->whereNull('deleted_at')
                ->whereNull('deleted_by')
                ->where('state',"!=","concluido")
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();

            $data = [
                'budget_chapter' => $budget_chapter,
                'budget' => $budget,
                'budgets' => $budgets,
                'action' => $action
            ];

            return view('GA::budget-chapters.budget_chapter')->with($data);
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


       
            $update = DB::table('bd_chapter')
                ->where('id', "=", $id)
                ->update(
                    [
                        'name' => $request->get('name'),
                        'budget_id' => $request->get('budget_id'),
                        'description' => $request->get('description'),
                        "updated_by" => auth()->user()->id,
                        "updated_at" => Carbon::now()
                    ]
                );



            // Success message
            Toastr::success(__('Capítulo actualizado com sucesso'), __('toastr.success'));
            return redirect()->route('budget_chapter.show', $id);
            
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::budget.not_found_message'), __('toastr.error'));
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

            
            
            $update = DB::table('bd_chapter')
                ->where('id', "=", $request->get("id"))
                ->update(
                    [
                        "deleted_by" => auth()->user()->id,
                        "deleted_at" => Carbon::now()
                    ]
                );

            // Success message
            Toastr::success(__('Capítulo eliminado com sucesso'), __('toastr.success'));
            return redirect()->back();
        } catch (ModelNotFoundException $e) { 
            Toastr::error(__('GA::budget-chapters.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


   
}
