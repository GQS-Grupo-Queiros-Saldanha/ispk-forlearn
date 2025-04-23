<?php

namespace App\Modules\Users\Controllers;

use App\Helpers\LanguageHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\Classes;
use App\Modules\GA\Models\Course;
use App\Modules\GA\Models\StudyPlan;
use App\Modules\Payments\Models\Article;
use App\Modules\Payments\Models\DisciplineArticle;
use App\Modules\Users\Models\Matriculation;
use App\Modules\Users\Models\UserCandidate;
use App\Modules\Users\Models\User;
use App\Modules\Users\Models\UserState;
use App\Modules\Users\Models\UserStateHistoric;
use App\Modules\Users\Requests\MatriculationRequest;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Throwable;
use App\Modules\GA\Models\LectiveYear;
use Illuminate\Support\Facades\Auth;
use App\Model\Institution;
use App\Modules\Users\Enum\ParameterEnum;
use App\Modules\Users\Enum\RoleEnum;


class ConfigMatriculationStrategyController extends Controller
{

    public function index()
    {
        try {

            //Pegar ano lectivo corrente.
            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();

            $currentData = Carbon::now();

            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();

            return view("Users::config-matriculation-strategy.index", compact('lectiveYears', 'lectiveYearSelected'));
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    public function getStrategyMatriculationAjax(Request $request)
    {

        try {

            $strategy = DB::table('matriculation_strategy_config as mtsc')
                ->join('users as u1', 'u1.id', '=', 'mtsc.created_by')
                ->leftJoin('user_parameters as u_p', function ($join) {
                    $join->on('u1.id', '=', 'u_p.users_id')
                        ->where('u_p.parameters_id', ParameterEnum::NOME);
                })
                ->select([
                    'mtsc.id',
                    'mtsc.institution',
                    'mtsc.status',
                    'mtsc.description',
                    'mtsc.create_at',
                    'mtsc.updated_at',
                    'u_p.value as created_by'
                ])
                ->get();



            return Datatables::of($strategy)
                ->addColumn('actions', function ($item) {
                    // return $status = $item->status == 0 ? "<button class=\"btn btn-success\">Activar</button></h1>" : " ";
                    return view('Users::config-matriculation-strategy.Datatable.btnAction')->with('item', $item);
                })
                ->addColumn('status_color', function ($item) {
                    return $status = $item->status == 1 ? "<span class=\"badge bg-success\">Activo</span></h1>" : " <span class=\"badge bg-danger\">Inactivo</span></h1>";
                })
                ->rawColumns(['actions', 'status_color'])
                ->addIndexColumn()
                ->toJson();
        } catch (Exception $e) {

            return response()->json($e->getMessage(), 500);
        }
    }



    public function edit($id)
    {

        try {

            return "admin";
            // return view('Users::equivalence.discipline_student_equivalence')->with($data);
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }





    public function create(LectiveYear $lective_year)
    {

        try {
        } catch (Exception | Throwable $e) {

            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }




    public function store(Request $request)
    {
        // discipline_delay
        $count = ["_first", "_second", "_thirth", "_fourth", "_fifth"];
        $dados = [];

      
        foreach ($count as $key => $value) {
            $year = $key + 1;
            $discipline = "discipline_delay" . $value;
            $statusPrecedency = "precedency" . $value;

            

            $store = DB::table('matriculation_aprove_roles_config')
                ->updateOrInsert(
                    [
                        'currular_year' =>  $year,
                        'id_lective' => $request->LectiveYear
                    ],
                    [
                        'discipline_in_delay' => $request[$discipline],
                        'precedence' => $request[$statusPrecedency],
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]
                );
        }


        return 1;
        Toastr::success(__('A forLEARN '), __('toastr.success'));
    }


    public function update($id)
    {
    }

    public function delete($id)
    {
        return $id;
    }



    public function numDisciplinasSelected(Request $request)
    {
        try {


            $numDisplineDelay = DB::table('matriculation_aprove_roles_config as marc')
                ->join('users as u1', 'u1.id', '=', 'marc.created_by')
                ->leftJoin('user_parameters as u_p', function ($join) {
                    $join->on('u1.id', '=', 'u_p.users_id')
                        ->where('u_p.parameters_id', ParameterEnum::NOME);
                })
                ->where('marc.id_lective', $request->value)
                ->select([
                    'marc.currular_year',
                    'marc.discipline_in_delay',
                    'marc.precedence',
                    'u_p.value as created_by'
                ])
                ->get();

            return response()->json(['data' => $numDisplineDelay]);
        } catch (Exception $e) {

            return response()->json($e->getMessage(), 500);
        }
    }


    public function activar($id)
    {
        try {
            //code...
            DB::table("matriculation_strategy_config")
                ->update([
                    "status" => 0
                ]);

            DB::table("matriculation_strategy_config")
                ->where("id", "=", $id)
                ->update([
                    "status" => 1,
                    "updated_by" => Auth::user()->id,
                    "created_by" => Auth::user()->id,
                ]);


                $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();

            $currentData = Carbon::now();

            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();

            return view("Users::config-matriculation-strategy.index", compact('lectiveYears', 'lectiveYearSelected'));

            Toastr::success(__('Falta Actualizada com sucesso'), __('toastr.success'));
        } catch (\Throwable $th) {
           
            Toastr::error("Não foi possível realizar acção", __('toastr.error'));
        }


        
    }
}
