<?php
namespace App\Modules\Lessons\Controllers;

use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use  App\Model\Institution;
use Carbon\Carbon;
use Exception;
use Auth;
use PDF;
use DB;

class SedracController extends Controller{

    public function lib_auth(){
        return view('Lessons::libary.authr');
    }

    public function lib_areas(){
        return view('Lessons::libary.areas');
    }

    public function lib_computer(){
        return view('Lessons::libary.computadores');
    }

    public function lib_auth_store(Request $request){
        try{
            dd($request->all());
        }catch(Exception $error){
            dd($error);
        }
    }

    //Start views
    public function hello(){
        return view('Lessons::hello');
    }

    public function edit($id){
        $user = DB::table('teste_usuario')->find($id);
        $generos =['MASCULINO'=>'Masculino', 'FEMENINO'=>'Femenino'];
        return view('Lessons::hello',compact('user','generos'));
    }
    //end views

    //Start Response
    public function get_all(){
        $arrays = DB::table('teste_usuario');
        return Datatables::queryBuilder($arrays)->addColumn('actions', function ($item) {
                    return view('Lessons::datatables.actions_sed')->with('item', $item);
               })->rawColumns(['actions'])->toJson();
    } 
    //end Response   
 
    private function validar(Request $request){
        $request->validate([
            'nome' => ['required','string'],
            'sobrenome' => ['required','string'],
            'genero' => ['required','regex:/[MASCULINO|FEMENINO]/'],
            'nascimento' => ['required','date'],
        ]);
    }

    public function store(Request $request){
        try{
            $this->validar($request);
            DB::insert('insert into teste_usuario  values (DEFAULT,?,?,?,?)', [
                $request->nome,
                $request->sobrenome,
                $request->genero,
                $request->nascimento
            ]);
            return redirect()->back()->with('success','A operação foi realizada com sucesso');
        }catch(Exception $error){
            return redirect()->back()->with('error','Não foi possível a operação');
        }
    }

    public function update(Request $request, $id){
        try{
            $this->validar($request);
            DB::update('update teste_usuario set nome=?, sobrenome=?, genero=?, nascimento=? where id=?', [
                $request->nome,
                $request->sobrenome,
                $request->genero,
                $request->nascimento,
                $id
            ]);
            return redirect()->route('lessons.hello')->with('success','A operação foi realizada com sucesso');;
        }catch(Exception $error){
            return redirect()->back()->with('error','Não foi possível a operação');
        }
    }

    public function delete($id){
        try{
            DB::table('teste_usuario')->delete($id);
            return redirect()->route('lessons.hello')->with('success','A operação foi realizada com sucesso');;
        }catch(Exception $error){
            return redirect()->back()->with('error','Não foi possível a operação');
        }
    }

    public function show()
    {
        //dados da instituição  
        $institution = Institution::latest()->first();
        $titulo_documento = "Úsuario";
        $documentoGerado_documento = "Documento gerado a ".(new Carbon);
        $documentoCode_documento = 1;
        $dataActual = "Benguela, ".$this->dataActual();

        $users = DB::table('teste_usuario')->get();
        $userLogado = DB::table('users')->find(Auth::user()->id);

        //instaciando o PDF  

        $pdf = PDF::loadView("Lessons::pdf.body", compact(
            'users',
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

}