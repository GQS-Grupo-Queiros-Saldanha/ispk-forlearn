<?php

namespace App\Modules\Cms\Controllers;
use Carbon\Carbon;
use App\Helpers\LanguageHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\Cms\Requests\LanguageRequest;
use DataTables;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Log;
use Throwable;
use Toastr;
use Illuminate\Support\Facades\DB;
use Auth;





class ConfingDevController extends Controller
{

    public function configCode()
    {
        try {
           
             $getcategaria=DB::table('code_category_developer as categoria')
            ->leftJoin("user_parameters as up",function ($join)
            {
                $join->on('up.users_id','=','categoria.created_by')
                ->where('up.parameters_id',1);
            })
            ->select([
                'categoria.id as id_categoria',
                'categoria.code as nome_code',
                'categoria.name_category as nome_categoria',
                'up.value as nomeCriador',
                'categoria.created_at as dataCriacao'
            ])
            ->whereNull('categoria.deleted_by')
            ->get();

            return view("Cms::config-codeDev.config_categria_codigo",compact('getcategaria'));
        } catch (Exception | Throwable $e) {
            return $e;
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
    public function created_categoria(Request $request)
    {

        try {
            
            // return  $request;
            $getcategaria=DB::table('code_category_developer as categoria')->where('categoria.code','=',$request->codigo_categoria)->get();
            if (count($getcategaria)>0) {
               Toastr::error(__('Erro ao criar a categoria, categoria já existe.'), __('toastr.error'));
               return redirect()->back();
            }else{
                $created_categoria=DB::table('code_category_developer')->insert([
                    'name_category' => $request->nome_categira,
                    'code' => strtolower($request->codigo_categoria),
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now()
                ]);
                Toastr::success(__('Categoria criado com sucesso'), __('toastr.success'));
                return redirect()->back();
            }
            
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

   public function getCodeCategoria($id_categoria)
    {
        try {
                $getcategariaCodigo=DB::table('code_category_developer as categoria')
                ->leftJoin("code_developer as code_dev",'code_dev.id_code_category','=','categoria.id')
                ->leftJoin("user_parameters as up",function ($join)
                {
                    $join->on('up.users_id','=','code_dev.created_by')
                    ->where('up.parameters_id',1);
                })
                ->select([
                    'categoria.id as id_categoria',
                    'code_dev.code as codeCat',
                    'code_dev.name_code as nome_code',
                    'code_dev.nota_code as notaCode',
                    'categoria.name_category as nome_categoria',
                    'up.value as nomeCriador',
                    'code_dev.created_at as dataCriacao'
                ])
                ->whereNull('categoria.deleted_by')
                ->whereNull('code_dev.deleted_by')
                ->where('code_dev.id_code_category','=',$id_categoria)
                ->get();
            return response()->json(['data'=>$getcategariaCodigo]);
            } catch (Exception | Throwable $e) {
            return response()->json($e);
                Log::error($e);
                return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
            }
    }
    public function created_codigoInCategory(Request $request)
    {
        // return $request;
        try {
            
            // return  $request;
            $getCode=DB::table('code_developer as code_dev')
            ->where('code_dev.id_code_category','=',$request->categoria)
            ->where('code_dev.name_code','=',$request->nome_codigo)
            ->where('code_dev.code','=',$request->codigo)
            ->get();
            if (count($getCode)>0) {
               Toastr::error(__('Erro ao criar o codigo, codigo já existe.'), __('toastr.error'));
               return redirect()->back();
            }else{
                $createdCode_incategory=DB::table('code_developer')->insert([
                    'id_code_category' => $request->categoria,
                    'name_code' => $request->nome_codigo,
                    'code' => strtolower($request->codigo),
                    'nota_code' => $request->nota,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now(),
                    'update_at' => Carbon::now()
                ]);
                Toastr::success(__('Codigo criado com sucesso'), __('toastr.success'));
                return redirect()->back();
            }
            
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }

    }
    public function getCodeInCategoria($nome_category)
    { 
        try{ 
            // return response()->json($nome_category);
            $getCategory=null;
            $getcodeCategory=getcodeCategory($nome_category);

            if ($nome_category == 'categ_emolum') {
                $getCategory= getArticles();
            } elseif($nome_category == 'categ_rh' ) {
                $getCategory= getImposto();
            }
            elseif($nome_category == 'categ_states' ) {
                $getCategory= getStates();
            }
            
           
           
            
            return response()->json(['data'=>$getCategory,'data_code'=>$getcodeCategory]);
        } catch (Exception | Throwable $e) {
            return response()->json($e);
             Log::error($e);
             return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
         }
    }


    public function created_categoria_save(Request $request)
    {
        // return $request;

        $array=null;
        $qtd=$request->qdtCodigo[0];
        for ($i=1; $i <=$qtd ; $i++) { 
             
            $code=null;
            $code='codigo_'.$i;
            $array=null;
             $array=explode(",",$request->$code);
             $update=DB::table($request->categoriaAtivo)
                ->where('id','=', $array[0])
                ->update([
                    'id_code_dev' => $array[1]
                ]);
        }
        return redirect()->back();
        
        
    }
    
}



