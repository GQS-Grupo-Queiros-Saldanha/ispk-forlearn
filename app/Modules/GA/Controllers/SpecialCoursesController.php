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

       
        return view('GA::special-courses.index');
    }

    public function show($id){

        $data = DB::table('special_courses')
        ->find($id);

     

    return view('GA::special-courses.form', 
        ['id' => $id, 'action'=>'show','data'=>$data]);
    }


    public function edit($id){

        $data = DB::table('special_courses')
                    ->find($id);

        return view('GA::special-courses.form', 
        ['id' => $id, 'action'=>'edit','data'=>$data]);
    }

    public function create() {
        $create = 'create';
    return view('GA::special-courses.form')->with('action', $create);
    }

    public function ajax() {

        try{

          $model= DB::table('special_courses')
                ->leftJoin('users as u1','u1.id','special_courses.created_by')
                ->leftJoin('users as u2','u2.id','special_courses.updated_by')
                ->whereNull('special_courses.deleted_at')
                ->whereNull('special_courses.deleted_by')
                ->select(['u1.name','u2.name','special_courses.*'])
                ->get();

           
        
            return DataTables::of($model)
            ->addColumn('actions', function ($item) {
                 return view('GA::special-courses.datatables.actions')->with('item', $item);
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
                    'display_name' => $request->get('display_name'),
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
                           
                            'display_name' => $request->get('display_name'),
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
            $exists = DB::table('special_course_edition')
            ->where('special_course_id',$id)
            ->whereNull('deleted_by')
            ->whereNull('deleted_at')
            ->exists();
         
          if($exists){
           
              Toastr::warning(__('O curso possui edições associadas!'), __('toastr.warning'));
              return redirect()->back();
          }
    
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