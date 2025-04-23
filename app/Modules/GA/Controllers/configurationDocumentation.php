<?php

namespace App\Modules\GA\Controllers;

use App\Exports\IncomeExport;
use App\Exports\PendingExport;
use App\Helpers\LanguageHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\GA\Models\configDocumentation;
use App\Modules\GA\Models\Course;
use App\Modules\Payments\Models\Article;
use App\Modules\Payments\Models\ArticleRequest;
use App\Modules\Payments\Models\Transaction;
use App\Modules\Users\Models\User;
//use Barryvdh\DomPDF\PDF;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

use App\Modules\Users\Models\Role;
use App\Modules\Users\Models\RoleTranslation;
use App\Modules\Users\Models\Matriculation;
use PDF;
use Toastr;

class configurationDocumentation extends Controller
{

        public function index()
        {
            $configDoc=DB::table('config_documentation')->get();
             
            return view('GA::config-documentation-student.index',compact('configDoc'));
        }
        
        public function create()
        {
            echo "Olá mundo das configurações dos documentos";
        }


        public function show($id)
        {

        }
        public function type_document()
        {
            
            
           return $Types= DB::table('documentation_type')->get();
        }



        public function store (Request $request)
        {
            
            $document_type=$request->type_document;
            $current_user =auth()->user()->id;;
            
        if (DB::table('config_documentation')->where('document_type', $document_type)->exists()) {
                
              
             $config = ConfigDocumentation::where('document_type', $document_type)->firstOrFail();
                   $config->user_id=$current_user;
                   $config->document_type=$document_type;
                   $config->cabecalho=$request->cabecalho;
                   $config->titulo_position=$request->titulo_position;
                   $config->tamanho_fonte=$request->font_letra;
                   $config->marca_agua=$request->marca;
                   $config->rodape=$request->rodape;
                   $config->file= 'Nada';
                  
                   $config->save();
          
            Toastr::success(__('A configuração do documento foi actualizada com sucesso'), __('toastr.success'));
            return redirect()->route('documentation.index');
            
           }
           
         else {
             
                $config = new  ConfigDocumentation;
                
                   $config->user_id=$current_user;
                   $config->document_type=$document_type;
                   $config->cabecalho=$request->cabecalho;
                   $config->titulo_position=$request->titulo_position;
                   $config->tamanho_fonte=$request->font_letra;
                   $config->marca_agua=$request->marca;
                   $config->rodape=$request->rodape;
                   $config->file= 'Nada';
                  
                   $config->save();
         
            Toastr::success(__('A configuração do documento foi actualizada com sucesso'), __('toastr.success'));
            return redirect()->route('documentation.index');
         }  
           
        }
        public function update($id)
        {
            
        }


}