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
use PDF;
use App\Model\Institution;

class BudgetController extends Controller
{
    /** 
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        try {
            return view('GA::budget.index');
        } catch (Exception | Throwable $e) {
            Log::error($e);

            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {

            $budget_last = DB::table('bd_chapter')
                ->whereNull('deleted_at')
                ->whereNull('deleted_by')
                ->orderBy("code", "desc")
                ->select(['budget_id', 'code'])
                ->get();

            $budget_chapter = DB::table('bd_chapter')
                ->whereNull('deleted_at')
                ->whereNull('deleted_by')
                ->select(['budget_id', 'code'])
                ->get();

            $budget_articles = DB::table('bd_chapter as capitulos')
                ->join('bd_budget_articles as artigos', 'capitulos.id', "=", 'artigos.chapter_id')
                ->whereNull('artigos.deleted_at')
                ->whereNull('artigos.deleted_by')
                ->whereNull('capitulos.deleted_by')
                ->select(['capitulos.id', 'artigos.money', 'artigos.chapter_id', 'capitulos.budget_id', 'artigos.quantidade'])
                ->get();

            $budget = DB::table('bd_budget as orcamento')
                ->join('users as u0', 'u0.id', "=", 'orcamento.created_by')
                ->leftjoin('users as u1', 'u1.id', "=", 'orcamento.updated_by')
                ->join('bd_budget_type as tipo', 'tipo.id', "=", 'orcamento.budget_type')
                ->whereNull('orcamento.deleted_at')
                ->whereNull('orcamento.deleted_by')
                ->select(['orcamento.id', 'orcamento.name', 'orcamento.description', 'tipo.name as type', 'orcamento.state', 'u0.name as created_by', 'u1.name as updated_by', 'orcamento.created_at', 'orcamento.updated_at']);

            return Datatables::queryBuilder($budget)
                ->addColumn('actions', function ($item) use ($budget_last, $budget_chapter) {
                    return view('GA::budget.datatables.actions', compact('item', 'budget_last', 'budget_chapter'));
                })
                ->addColumn('chapters', function ($item) use ($budget_chapter) {
                    return view('GA::budget.datatables.chapters', compact('item', 'budget_chapter'));
                })
                // ->addColumn('state', function ($item) {
                //     return view('GA::budget.datatables.state')->with('state', $item);
                // })
                ->addColumn('money', function ($item) use ($budget_articles) {
                    return view('GA::budget.datatables.money', compact('item', 'budget_articles'));
                })
                ->rawColumns(['actions', 'chapters', /*,'state'*/ 'money'])
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

            $types = DB::table('bd_budget_type')
                ->whereNull('deleted_at')
                ->whereNull('deleted_by')
                ->select(['id', 'name'])
                ->get();

            $data = [
                'action' => 'create',
                'types' => $types
            ];
            return view('GA::budget.budget')->with($data);
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

            $id_budget = DB::table('bd_budget')->insertGetId(
                [
                    'name' => $request->get('name'),
                    'budget_type' => $request->get('budget_type'),
                    'description' => $request->get('description'),
                    'state' => "espera",
                    'created_by' => auth()->user()->id
                ]
            );



            // Success message
            Toastr::success(__('Orçamento criado com sucesso'), __('toastr.success'));
            return redirect()->route('budget.index');
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


            $budget = DB::table('bd_budget')
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->whereNull('deleted_by')
                ->select(['id', 'name', 'description', 'budget_type', 'state'])
                ->first();

            $budget_type = DB::table('bd_budget_type')
                ->whereNull('deleted_at')
                ->whereNull('deleted_by')
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();

            $data = [
                'budget' => $budget,
                'types' => $budget_type,
                'action' => $action
            ];

            return view('GA::budget.budget')->with($data);
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



            $update = DB::table('bd_budget')
                ->where('id', "=", $id)
                ->update(
                    [
                        'name' => $request->get('name'),
                        'budget_type' => $request->get('budget_type'),
                        'description' => $request->get('description'),
                        "updated_by" => auth()->user()->id,
                        "updated_at" => Carbon::now()
                    ]
                );



            // Success message
            Toastr::success(__('Orçamento actualizado com sucesso'), __('toastr.success'));
            return redirect()->route('budget.show', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::events.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
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



            $update = DB::table('bd_budget')
                ->where('id', "=", $request->get("id"))
                ->update(
                    [
                        "deleted_by" => auth()->user()->id,
                        "deleted_at" => Carbon::now()
                    ]
                );

            // Success message
            Toastr::success(__('Orçamento eliminado com sucesso'), __('toastr.success'));

            return redirect()->route('budget.index');
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::budget.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function reports($id)
    {

        // Pesquisar todos os computadores cadastrados no sistema



        $orcamento = DB::table('bd_budget')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->whereNull('deleted_by')
            ->select(['id', 'name', 'description', 'budget_type', 'state'])
            ->first();

        $capitulo = DB::table('bd_chapter')
            ->where('budget_id',  $orcamento->id)
            ->whereNull('deleted_at')
            ->whereNull('deleted_by')
            ->orderBy('code')
            ->select(['code','id', 'name'])
            ->get();

        $artigo = DB::table('bd_chapter as capitulo')
            ->join('bd_budget as orcamento','orcamento.id','=','capitulo.budget_id')
            ->join('bd_budget_articles as artigos','artigos.chapter_id','=','capitulo.id')
            ->whereNull('capitulo.deleted_at')
            ->whereNull('capitulo.deleted_by')
            ->whereNull('artigos.deleted_at')
            ->whereNull('artigos.deleted_by')
            ->where('capitulo.budget_id',$id)
            ->orderBy('artigos.code')
            ->select([
                'capitulo.id as id_capitulo',
                'artigos.code as code_artigo',
                'artigos.name as nome_artigo',
                'artigos.money as money_artigo',
                'artigos.quantidade as quantidade_artigo',
                'artigos.unidade as unidade_artigo',
                ]) 
            ->get();


        //dados da instituição  

        $institution = Institution::latest()->first();
        $titulo_documento = "ORÇAMENTOS";
        $documentoGerado_documento = "Documento gerado a ";
        $documentoCode_documento = 1;
        $dataActual = $this->dataActual();

        $funcionario = DB::table('users as usuario')
            ->leftjoin('user_parameters as u_p', 'usuario.id', '=', 'u_p.users_id')
            ->where('usuario.id',auth()->user()->id)
            ->where('u_p.parameters_id', 1)
            ->whereNull('usuario.deleted_at')
            ->select([
                'usuario.id as id_usuario','u_p.value'
            ])
            ->first();

        //instaciando o PDF  

        $pdf = PDF::loadView("GA::budget.pdf.budget-pdf", compact(
            'orcamento',
            'capitulo',
            'artigo',
            'institution',
            'funcionario',
            'dataActual',
            'titulo_documento',
            'documentoGerado_documento',
            'documentoCode_documento'
        ));
        //Configuração
        $pdf->setOption('margin-top', '1mm');
        $pdf->setOption('margin-left', '1mm');
        $pdf->setOption('margin-bottom', '12mm');
        $pdf->setOption('margin-right', '1mm');
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('debug-javascript', true);
        $pdf->setOption('javascript-delay', 1000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);
        $pdf->setPaper('a4', 'portrait');

        //Nome do documento PDF  

        $pdf_name = "Orçamento";

        //Rodapé do PDF

        $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();

        $pdf->setOption('footer-html', $footer_html);

        //Retornar o PDF 

        return $pdf->stream($pdf_name . '.pdf');
    }
    private function dataActual()
    {
        $m = date("m");
        $mes = array(
            "01" => "Janeiro", "02" => "Fevereiro",
            "03" => "Março", "04" => "Abril",
            "05" => "Maio", "06" => "Junho",
            "07" => "Julho", "08" => "Agosto",
            "09" => "Setembro", "10" => "Outubro",
            "11" => "Novembro", "12" => "Dezembro"
        );
        $data = date("d") . " de " . $mes[$m] . " de " . date("Y");
        return $data;
    }
}
