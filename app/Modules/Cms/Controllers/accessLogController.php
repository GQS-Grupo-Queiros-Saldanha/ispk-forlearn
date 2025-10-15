<?php

namespace App\Modules\Cms\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Modules\Cms\Models\Language;
use App\Modules\Cms\Models\Menu;
use App\Modules\Cms\Models\MenuItem;
use App\Modules\Cms\Requests\MenuRequest;
use App\Modules\Cms\Models\MenuTranslation;
use Carbon\Carbon;
use DataTables;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Log;
use Throwable;
use Toastr;

class accessLogController extends Controller
{

    public function index()
    {
        try {
            
           
              
            return view('Cms::access-control.index');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {
        $cargos = DB::table('users as usuario')
            ->join('model_has_roles as usuario_cargo', 'usuario.id', '=', 'usuario_cargo.model_id')
            ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')
            ->join('role_translations as ct', 'ct.role_id', '=', 'usuario_cargo.role_id')
            ->where('usuario_cargo.role_id','!=',2)
            ->whereNull('usuario.deleted_at')
            ->whereNull('ct.deleted_at')
            ->where('ct.active',1)
            ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
            ->whereNotIn('usuario.id',users_exemplo())
            ->select(['usuario.id as id_usuario',  'ct.display_name as cargo_usuario'])
            ->get();

        $superadmin = DB::table('model_has_roles as usuario_cargo')
            ->where('usuario_cargo.role_id','=',2)
            ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
            ->select(['usuario_cargo.model_id'])
            ->get();

         $user=array();

         foreach ($superadmin as $value) {
            $user[]=$value->model_id;
         }

         

          $dados = DB::table('tb_acess_control_log as log')
            ->join('users as u', 'u.id', '=', 'log.id_user')
            ->whereNotIn("u.id",$user)
            ->leftJoin('user_parameters as full_name', function ($join) {
                $join->on('u.id', '=', 'full_name.users_id')
                ->where('full_name.parameters_id', 1);
           })
           ->whereNotIn('log.id_user',users_exemplo())
           ->select(['log.id_user','full_name.value as full_name','u.email as email', 'log.data as acess_data']);
           return Datatables::of($dados)
           ->addColumn('roles', function ($item) use($cargos) {
               return view('Cms::access-control.datatables.roles',compact('item','cargos'));
           })
         
           ->rawColumns(['roles'])
           ->addIndexColumn()
           ->toJson();


            

        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage());
        }
    }

    public function create()
    {
        try {
  
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(MenuRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create
            $menu = Menu::create([
                'code' => $request->get('code')
            ]);
            $menu->save();

            // Create translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {

                $menu_translations[] = [
                    'menus_id' => $menu->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id],
                    'description' => $request->get('description')[$language->id],
                    'created_at' => Carbon::now(),
                    'version' => 1,
                    'active' => true
                ];
            }

            if (!empty($menu_translations)) {
                MenuTranslation::insert($menu_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('Cms::menus.store_success_message'), __('toastr.success'));
            return redirect()->route('menus.index');

        } catch (Exception | Throwable $e) {
            Toastr::error($e->getMessage(), __('toastr.error'));
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    private function fetch($id, $action)
    {
        try {
        
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Cms::menus.not_found_message'), __('toastr.error'));
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
     * @return Response
     */
    public function show($id)
    {
        try {
          
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        try {
          
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(MenuRequest $request, $id)
    {
        try {
            DB::beginTransaction();


            DB::commit();

       

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Cms::menus.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    public function destroy($id)
    {
        try {
        
           

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Cms::menus.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function items(Request $request)
    {
        try {
            if ($request->has('menus_id')) {
                $items = MenuItem::with([
                    'translation' => function ($q) {
                        $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
                    }
                ])->whereMenusId($request->get('menus_id'))->get();
                return $items;
            }
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
        function repair(){
        return view('Cms::repair.index'); 
    }

}
