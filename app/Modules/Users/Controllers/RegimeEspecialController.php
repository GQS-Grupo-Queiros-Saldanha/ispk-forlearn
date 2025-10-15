<?php

namespace App\Modules\Users\Controllers;

use DB;
use Exception;
use DataTables;
use App\Http\Controllers\Controller;
use App\Modules\Users\Models\User;
use App\Helpers\LanguageHelper;
use App\Model\Institution;
use PDF;
use Log;
use Toastr;
class RegimeEspecialController extends Controller{


    public function index(){
       return view('Users::regime-especial.index');
    }

    public function ajax(){
        try{
           
            $model = User::join('regime_especial', 'regime_especial.user_id', '=', 'users.id')
                                ->leftJoin('rotacao_regime_especial as rotacao', 'regime_especial.rotation_id', '=', 'rotacao.id')
                                ->leftJoin('user_parameters as u_p', function ($join) {
                                    $join->on('users.id', '=', 'u_p.users_id')->where('u_p.parameters_id', 19);
                                })
                                ->leftJoin('user_courses', 'user_courses.users_id', '=', 'users.id')
                                ->leftJoin('courses as crs', 'crs.id', '=', 'user_courses.courses_id')
                                ->leftJoin('courses_translations as ct', function ($join) {
                                    $join->on('ct.courses_id', '=', 'crs.id');
                                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                                    $join->on('ct.active', '=', DB::raw(true));
                                })
                                ->where('regime_especial.are_regime_especial', 1);
                               
                              
            return Datatables::eloquent($model)
            ->addIndexColumn()
            ->toJson();
           
        }
        catch(Exception $e){
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function pdfRegimeEspecial()
    {
        try {
            // Recupera os dados de todos os bolsistas
            $regime_especial = User::join('regime_especial', 'regime_especial.user_id', '=', 'users.id')
            ->leftJoin('rotacao_regime_especial as rotacao', 'regime_especial.rotation_id', '=', 'rotacao.id')
                ->leftJoin('user_parameters as u_p', function ($join) {
                    $join->on('users.id', '=', 'u_p.users_id')
                        ->where('u_p.parameters_id', 19);
                })
                ->leftJoin('user_parameters as full_name', function ($join) {
                    $join->on('users.id', '=', 'full_name.users_id')
                        ->where('full_name.parameters_id', 1);
                })
                ->leftJoin('user_courses', 'user_courses.users_id', '=', 'users.id')
                ->leftJoin('courses as crs', 'crs.id', '=', 'user_courses.courses_id')
                ->leftJoin('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'crs.id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })
                ->select([
                    'full_name.value as name',
                    'u_p.value as matricula',
                    'users.email as email',
                    'rotacao.nome as rotacao',
                    'ct.display_name as display_name'
                ])
                ->where('regime_especial.are_regime_especial', 1)
                ->orderBy('name', 'asc')
                ->get();

            
            if($regime_especial->isEmpty()){
                Toastr::warning(__('Nenhum estudante encontrado'),__('toastr.warning'));
                return redirect()->back();
            }
            

            // Obtendo a instituição
            $institution = Institution::latest()->first();

            // Gerar um nome de arquivo único
            $pdf_name = "Relatorio_Regime_Especial" . date("Y_m_d") . ".pdf";
            $pdf_path = storage_path('pdfs/' . $pdf_name);

            // Verifica se o arquivo já existe e exclui
            if (file_exists($pdf_path)) {
                unlink($pdf_path);
            }

            // Gerar o PDF
            $pdf = PDF::loadView(
                'Users::regime-especial.pdf.pdf_regime_especial',
                [
                    'regime_especial' => $regime_especial,
                    'institution' => $institution,
                    'titulo_documento' => "Relatório de Regime Especial",
                    'documentoGerado_documento' => "Documento gerado em " . date("Y/m/d")
                ]
            );

            $pdf->setOption('margin-top', '1mm');
            $pdf->setOption('margin-left', '1mm');
            $pdf->setOption('margin-bottom', '4mm');
            $pdf->setOption('margin-right', '1mm');
            $pdf->setOption('enable-javascript', true);
            $pdf->setOption('debug-javascript', true);
            $pdf->setOption('javascript-delay', 1000);
            $pdf->setOption('enable-smart-shrinking', true);
            $pdf->setOption('no-stop-slow-scripts', true);
            $pdf->setPaper('a4', 'portrait');

            // Salvar o PDF em uma pasta no servidor
            $pdf->save($pdf_path);

            // Retornar uma mensagem de sucesso
            return $pdf->stream($pdf_name . '.pdf');

        } catch (Exception $e) {
            // Trate a exceção adequadamente
            Log::error($e->getMessage());
            return response()->json($e->getMessage(), 500);
        }
    }

}