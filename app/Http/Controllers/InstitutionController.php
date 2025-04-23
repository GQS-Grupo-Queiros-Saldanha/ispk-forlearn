<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Institution;
use App\Modules\Users\Models\User;
use App\Modules\Payments\Models\Bank;
use DB;
use PDF;
//use Barryvdh\DomPDF\PDF;
use App\Modules\Users\Models\ParameterGroup;
use App\Modules\GA\Models\Course;
use App\Modules\GA\Models\Department;
use App\Modules\GA\Models\CourseTranslation;
use Carbon\Carbon;
use App\Util\InstitutionUtil;

class InstitutionController extends Controller
{
    private $institutionUtil;
    
    function __construct(){
        $this->institutionUtil = new InstitutionUtil();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    //PROCURA PELO CARGOS 
    public function pesquisar_cargos()
    {
        //
        $director_geral = User::whereHas('roles', function ($q) {
            $q->whereIn('id', [8]);
          }) ->leftJoin('user_parameters as u_p9', function ($q) {
                   $q->on('users.id', '=', 'u_p9.users_id')
                  ->where('u_p9.parameters_id', 1);
           })
        ->get();

        $vice_director_academica = User::whereHas('roles', function ($q) {
            $q->whereIn('id', [9]);
          }) ->leftJoin('user_parameters as u_p9', function ($q) {
                   $q->on('users.id', '=', 'u_p9.users_id')
                  ->where('u_p9.parameters_id', 1);
           })
        ->get();   
        
        $vice_director_cientifica = User::whereHas('roles', function ($q) {
            $q->whereIn('id', [10]);
          }) ->leftJoin('user_parameters as u_p9', function ($q) {
                   $q->on('users.id', '=', 'u_p9.users_id')
                  ->where('u_p9.parameters_id', 1);
           })
        ->get();   

        $daac = User::whereHas('roles', function ($q) {
            $q->whereIn('id', [76]);
          }) ->leftJoin('user_parameters as u_p9', function ($q) {
                   $q->on('users.id', '=', 'u_p9.users_id')
                  ->where('u_p9.parameters_id', 1);
           })
        ->get();  
           
        $gabinete_termos = User::whereHas('roles', function ($q) {
            $q->whereIn('id', [47]);
          }) ->leftJoin('user_parameters as u_p9', function ($q) {
                   $q->on('users.id', '=', 'u_p9.users_id')
                  ->where('u_p9.parameters_id', 1);
           })
        ->get();  
           
        $secretaria_academica = User::whereHas('roles', function ($q) {
            $q->whereIn('id', [18]);
          }) ->leftJoin('user_parameters as u_p9', function ($q) {
                   $q->on('users.id', '=', 'u_p9.users_id')
                  ->where('u_p9.parameters_id', 1);
           })
        ->get();     
           
        $director_executivo = User::whereHas('roles', function ($q) {
            $q->whereIn('id', [16]);
          }) ->leftJoin('user_parameters as u_p9', function ($q) {
                   $q->on('users.id', '=', 'u_p9.users_id')
                  ->where('u_p9.parameters_id', 1);
           })
        ->get();

        $recursos_humanos = User::whereHas('roles', function ($q) {
            $q->whereIn('id', [44]);
          }) ->leftJoin('user_parameters as u_p9', function ($q) {
                   $q->on('users.id', '=', 'u_p9.users_id')
                  ->where('u_p9.parameters_id', 1);
           })
        ->get();

        return $institution_cargos = [$director_geral, $vice_director_academica, $vice_director_cientifica, $daac, $gabinete_termos, $secretaria_academica, $director_executivo, $recursos_humanos];
    }


    
    public function index()
    {
        ///
        try {         
             $institution = Institution::get();
            
            if (count($institution) > 0) {
                $institution = Institution::latest()->first();
                $bancos = Bank::get();
                
                $director = User::whereId($institution->director_geral)->first();
                $director_academica = User::whereId($institution->vice_director_academica)->first();
                $director_cientifica = User::whereId($institution->vice_director_cientifica)->first();
                $daac = User::whereId($institution->daac)->first();
                $gabinete = User::whereId($institution->gabinete_termos)->first();
                $secretaria = User::whereId($institution->secretaria_academica)->first();
                $director_executivo = User::whereId($institution->director_executivo)->first();
                $rh = User::whereId($institution->recursos_humanos)->first();
                
                $institution_cargos = $this->pesquisar_cargos(); 
                
                $pdf = PDF::loadView("institution.pdf", compact(
                        'director', 
                        'director_academica', 
                        'director_cientifica', 
                        'daac', 
                        'gabinete', 
                        'secretaria', 
                        'director_executivo', 
                        'rh',
                        'institution', 
                        'bancos',
                        'institution_cargos'
                    )
                ); 
                
                //$pdf = PDF::loadHTML("<h1>oi</h1>");
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
                
                $pdf_name = "Instituicao_informacao";
                
                $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
                $pdf->setOption('footer-html', $footer_html);
                //$save = $pdf->save(storage_path('app/public/institution/' . $pdf_name));
                return $pdf->stream($pdf_name);            
            }
            else {
                $institution_cargos = $this->pesquisar_cargos();            
                return view('institution/create', compact('institution_cargos'));
            } 
                        
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        } 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        
        try {  
            
            $institution = Institution::get();
            
            if (count($institution) <= 0)
            {                
                $institution_cargos = $this->pesquisar_cargos();
                
                return view('institution/create', compact('institution_cargos'));
    
            }
            else {
    
                return redirect()->route('institution.show');
            }
                        
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return $e;
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
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
        
        $request->validate([
            //'nome' => '|max:255',
            'logotipo' => 'required|mimes:jpeg,png,jpg,gif,svg|max:1048',
            'instituicao_arquivo' => 'required|mimes:pdf|max:2048',
            'cursos_arquivo' => 'required|mimes:pdf|max:2048',
        ]);
        
        
        if ($request->validate = true) {            
            
            //Processamento dos arquivos PDF
            $logotipo_file = time() . '_profile_image' . '.' . request()->logotipo->getClientOriginalExtension();
            $file_logo = $request->logotipo->storeAs('attachment', $logotipo_file);
                        
            $instituicao_decreto = time() . 'instituicao_decreto' . '.' . request()->instituicao_arquivo->getClientOriginalExtension();
            $file_decreto = $request->instituicao_arquivo->storeAs('attachment', $instituicao_decreto);
            
            $curso_decreto = time() . 'curso_decreto' . '.' . request()->cursos_arquivo->getClientOriginalExtension();
            $file_curso = $request->file('logotipo')->storeAs('attachment', $curso_decreto);
            
            $institution = Institution::create([                                            
                //ARQUIVOS
				'logotipo' => $file_logo,
                'instituicao_arquivo' => $file_decreto,//PDF
                'cursos_arquivo' => $file_curso,//PDF
                //DECRETO
                'decreto_instituicao' => $request->get('decreto_instituicao'),
                'decreto_cursos' => $request->get('decreto_cursos'),
				//DADOS 
                'nome' => $request->get('nome'),
                'morada' => $request->get('morada'),
				'provincia' => $request->get('provincia'),
				'municipio' => $request->get('municipio'),
                'contribuinte' => $request->get('contribuinte'),
                'capital_social' => $request->get('capital_social'),
                'registro_comercial_n' => $request->get('registro_comercial_n'),
                'registro_comercial_de' => $request->get('registro_comercial_de'),
                'dominio_internet' => $request->get('dominio_internet'),
                //CONTACTOS
                'telefone_geral' => $request->get('telefone_geral'),
                'telemovel_geral' => $request->get('telemovel_geral'),
                'email' => $request->get('email'),
                'whatsapp' => $request->get('whatsapp'),
                'facebook' => $request->get('facebook'),
                'instagram' => $request->get('instagram'),				
                //ACADÉMICAS
                'director_geral' => $request->get('director_geral'),
                'vice_director_academica' => $request->get('vice_director_academica'),
                'vice_director_cientifica' => $request->get('vice_director_cientifica'),
                'daac' => $request->get('daac'),
                'gabinete_termos' => $request->get('gabinete_termos'),
                'secretaria_academica' => $request->get('secretaria_academica'),
                //ADMINISTRATIVAS
                'director_executivo' => $request->get('director_executivo'),
                'recursos_humanos' => $request->get('recursos_humanos'),
                //PROPRIETARIO
                'nome_dono' => $request->get('nome_dono'),
                'nif' => $request->get('nif'),
            ]);

            //return redirect()->back(); 
            return redirect()->route('institution.show')->with('success', 'Instituição criada com sucesso.');            
            
        }
        else {
            
            return redirect()->route('home');

        }
                
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {   
        
        try { 
              $institution = Institution::get(); 
            
            if (count($institution) > 0) {
                
                //$institution = Institution::whereId(1)->first();             
                $institution = Institution::get()->last();
                $bancos = Bank::get();// whereId($id)->first();
        
                $director = User::whereId($institution->director_geral)->get();
                $director_academica = User::whereId($institution->vice_director_academica)->get();
                $director_cientifica = User::whereId($institution->vice_director_cientifica)->get();
                $daac = User::whereId($institution->daac)->get();
                $gabinete = User::whereId($institution->gabinete_termos)->get();
                $secretaria = User::whereId($institution->secretaria_academica)->get();
                $director_executivo = User::whereId($institution->director_executivo)->get();
                $rh = User::whereId($institution->recursos_humanos)->get();
                
                //return User::whereId($institution->director_executivo)->get();
                
                if ($director_executivo->isEmpty()) {
                    $director_executivo = 0;
                }
                
                
        
                $director = isset($director[0]->email)?$director[0]->email:"";
                $director_academica = isset($director_academica[0]->email)?$director_academica[0]->email:"";
                $director_cientifica = isset( $director_cientifica[0]->email)? $director_cientifica[0]->email:"";
                $daac = isset(  $daac[0]->email)?  $daac[0]->email:"";
                $gabinete = isset(   $gabinete[0]->email)?   $gabinete[0]->email:"";
                $secretaria = isset( $secretaria[0]->email)? $secretaria[0]->email:"";
                // $director_executivo = $director_executivo->email;
                $rh = isset(  $rh[0]->email)? $rh[0]->email:""; 
        
                $institution_cargos = $this->pesquisar_cargos();   
                
                return view('institution/show', compact('director', 'director_academica', 'director_cientifica', 'daac', 'gabinete', 'secretaria', 'director_executivo', 'rh','institution', 'bancos', 'institution_cargos'));       
                                                    
            }
            else {
                //
                return redirect()->route('home');
            }
                        
        } catch (Exception | Throwable $e) {
            dd($e);
            Log::error($e);
            return $e;
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        } 
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        //
        $institution = Institution::get();        
        
        if (count($institution) > 0) { 
            //$institution = Institution::whereId(1)->first();
            $institution = Institution::latest()->first();
            $bancos = Bank::get();// whereId($id)->first();
            
            $institution_cargos = $this->pesquisar_cargos();

            // dd($institution_cargos);
            $provinces = $this->institutionUtil->getProvinces(); 
            $municipios = $this->institutionUtil->getMunicipios();
            
            return view('institution/edit', compact('institution_cargos', 'institution', 'bancos','provinces','municipios'));
        }
        else {
            //
            return redirect()->route('home');
        }
                
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {        
        try {           

            $institution = DB::table('institutions')
            ->latest()->first();

            // VALIDA O LOGOTIPO
            if ($request->logotipo == null)
            {                
                $file_logo = $institution->logotipo;
                
                // return $institution->logotipo;
            }
            else {
                $logotipo_file = time() . '_profile_image' . '.' . request()->logotipo->getClientOriginalExtension();
                $file_logo = $request->logotipo->storeAs('attachment', $logotipo_file);
            }

            // VALIDA O PDF INSTITUIÇÃO
            if ($request->instituicao_arquivo == null)
            {                
                $file_decreto = $institution->instituicao_arquivo;
            }
            else {
                $instituicao_decreto = time() . 'instituicao_decreto' . '.' . request()->instituicao_arquivo->getClientOriginalExtension();
                $file_decreto = $request->instituicao_arquivo->storeAs('attachment', $instituicao_decreto);
            }

            // VALIDA O PDF CURSOS
            if ($request->cursos_arquivo == null)
            {
                $file_curso = $institution->cursos_arquivo;
            }
            else {                
                $curso_decreto = time() . 'curso_decreto' . '.' . request()->cursos_arquivo->getClientOriginalExtension();
                $file_curso = $request->cursos_arquivo->storeAs('attachment', $curso_decreto);

            }            

            $institutions = DB::table('institutions')
                ->where('id', $institution->id)
            ->update([
                'nome' => $request->get('nome'),
                'abrev' => $request->get('abrev'),                
                'morada' => $request->get('morada'),
                'provincia' => $request->get('provincia'),
                'municipio' => $request->get('municipio'),
                'contribuinte' => $request->get('contribuinte'),
                'capital_social' => $request->get('capital_social'),
                'registro_comercial_n' => $request->get('registro_comercial_n'),
                'registro_comercial_de' => $request->get('registro_comercial_de'),
                'dominio_internet' => $request->get('dominio_internet'),

                'telefone_geral' => $request->get('telefone_geral'),
                'telemovel_geral' => $request->get('telemovel_geral'),
                'email' => $request->get('email'),
                'whatsapp' => $request->get('whatsapp'),
                'facebook' => $request->get('facebook'),
                'instagram' => $request->get('instagram'),

                'director_geral' => $request->get('director_geral'),
                'vice_director_academica' => $request->get('vice_director_academica'),
                'vice_director_cientifica' => $request->get('vice_director_cientifica'),
                'daac' => $request->get('daac'),
                'gabinete_termos' => $request->get('gabinete_termos'),
                'secretaria_academica' => $request->get('secretaria_academica'),
                'director_executivo' => $request->get('director_executivo'),
                'recursos_humanos' => $request->get('recursos_humanos'),
                'nome_dono' => $request->get('nome_dono'),
                'nif' => $request->get('nif'),
                
                'logotipo' => $file_logo,
                'instituicao_arquivo' => $file_decreto,
                'cursos_arquivo' => $file_curso,
                'decreto_instituicao' => $request->get('decreto_instituicao'),
                'decreto_cursos' => $request->get('decreto_cursos'),
                'updated_at' => Carbon::Now(), 
                'updated_by' => auth()->user()->id, 
            ]);            
                   
            return redirect()->route('institution.show')->with('success', 'Dados da instituição foram alterados com sucesso.');
            
        }
        catch (Exception | Throwable $e) {
            
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
        

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        //
        //return "apagar";
        $institution_cargos = $this->pesquisar_cargos();  
        $institution = Institution::latest()->first();

        $user_instituion = User::whereId($institution->director_geral)->first();

        $parameter_groups = ParameterGroup::with([
            'currentTranslation',
            'roles',
            'parameters' => function ($q) {
                $q->with([
                    'currentTranslation',
                    'roles',
                    'options' => function ($q) {
                        $q->with([
                            'currentTranslation',
                            'relatedParametersRecursive'
                        ]);
                    }
                ]);
            }
        ])->orderBy('order')->get();

        dd($parameter_groups, $institution_cargos[0]->id, $user_instituion);         
    }

       
    //PEGA OS CURSOS
    private function DepartmentsCourse()
    {
        $departamentos = DB::table('departments')
        ->join('department_translations', 'department_translations.departments_id', '=', 'departments.id')
        ->select([
            'department_translations.display_name as departamento_nome'
        ])
        ->groupBy('department_translations.departments_id')
        ->get();

        $cursos = DB::table('courses')
        ->join('courses_translations', 'courses_translations.courses_id', '=', 'courses.id')
        ->select([
            'courses_translations.display_name as curso_nome'
        ])
        ->groupBy('courses_translations.courses_id')
        ->get();

        $data = [
            'Department' => $departamentos,
            'Course' => $cursos
        ];
        
        return $data;
    }

    //webSite INDEX
    public function webSite()
    {
        return view('web_site/website_layout')->with($this->DepartmentsCourse());
    }

    //webSite ABOUT
    public function webSite_sobre()
    {
        // return "webSite";
        return view('web_site/doc/pag-sobre-nos')->with($this->DepartmentsCourse());
    }

    //webSite CONTACTS
    public function webSite_contactos()
    {
        // return "webSite";
        return view('web_site/doc/pag-contactos')->with($this->DepartmentsCourse());
    }

    //webSite EVENTS
    public function webSite_eventos()
    {
        // return "webSite";
        return view('web_site/doc/pag-eventos')->with($this->DepartmentsCourse());
    }

    //webSite NEWS
    public function webSite_noticia()
    {
        // return "webSite";
        return view('web_site/doc/pag-noticia')->with($this->DepartmentsCourse());
    }

    //webSite SERVICO
    public function webSite_servico()
    {
        // return "webSite";
        return view('web_site/doc/pag-servico')->with($this->DepartmentsCourse());
    }
    
    public function getMunicipios(Request $request){
        if(!isset($request->parameter)) return [];
        return $this->institutionUtil->getMunicipios([$request->parameter]);
    }




}
