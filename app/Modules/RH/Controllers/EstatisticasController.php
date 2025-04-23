<?php

namespace App\Modules\RH\Controllers;

use App\Helpers\LanguageHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\GA\Models\Course;
use App\Modules\GA\Models\LectiveYear;
use Toastr;
use App\Modules\Users\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

use PDF;
use App\Model\Institution;

class EstatisticasController extends Controller
{

    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {

            //Pegar o ano lectivo na select

            $courses = Course::with(['currentTranslation'])->get();

            $data = ['courses' => $courses];



            return view("RH::estatistica-RH.index")->with($data);
        } catch (Exception | Throwable $e) {

            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    //BUSCA O DOCENTE DE ACORDO AO CURSO


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {

            
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */



    public function store(Request $request)
    {

        //Bem no final de lançar as notas alguém tem que fechar elas.

        try {
        } catch (Exception | Throwable $e) {
            Toastr::error($e->getMessage(), __('toastr.error'));
            logError($e);
            return $e;
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }










    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    public function show($id)
    {
        try {
            return view("Avaliations::avaliacao-aluno.show-avaliacao-aluno");
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
        return $id;
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        try {
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    // ===========================================  Estatísticas Docentes  ========================================


    public function generateEstatistic(Request $request)
    {
        try {



            if (empty($request->course)) {
                Toastr::error(__('Verifique se selecionou uma turma antes de gerar o PDF.'), __('toastr.error'));
                return redirect()->back();
            }
            $courses = DB::table('courses as curso')
                ->join('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'curso.id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })
                ->where('curso.id', $request->course)
                ->select(["ct.display_name as curso"])
                ->get();

            $curso = $courses[0]->curso;

            $usuarios_cargos = DB::table('users as usuario')
                ->join('model_has_roles as usuario_cargo', 'usuario.id', '=', 'usuario_cargo.model_id')
                ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')
                ->join('user_parameters as up', 'up.users_id', '=', 'usuario.id')
                ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'usuario.id')
                ->leftJoin('user_parameters as sexo', function ($join) {
                    $join->on('usuario.id', '=', 'sexo.users_id')
                        ->where('sexo.parameters_id', 2);
                })

                ->leftJoin('parameter_options as sexo_value', 'sexo_value.id', '=', 'sexo.value')

                # BACHAREL

                ->leftJoin('user_parameters as bacharelato', function ($join) {
                    $join->on('usuario.id', '=', 'bacharelato.users_id')
                        ->where('bacharelato.parameters_id', 285);
                })
                ->leftJoin('parameter_options as bacharelato_value', 'bacharelato_value.id', '=', 'bacharelato.value')

                # LICENCIATURA

                ->leftJoin('user_parameters as licenciatura', function ($join) {
                    $join->on('usuario.id', '=', 'licenciatura.users_id')
                        ->where('licenciatura.parameters_id', 263);
                })
                ->leftJoin('parameter_options as licenciatura_value', 'licenciatura_value.id', '=', 'licenciatura.value')

                # MESTRADO

                ->leftJoin('user_parameters as mestrado', function ($join) {
                    $join->on('usuario.id', '=', 'mestrado.users_id')
                        ->where('mestrado.parameters_id', 286);
                })
                ->leftJoin('parameter_options as mestrado_value', 'mestrado_value.id', '=', 'mestrado.value')

                # DOUTORAMENTO

                ->leftJoin('user_parameters as doutoramento', function ($join) {
                    $join->on('usuario.id', '=', 'doutoramento.users_id')
                        ->where('doutoramento.parameters_id', 287);
                })
                ->leftJoin('parameter_options as doutoramento_value', 'doutoramento_value.id', '=', 'doutoramento.value')


                ->where('up.parameters_id', 1)
                ->where('usuario_cargo.role_id', 1)
                ->where('usuario_cargo.role_id', "!=", 2)
                ->whereNull('usuario.deleted_at')
                ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
                ->orderBy('nome_usuario')
                ->groupBy('id_usuario')
                ->where('uc.courses_id', $request->course)
                ->select([
                    'usuario.id as id_usuario',
                    'up.value as nome_usuario',
                    'sexo_value.code as sexo',
                    'bacharelato_value.code as bacharel',
                    'licenciatura_value.code as licenciado',
                    'mestrado_value.code as mestre',
                    'doutoramento_value.code as doutor',
                    'uc.courses_id as curso'
                ])
                ->get();


            // return $model;

            //    Validação se for vazio a lista de Docente
            if ($usuarios_cargos->isEmpty()) {
                Toastr::error(__('Não foram encontrado(s) Docente(s) no Curso selecionado.'), __('toastr.error'));
                return redirect()->back();
            }


             $docentes = collect($usuarios_cargos)->groupBy('curso')->map(function ($item, $key) {

                $graus = [

                    "bacharel_M" => 0,
                    "bacharel_MP" => 0,
                    "bacharel_F" => 0,
                    "bacharel_FP" => 0,
                    "bacharel_T" => 0,

                    "licenciado_M" => 0,
                    "licenciado_MP" => 0,
                    "licenciado_F" => 0,
                    "licenciado_FP" => 0,
                    "licenciado_T" => 0,

                    "mestre_M" => 0,
                    "mestre_MP" => 0,
                    "mestre_F" => 0,
                    "mestre_FP" => 0,
                    "mestre_T" => 0,

                    "doutor_M" => 0,
                    "doutor_MP" => 0,
                    "doutor_F" => 0,
                    "doutor_FP" => 0,
                    "doutor_T" => 0,
                    
                    "TM" => 0,
                    "TMP" => 0,
                    "TF" => 0,
                    "TFP" => 0,
                    "T" => 0,
                ];

                foreach ($item as $docente) {

                    switch ($docente->sexo) {

                            # Para o sexo  Masculino...

                        case 'Masculino':
                            $grau =null;
                            $graus["TM"] = $graus["TM"]+1;

                            # BACHAREL
                            
                            if (isset($docente->bacharel)) {
                                if(explode('_',$docente->bacharel)[3]=="sim") { 
                                    $grau = 1;                                    
                                }
                            }

                            # LICENCIATURA

                            if (isset($docente->licenciado)) {
                                if(explode('_',$docente->licenciado)[3]=="sim") { 
                                    $grau = 2;                                    
                                }
                            }

                            # MESTRE

                            if (isset($docente->mestre)) {
                                if(explode('_',$docente->mestre)[3]=="sim") { 
                                    $grau = 3;                                    
                                }
                            }

                            # DOUTORAMENTO
                            
                            if (isset($docente->doutor)) {
                                if(explode('_',$docente->doutor)[3]=="sim") { 
                                    $grau = 4;                                    
                                }
                            }
                            
                            if (isset($grau)) {
                                

                                switch ($grau) {
                                    case 1:
                                        $graus["bacharel_M"] = $graus["bacharel_M"]+1;
                                        
                                        break;
                                    case 2:
                                        $graus["licenciado_M"] = $graus["licenciado_M"]+1;
                                        
                                        break;
                                    case 3:
                                        $graus["mestre_M"] = $graus["mestre_M"]+1;
                                        
                                        break;
                                    case 4:
                                        $graus["doutor_M"] = $graus["doutor_M"]+1;
                                        
                                        break;
                                    
                                    default:
                                        # code...
                                        break;
                                }

                            }
                            // else{
                            //     $graus["licenciado_M"] = $graus["licenciado_M"]+1;
                            // }
                            


                            
                            break;

                            # Para o sexo Feminino...

                        case 'Feminino':
                                
                            $grau =null;
                            $graus["TF"] = $graus["TF"]+1;
                            # BACHAREL
                            
                            if (isset($docente->bacharel)) {
                                if(explode('_',$docente->bacharel)[3]=="sim") { 
                                    $grau = 1;                                    
                                }
                            }

                            # LICENCIATURA

                            if (isset($docente->licenciado)) {
                                if(explode('_',$docente->licenciado)[3]=="sim") { 
                                    $grau = 2;                                    
                                }
                            }

                            # MESTRE

                            if (isset($docente->mestre)) {
                                if(explode('_',$docente->mestre)[3]=="sim") { 
                                    $grau = 3;                                    
                                }
                            }

                            # DOUTORAMENTO
                            
                            if (isset($docente->doutor)) {
                                if(explode('_',$docente->doutor)[3]=="sim") { 
                                    $grau = 4;                                    
                                }
                            }
                            
                            if (isset($grau)) {
                                

                                switch ($grau) {
                                    case 1:
                                        $graus["bacharel_F"] = $graus["bacharel_F"]+1;
                                        
                                        break;
                                    case 2:
                                        $graus["licenciado_F"] = $graus["licenciado_F"]+1;
                                        
                                        break;
                                    case 3:
                                        $graus["mestre_F"] = $graus["mestre_F"]+1;
                                        
                                        break;
                                    case 4:
                                        $graus["doutor_F"] = $graus["doutor_F"]+1;
                                        
                                        break;
                                    
                                    default:
                                        # code...
                                        break;
                                }

                            }
                            // else{
                            //     $graus["licenciado_F"] = $graus["licenciado_F"]+1;
                            // }
                            
                           
                            break;

                            # code...

                        default:
                            break;
                    }
                }

                # TOTAL E PERCENTAGENS 

                $graus["bacharel_T"] = $graus["bacharel_M"]+$graus["bacharel_F"];

                if($graus["bacharel_T"]!=0){
                    $graus["bacharel_M"] = (int) round((($graus["bacharel_M"])/$graus["bacharel_T"]) * 100, 0)."%";
                    $graus["bacharel_FP"] = (int) round((($graus["bacharel_F"])/$graus["bacharel_T"]) * 100, 0)."%";
                }

                # TOTAL E PERCENTAGENS

                $graus["licenciado_T"] = $graus["licenciado_M"]+$graus["licenciado_F"];

                if($graus["licenciado_T"]!=0){
                    $graus["licenciado_MP"] = (int) round((($graus["licenciado_M"])/$graus["licenciado_T"]) * 100, 0)."%";
                    $graus["licenciado_FP"] = (int) round((($graus["licenciado_F"])/$graus["licenciado_T"]) * 100, 0)."%";
                }

                # TOTAL E PERCENTAGENS

                $graus["mestre_T"] = $graus["mestre_M"]+$graus["mestre_F"];
                
                if($graus["mestre_T"]!=0){
                    $graus["mestre_MP"] = (int) round((($graus["mestre_M"])/$graus["mestre_T"]) * 100, 0)."%";
                    $graus["mestre_FP"] = (int) round((($graus["mestre_F"])/$graus["mestre_T"]) * 100, 0)."%";
                }

                # TOTAL E PERCENTAGENS

                $graus["doutor_T"] = $graus["doutor_M"]+$graus["doutor_F"];

                if($graus["doutor_T"]!=0){
                    $graus["doutor_MP"] = (int) round((($graus["doutor_M"])/$graus["doutor_T"]) * 100, 0)."%";
                    $graus["doutor_FP"] = (int) round((($graus["doutor_F"])/$graus["doutor_T"]) * 100, 0)."%";
                }
                
                # TOTAL E PERCENTAGENS

                $graus["T"] = $graus["TM"]+$graus["TF"];

                if($graus["T"]!=0){
                    $graus["TMP"] = (int) round((($graus["TM"])/$graus["T"]) * 100, 0)."%";
                    $graus["TFP"] = (int) round((($graus["TF"])/$graus["T"]) * 100, 0)."%";
                }
                
                


                # CALCULAR AS PERCENTAGENS

                return $graus;
            });



            $institution = Institution::latest()->first();
            $Pauta_Name  = "DOCENTES";
            $anoLectivo_documento = "Ano Lectivo :";
            $documentoGerado_documento = "Documento gerado a";
            $documentoCode_documento = 501;
            $logotipo = "https://" . $_SERVER['HTTP_HOST'] . "/instituicao-arquivo/" . $institution->logotipo;
            $id_curso = $request->course;
            $pdf = PDF::loadView("RH::estatistica-RH.pdf.pdf", compact(
                'logotipo',
                'curso',
                'id_curso',
                'docentes',
                'institution',
                'Pauta_Name',
                'anoLectivo_documento',
                'documentoGerado_documento',
                'documentoCode_documento'
            ));


            $pdf->setOption('margin-top', '2mm');
            $pdf->setOption('margin-left', '2mm');
            $pdf->setOption('margin-bottom', '13mm');
            $pdf->setOption('margin-right', '2mm');
            $pdf->setPaper('a4', 'landscape');

            $pdf_name = "Estatística_docente";
            // $footer_html = view()->make('Users::users.partials.pdf_footer', compact('institution'))->render();
            $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
            $pdf->setOption('footer-html', $footer_html);
            return $pdf->stream($pdf_name . '.pdf');
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
}
