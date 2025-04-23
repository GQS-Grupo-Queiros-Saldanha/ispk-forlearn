<?php

namespace App\Modules\Users\Controllers;

use App\Helpers\LanguageHelper;
use App\Http\Controllers\Controller;
use App\Modules\GA\Models\Course;
use App\Modules\Users\Requests\MatriculationRequest;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Throwable;
use App\Modules\GA\Models\LectiveYear;
use App\Modules\GA\Models\Discipline;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class Equivalence2Controller extends Controller
{


    public function courses_change_store(Request $request)
    {

        try {

            $c_first = $request->course;
            $c_second = $request->courseNew;
            $Descricao = $request->Descricao;
            $Ano = $request->anoLective;
            $estado = $request->estado ?? 0;


            DB::transaction(function () use ($c_first, $c_second, $Ano, $Descricao, $estado) {
                DB::table('tb_courses_change')
                    ->updateOrInsert(
                        [
                            'course_id_primary' =>  $c_first,
                            'course_id_secundary' => $c_second,
                            'lective_year_id' => $Ano,
                        ],

                        [
                            'description' => $Descricao,
                            'status' => $estado,
                            'created_by' => Auth::user()->id,
                            'updated_by' => Auth::user()->id,

                        ]
                    );
            });

            Toastr::success(__('Associação foi criada com sucesso.'), __('toastr.success'));
            return redirect()->back();
        } catch (Exception | Throwable $e) {

            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    public function studentchangeCourse()
    {
        try {

            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();

            $courses = Course::with([
                'currentTranslation'
            ])->get();

            $currentData = Carbon::now();

            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();
            $data = [
                'action' => 'create',
                'courses' => $courses,
                'lectiveYears' => $lectiveYears,
                'lectiveYearSelected' => $lectiveYearSelected->id
            ];

            return view('Users::equivalence.chance-course.student')->with($data);
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function disciplinasChangeCourseDel(Request $request, $id){
        try{
            DB::delete('delete from tb_equivalence where id = ? and id_change_course=? limit 1', [$request->id_tb_equivalence, $id]);
            return redirect()->route('change.courses.disciplina.list',$id)->with('success','operação realizado com successo.');
        }catch(Exception $error){
            dd($error);
            return redirect()->route('change.courses.disciplina.list',$id)->with('error','não foi possível apagar.');
        }
    }

    public function disciplinasChangeCourse($id)
    {
        try {

            $tb_courses_change = DB::table('tb_courses_change as tcc')
                ->join('courses_translations as ct', 'ct.courses_id', "=", 'tcc.course_id_primary')
                ->join('courses_translations as ct1', 'ct1.courses_id', "=", 'tcc.course_id_secundary')
                ->where('tcc.id','=',$id)
                ->where('ct.active', 1)->where('ct1.active', 1)
                ->select(
                    'tcc.id',                 
                    'ct.display_name as curso_velho',
                    'ct1.display_name as curso_novo'
                )->first();

            return view('Users::equivalence.chance-course.list_disciplina',compact('tb_courses_change'));
            
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    function disciplinesSelect(Request $request)
    {
        $tb_courses_change = DB::table('tb_courses_change')->find($request->id_eq);
        $disciplines = Discipline::with([
            'currentTranslation'
        ]);

        $courses = [$tb_courses_change->course_id_primary, $tb_courses_change->course_id_secundary];

        // if ($teacher) {
        //     $disciplines = $disciplines->whereIn('id', $teacher->disciplines()->pluck('id')->all());
        // }

        if (!empty($courses)) {
            if ($request->return_type == "course_id_primary")
                $disciplines = $disciplines->where('courses_id', '=', $tb_courses_change->course_id_primary);
            if ($request->return_type == "course_id_secundary")
                $disciplines = $disciplines->where('courses_id', '=', $tb_courses_change->course_id_secundary);
        }

        $disciplines = $disciplines->get();

        $array = [];
        foreach ($disciplines as $disc) {
            $array[] = (object)[
                "id" =>  $disc->currentTranslation->id,
                "discipline_id" => $disc->currentTranslation->discipline_id,
                "display_name" => $disc->currentTranslation->display_name,
                "code" =>$disc->code
            ];
        }

        return response()->json($array);
        /*return $disciplines->map(function ($discipline) {
            return [
                'id' => $discipline->id,
                'display_name' => "#$discipline->code - " . $discipline->translation
            ];
        });*/
    }

    public function studentChangeCourseAjax($lective_year)
    {
        try {

            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();
            // return $lective_year;

            $dados = DB::table('tb_user_change_course as tucc')
                ->join('users as u', 'u.id', '=', 'tucc.user_id')
                ->join('courses_translations as ct', 'ct.courses_id', "=", 'tucc.old_course_id')
                ->join('courses_translations as ct1', 'ct1.courses_id', "=", 'tucc.new_course_id')
                ->join('matriculations as m', 'm.user_id', "=", "tucc.user_id")
                ->where('m.lective_year', '=', $lective_year)
                ->where('ct.active', 1)->where('ct1.active', 1)
                ->whereNull('ct.deleted_at')->whereNull('ct1.deleted_at')->whereNull('m.deleted_at')
                ->whereNull('m.deleted_by')
                //->orderBy('m.created_at','desc')
                ->select(
                    'u.name as name',
                    'ct.display_name as curso_velho',
                    'ct1.display_name as curso_novo',
                    'tucc.old_course_id as id_curso_velho',
                    'tucc.new_course_id as id_curso_novo',
                    'tucc.id as id',
                    'm.id as id_matricula',
                    'm.lective_year as ano_lectivo'
                )
                ->get();

            return Datatables::of($dados)
                ->addColumn('actions', function ($item) {
                    return view('Users::equivalence.datatables.changeactions')->with('item', $item);
                })
                ->rawColumns(['actions'])
                ->addIndexColumn()
                ->toJson();
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function disciplinasChangeCourseAjax($course_change_id){
        try {

            $dados = DB::table('tb_equivalence as te')
                ->join('disciplines_translations as dt1','dt1.discipline_id','=','te.first_discipline_course')
                ->join('disciplines_translations as dt2','dt2.discipline_id','=','te.second_discipline_course')
                ->where('dt1.active','=',1)->where('dt2.active','=',1)
                ->where('te.id_change_course','=',$course_change_id)
                ->select(
                    'te.*',
                    'dt2.display_name as disciplina_first',
                    'dt1.display_name as disciplina_second',
                )->get();
                    
            return Datatables::of($dados)
                ->addColumn('actions', function ($item) {
                    return view('Users::equivalence.datatables.list_disciplina_actions')->with('item', $item);
                })
                ->rawColumns(['actions'])
                ->addIndexColumn()
                ->toJson();
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }        
    }

    public function changeDisciplinaStoreAjax(Request $request)
    {

        if (isset($request->items)) {
            $contador = 0; 
            $insert = 0;
            $tam = sizeof($request->items);
            try {
                $auth = Auth::user()->id;
                foreach ($request->items as $item) {
                    $status = DB::insert(
                        'INSERT INTO tb_equivalence(id_change_course, first_discipline_course, second_discipline_course, created_by, deleted_by) VALUES (?,?,?,?,?)',
                        [$item["id_change_course"], $item["first_discipline_course"], $item["second_discipline_course"], $auth, $auth]
                    );
                    if ($status) $contador++;
                }
                $insert = $tam == $contador ? $tam : $tam - $contador;
                $obj = (object)["status" => true, "size_items" => $tam, "size_insert" => $insert, "total" => "yes"];
                return response()->json($obj);
            } catch (QueryException $error) {
                $obj = (object)["status" => false, "size_items" => $tam, "size_insert" => $insert > 0 ? $insert : 0, "total" => "parcial"];
                return response()->json($obj);
            }
        }
        $obj = (object)["status" => false, "size_items" => 0, "size_insert" => 0, "total" => "no"];
        return response()->json($obj);
    }


    public function courseNamejax($id)
    {
        try {
            $analisar = DB::table('courses_translations')
                ->where('courses_id', '=', $id)
                ->where('active', '=', 1)
                ->select('display_name')->first();
            $obj = (object)["display_name" => $analisar->display_name];
            return response()->json($obj);
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    public function ajaxUserData($studentId)
    {
        try {





            // $view = view("Users::confirmations-matriculations.disciplines")->with($data)->render();

            return response()->json(array('html' => $view));
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }







    public function store(MatriculationRequest $request)
    {
    }


    public function update($id)
    {
    }
}
