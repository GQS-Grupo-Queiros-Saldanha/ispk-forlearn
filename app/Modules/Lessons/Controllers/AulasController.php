<?php

namespace App\Modules\Lessons\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\Classes;
use App\Modules\GA\Models\Schedule;
use App\Modules\GA\Models\ScheduleEvent;
use App\Modules\GA\Models\ScheduleTypeTime;
use App\Modules\GA\Models\Summary;
use App\Modules\Lessons\Models\Lessons;
use App\Modules\Lessons\Requests\LessonRequest;
use App\Modules\Users\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\GA\Models\Course;
use App\Modules\GA\Models\Discipline;
use App\Modules\Users\Models\Matriculation;
use Carbon\Traits\Date;
use Exception;
use Auth;
use Toastr;
use Illuminate\Support\Facades\Validator;
use PDF;
use Throwable;
use Yajra\DataTables\Facades\DataTables;
use App\Modules\Users\Models\Permission;
use App\Modules\Users\Models\Role;
use  App\Model\Institution;


class AulasController extends Controller
{
    public function teste(){
        try {
            return view("Lessons::teste");
        } catch (\Exception $e) {
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function store(Request $request){ 
        
        try{    //  dd($request->all());
            DB::table('teste_disciplina')->insert(
                [
                    "ano"=>$request->ano, 
                    "cadeira"=>$request->cadeira, 
                    "professor"=>$request->professor, 
                    "horario"=>$request->horario
            ]);

            return redirect()->route('lessons.index-teste')->with('valido', 'Inserido com sucesso');

        } catch (\Exception $e) {
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
  
    }

    public function ajax(){
        $sql=DB::table('teste_disciplina')->orderBy('id', 'DESC');
        return Datatables::queryBuilder($sql)
        ->addColumn('actions',function($aulas){return view("Lessons::datatables.actions_aulas",compact('aulas'));})
        ->rawColumns(['actions'])
        ->toJson();

    }

    public function index(){
        try{
            return view("Lessons::index-teste");
    } catch (\Exception $e) {
        logError($e);
        return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
    }
    }

    public function edit($id){
        try{
            $aulas = DB::table('teste_disciplina')->where('id',$id)->first();
            return view("Lessons::teste-edit", compact('aulas'));
    } catch (\Exception $e) {
        logError($e);
        return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
    }
    }
    public function update(Request $request, $id){
        try{ 
            
            DB::table('teste_disciplina')->where('id',$id)->update(
                [
                    "ano"=>$request->ano, 
                    "cadeira"=>$request->cadeira, 
                    "professor"=>$request->professor, 
                    "horario"=>$request->horario
            ]);

            return redirect()->route('lessons.index-teste')->with('valido', 'Atualizado com sucesso!');

        } catch (\Exception $e) {
            return redirect()->back();
        }
    }
    
    public function show($id){
        try{ 
            $view = DB::table('teste_disciplina')->where('id',$id)->first();
            return view("Lessons::teste-edit", compact('view'));

        } catch (\Exception $e) {
            return redirect()->back();
        }
    }
    
    public function delete($id){
        try{ 
            $delete = DB::table('teste_disciplina')->delete($id);
            return redirect()
                    ->route('lessons.index-teste')
                    ->with('valido', 'Apagado com sucesso!');

        } catch (\Exception $e) {
            return redirect()->back();
        }
    }

    public function relatorio(){
        
        //dados da instituição  
        $institution = Institution::latest()->first();
        $titulo_documento = "Cadeiras";
        $documentoGerado_documento = "Documento gerado a ".(new Carbon);
        $documentoCode_documento = 1;
        $dataActual = "Benguela, ".$this->dataActual();

        $aulas = DB::table('teste_disciplina')->get();
        $userLogado = DB::table('users')->find(Auth::user()->id);

        //instaciando o PDF  

        $pdf = PDF::loadView("Lessons::pdf.teste-relatorio", compact(
            'aulas',
            'userLogado',
            'institution',
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

        $pdf_name = " ";

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

        public function editora(){
            $masculinos = DB::table('lb_author')->select(['id','genre'])->groupBy('genre')->where('genre','Masculino')->get();
            $femininos = DB::table('lb_author')->select(['id','genre'])->groupBy('genre')->where('genre','Feminino')->get();
            return view('Lessons::editoras.teste-editora', compact('masculinos', 'femininos'));
        }

        public function edtStore(Request $request){
            try{    //  dd($request->all());
                DB::table('lb_author')->insert([
                        "name"=>$request->nomeAutor, 
                        "surname"=>$request->sobrenomeAutor,
                        "genre"=>$request->generoAutor,
                        "country"=>$request->pais,
                        "others_information"=>$request->codigoAutor,
                        "created_by"=>auth()->user()->id
                ]);  
                return redirect()->route('lessons.editora')->with('valido', 'Inserido com sucesso');
            } catch (\Exception $e) {
                logError($e);
                return redirect()->back(); //Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
            }
        }

}
