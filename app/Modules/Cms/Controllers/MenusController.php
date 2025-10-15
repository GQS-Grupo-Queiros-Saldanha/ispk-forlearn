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

class MenusController extends Controller
{   
    public function index()
    {
        try {
            return view('Cms::menus.index');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {

            $model = Menu::join('users as u1', 'u1.id', '=', 'menus.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'menus.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'menus.deleted_by')
                ->leftJoin('menu_translations as mt', function ($join) {
                    $join->on('mt.menus_id', '=', 'menus.id');
                    $join->on('mt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('mt.active', '=', DB::raw(true));
                })
                ->select(['menus.*', 'u1.name as created_by', 'u2.name as updated_by', 'u3.name as deleted_by', 'mt.display_name']);

            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('Cms::menus.datatables.actions')->with('item', $item);
                })
               /* ->editColumn('created_at', function ($item) {
                    return TimeHelper::time_elapsed_string($item->created_at);
                })
                ->editColumn('updated_at', function ($item) {
                    return TimeHelper::time_elapsed_string($item->updated_at);
                })
                ->editColumn('deleted_at', function ($item) {
                    return TimeHelper::time_elapsed_string($item->deleted_at);
                })*/
                ->rawColumns(['actions'])
                ->toJson();

        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage());
        }
    }

    public function create()
    {
        try {
            $data = [
                'action' => 'create',
                'languages' => Language::whereActive(true)->get()
            ];
            return view('Cms::menus.menu')->with($data);
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
            // Find
            $menu = Menu::whereId($id)->with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                }
            ])->firstOrFail();

            $data = [
                'action' => $action,
                'menu' => $menu,
                'translations' => $menu->translations->keyBy('language_id')->toArray(),
                'languages' => Language::whereActive(true)->get()
            ];
            return view('Cms::menus.menu')->with($data);
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
     * @return Response
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
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(MenuRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Fetch the default language
            $default_language = Language::whereDefault(true)->firstOrFail();

            // Find and update
            $menu = Menu::whereId($id)->firstOrFail();
            $menu->code = $request->get('code');
            $menu->save();

            // Disable previous translations
            MenuTranslation::where('menus_id', $menu->id)->update(['active' => false]);

            $version = MenuTranslation::where('menus_id', $menu->id)->whereLanguageId($default_language->id)->count() + 1;

            // Associated translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {
                $menu_translations[] = [
                    'menus_id' => $menu->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id] ?? null,
                    'description' => $request->get('description')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => $version,
                    'active' => true,
                ];
            }

            if (!empty($menu_translations)) {
                MenuTranslation::insert($menu_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('Cms::menus.update_success_message'), __('toastr.success'));
            return redirect()->route('menus.show', $id);
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
            DB::beginTransaction();

            // Find and delete
            $menu = Menu::whereId($id)->firstOrFail();
            $menu->delete();

            DB::commit();

            // Success message
            Toastr::success(__('Cms::menus.destroy_success_message'), __('toastr.success'));
            return redirect()->route('menus.index');

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

    public static function fr_menu(){
        $menu =DB::table('menus')
        ->leftJoin('menu_items as m_i', 'm_i.menus_id', '=', 'menus.id')
        ->leftJoin('menu_item_translations as mit', 'mit.menu_items_id', '=', 'm_i.id')
        ->where("mit.active","1")
        ->where("mit.language_id","1")
        ->where("menus.order","<",13)
        ->whereNull("m_i.parent_id")
        ->whereNull("menus.deleted_at")
        ->whereNull("m_i.deleted_at")
        ->whereNull("mit.deleted_at")
        ->select([
            "menus.id",
            "menus.code",
            "menus.order",
            "m_i.parent_id",
            "m_i.id as menu_item",
            "m_i.external_link",
            "m_i.external_link",
            "mit.display_name",
            "menus.grid",
        ])
        ->orderBy("menus.order")
        ->get();
    return $menu; 
    }


    public static function verify_permission($menu){
   
            
        
        $menu = DB::table('model_has_permissions as menu_permission')
        ->where('menu_permission.model_type', "App\Modules\Cms\Models\MenuItem")
        ->where('menu_permission.model_id',$menu) 
        ->select(["menu_permission.permission_id"])
        ->get(); 

       
        if(count($menu)>0){
            $permission = DB::table('model_has_roles as usuario_cargo')
            ->Join("role_has_permissions as rhp","rhp.role_id","=","usuario_cargo.role_id")
            ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
            ->where('usuario_cargo.model_id', auth()->user()->id)
            ->where('rhp.permission_id', $menu[0]->permission_id)
            ->select(["rhp.permission_id"])
            ->get(); 
            
            if(count($permission)>0){
                return 1;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
         
        
}


    public static function fr_menu2($id){
        $submenu = DB::table('menus')
        ->leftJoin('menu_items as m_i', 'm_i.menus_id', '=', 'menus.id')
        ->leftJoin('menu_item_translations as mit', 'mit.menu_items_id', '=', 'm_i.id')
        ->where("mit.active","1")
        ->where("mit.language_id","1")
        ->where("m_i.menus_id",$id)
        // ->where("m_i.external_link",null) 
        ->whereNull("menus.deleted_at")
        ->whereNull("m_i.deleted_at")
        ->whereNull("mit.deleted_at")
        ->select([  
            "menus.id",
            "menus.code",
            "menus.order",
            "m_i.id as parent",
            "m_i.external_link",
            "m_i.id as menu_item",
            "m_i.external_link",
            "mit.display_name",
        ])
        
        ->get();
            
    $submenu = [
        "count"=>count($submenu),
        "submenu"=>$submenu
    ];
    return $submenu; 
    }
    public static function fr_menu3($id){
        $submenu = DB::table('menu_items as m_i')
        ->leftJoin('menu_item_translations as mit', 'mit.menu_items_id', '=', 'm_i.id')
        ->where("mit.active","1")
        ->where("mit.language_id","1")
        ->where("m_i.parent_id",$id) 
        ->where("m_i.external_link",null) 
        ->whereNotNull("m_i.menus_id")
        ->whereNull("m_i.deleted_at")
        ->whereNull("mit.deleted_at")
        ->select([ 
            "m_i.id as parent",
            "m_i.parent_id",
            "m_i.external_link",
            "m_i.position",
            "mit.display_name",
        ])
        ->orderBy("m_i.position")
        ->get();
            
    $submenu = [
        "count"=>count($submenu),
        "submenu"=>$submenu
    ];
    return $submenu; 
    }

    public static function fr_menu4($id){
        $submenu = DB::table('menu_items as m_i')
        ->leftJoin('menu_item_translations as mit', 'mit.menu_items_id', '=', 'm_i.id')
        ->where("mit.active","1")
        ->where("mit.language_id","1")
        ->where("m_i.parent_id",$id) 
        ->whereNotNull("m_i.external_link") 
        ->whereNull("m_i.deleted_at")
        ->whereNull("mit.deleted_at")
        ->select([ 
            "m_i.id as parent",
            "m_i.external_link",
            
            "m_i.position",
            "mit.display_name",
        ])
        ->orderBy("m_i.position")
        ->get(); 
            
    $submenu = [
        "count"=>count($submenu),
        "submenu"=>$submenu
    ];
    return $submenu; 
    }
 

}
