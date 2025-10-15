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

class BudgetArticlesController extends Controller
{
    /** 
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        try {
            return view('GA::budget-articles.index');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return $e;
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function budget_articles($id)
    {

        try {
           
            $chapter = DB::table('bd_budget_articles as artigo')
            ->join('bd_chapter as capitulo','capitulo.id','=','artigo.chapter_id')
            ->join('bd_budget as orcamento','capitulo.budget_id','=','orcamento.id')
            ->whereNull('artigo.deleted_at')
            ->whereNull('artigo.deleted_by')
            ->where('capitulo.id',$id)
            ->select([
                'artigo.id as id_artigo',
                'artigo.name as nome_artigo',
                'artigo.code as code_artigo',
                'capitulo.id as id_capitulo',
                'orcamento.name as nome_orcamento',
                'orcamento.id as id_orcamento',
                'capitulo.name as nome_capitulo',
                'capitulo.code as code_capitulo']) 
            ->first();
          

            return view('GA::budget-articles.index',compact('chapter'));
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return $e;
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax($id)
    {
        try {

             

            $budget_articles = DB::table('bd_budget_articles as artigos')
                
                ->join('users as u0', 'u0.id', "=", 'artigos.created_by')
                ->leftjoin('users as u1', 'u1.id', "=", 'artigos.updated_by')
                ->whereNull('artigos.deleted_at')
                ->whereNull('artigos.deleted_by')
                ->where('artigos.chapter_id',$id)
                ->select(['artigos.id','artigos.code', 'artigos.name', 'artigos.description','artigos.money','artigos.unidade','artigos.quantidade','u1.name as updated_by', 'u0.name as created_by', 'u1.name as updated_by']);

            return Datatables::queryBuilder($budget_articles)
                ->addColumn('actions', function ($item) {
                    return view('GA::budget-articles.datatables.actions')->with('item', $item);
                })
                ->addColumn('total', function ($item) {
                    return view('GA::budget-articles.datatables.money')->with('item', $item);
                })
                ->addColumn('unitario', function ($item) {
                    return view('GA::budget-articles.datatables.unitario')->with('item', $item);
                })
                ->rawColumns(['actions', 'total','unitario'])
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

            $budget_chapter = DB::table('bd_chapter as capitulo')
                ->join('bd_budget as orcamento','orcamento.id','=','capitulo.budget_id')
                ->whereNull('capitulo.deleted_at')
                ->whereNull('capitulo.deleted_by')
                ->where('capitulo.id',$id)
                ->select([
                    'capitulo.id as id_capitulo',
                    'capitulo.name as nome_capitulo',
                    'capitulo.code as code_capitulo',
                    'orcamento.id as id_orcamento',
                    'orcamento.name as nome_orcamento'
                    ]) 
                ->first();

            $data = [
                'action' => 'create',
                'budget_chapter' => $budget_chapter
            ];
            return view('GA::budget-articles.budget_articles')->with($data);
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

             $articles = DB::table('bd_budget_articles')
            ->where('chapter_id',$request->get('chapter_id'))
            ->orderBy("code",'desc')
            ->select(["code"])
            ->first();

            $code =1;
 
            if (isset($articles->code)) {
              $code = $articles->code+1;
            } 

            $id = DB::table('bd_budget_articles')->insertGetId(
                [
                    'name' => $request->get('name'),
                    'chapter_id' => $request->get('chapter_id'),
                    'description' => $request->get('description'),
                    'quantidade' => $request->get('quantidade'),
                    'unidade' => $request->get('unidade'),
                    'money' => $request->get('valor'),
                    'code'=> $code,
                    'created_by' => auth()->user()->id
                ]
            );

            $codigo = $request->get('chapter_id');


            // Success message
            Toastr::success(__('Artigo criado com sucesso'), __('toastr.success'));
            return redirect()->route('budget_articles.budget',$codigo);
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


            $budget_chapter = DB::table('bd_chapter as capitulo')
                ->join('bd_budget as orcamento','orcamento.id','=','capitulo.budget_id')
                ->join('bd_budget_articles as artigos','artigos.chapter_id','=','capitulo.id')
                ->whereNull('capitulo.deleted_at')
                ->whereNull('capitulo.deleted_by')
                ->where('artigos.id',$id)
                ->select([
                    'capitulo.id as id_capitulo',
                    'capitulo.name as nome_capitulo',
                    'capitulo.code as code_capitulo',
                    'orcamento.id as id_orcamento',
                    'orcamento.name as nome_orcamento'
                    ]) 
                ->first();
            
            $budget_articles = DB::table('bd_budget_articles as artigos')
            ->join('users as u0', 'u0.id', "=", 'artigos.created_by')
            ->leftjoin('users as u1', 'u1.id', "=", 'artigos.updated_by')
            ->whereNull('artigos.deleted_at')
            ->whereNull('artigos.deleted_by')
            ->where('artigos.id',$id)
            ->select(['artigos.id','artigos.code', 'artigos.name', 'artigos.description','artigos.money','artigos.chapter_id','artigos.unidade','artigos.quantidade','u1.name as updated_by', 'u0.name as created_by', 'u1.name as updated_by'])
            ->first();

            $data = [
                'budget_chapter' => $budget_chapter,
                'budget_articles' =>$budget_articles,
                'action' => $action
            ];

            return view('GA::budget-articles.budget_articles')->with($data);
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


            
       
            $update = DB::table('bd_budget_articles')
                ->where('id', "=", $id)
                ->update(
                    [
                        'name' => $request->get('name'),
                        'chapter_id' => $request->get('chapter_id'),
                        'description' => $request->get('description'),
                        'quantidade' => $request->get('quantidade'),
                        'unidade' => $request->get('unidade'),
                        'money' => $request->get('valor'),
                        "updated_by" => auth()->user()->id,
                        "updated_at" => Carbon::now()
                    ]
                );



            // Success message
            Toastr::success(__('Artigo actualizado com sucesso'), __('toastr.success'));
            return redirect()->route('budget_articles.show', $id);
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
  
            $update = DB::table('bd_budget_articles')
                ->where('id', "=", $request->get("id"))
                ->update(
                    [
                        "deleted_by" => auth()->user()->id,
                        "deleted_at" => Carbon::now()
                    ]
                ); 

            // Success message
            Toastr::success(__('Artigo eliminado com sucesso'), __('toastr.success'));
            return redirect()->route('budget.index');
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::budget-articles.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


   
}
