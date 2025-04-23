<?php

namespace App\Modules\GA\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use DB;
use Illuminate\Http\Request;
use Toastr;
use Carbon\Carbon;
use Log;
use Exception;

class SpecialCoursesController extends Controller {

    public function index() {

       
        return view('Users::special-courses.index');
    }

    public function show($id){

        $data = DB::table('special_courses')
        ->find($id);

     

    return view('Users::special-courses.form', 
        ['id' => $id, 'action'=>'show','data'=>$data]);
    }


    public function edit($id){

        $data = DB::table('special_courses')
                    ->find($id);

        return view('Users::special-courses.form', 
        ['id' => $id, 'action'=>'edit','data'=>$data]);
    }

    public function create() {
        $create = 'create';
    return view('Users::special-courses.form')->with('action', $create);
    }

    public function ajax() {

        try{

          $model= DB::table('special_courses')
                ->whereNull('deleted_at')
                ->whereNull('deleted_by')
                ->get();

            foreach($model as $item)
            { 

                // pegando o name do usuário que criou
                $data = DB::table('special_courses')
                    ->join('users','special_courses.created_by','=','users.id' )
                    ->select('users.name')
                    ->where('users.id', $item->created_by)
                    ->first();

                    $item->created_by = $data->name;

                // pegando o name do usuário que actualizou
                if($item->updated_by != null )
                {
                    
                $data = DB::table('special_courses')
                ->join('users','special_courses.updated_by','=','users.id' )
                ->select('users.name')
                ->where('users.id', $item->updated_by)
                ->first();

                $item->updated_by = $data->name;
               }
    
            }

            return DataTables::of($model)
            ->addColumn('actions', function ($item) {
                 return view('Users::special-courses.datatables.actions')->with('item', $item);
                         })
             ->addIndexColumn()   
            ->rawColumns(['actions'])
            ->toJson();
         
        }
        catch(Exception | Throwable $e)
        {
            dd($e);
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }

    
    }

       /**
     * Store a new blog post.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request){

        try{
         
            DB::table('special_courses')
                ->insert(
                    [
                    'name' => $request->get('name'),
                    'description' => $request->get('description'),
                    'code' => $request->get('code'),
                    'created_by' => auth()->user()->id
                ]);
    
                
    
              // Success message
              Toastr::success(__('Criado com sucesso'), __('toastr.success'));
              return redirect()->route('special-courses.index');
        }
         catch (Exception | Throwable $e) {
          
           //Toastr::error($e->getMessage(), __('toastr.error'));
            Log::error($e);
           return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    
    
       
    }

    public function update($id, Request $request){
        try{
             DB::table('special_courses')
                        ->where('id', $id)
                        ->update([
                           
                            'name' => $request->get('name'),
                            'description' => $request->get('description'),
                            'code' => $request->get('code'),
                            'updated_by' => auth()->user()->id,
                            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                        ]); 

            Toastr::success(__('Actualizado com sucesso'), __('toastr.success'));
            return redirect()->route('special-courses.index');

        }
        catch(Exception $e){
            Log::error($e);
           return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
    }

    }


    public function destroy($id) {

        try{
    
    
            DB::table('special_courses')
            ->where('id', $id)
            ->update(
              [
                'deleted_by' => auth()->user()->id,
                'deleted_at' => Carbon::now()->format('Y-m-d H:i')
              ]);
    
          // Success message
          Toastr::success('Success');
          return redirect()->route('special-courses.index');
       
      }
     catch (Exception | Throwable $e) {
      //  dd($e);
        Toastr::error($e->getMessage(), __('toastr.error'));
        Log::error($e);
        return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
       }
    }





}