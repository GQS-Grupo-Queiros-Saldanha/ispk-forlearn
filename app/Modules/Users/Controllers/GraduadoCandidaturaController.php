<?php

namespace App\Modules\Users\Controllers;

use DB;
use Auth;
use Toastr;
use Exception;
use DataTables;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Modules\Users\Models\User;
use App\Modules\GA\Models\LectiveYear;
use App\Modules\Users\Models\UserCandidate;
use App\Modules\Users\Models\UserParameter;
use App\Modules\Users\util\FaseCandidaturaUtil;
use App\Modules\Users\util\GraduadoCandidaturaUtil;

class GraduadoCandidaturaController
{
    private $userController;

    function __construct()
    {
        $this->userController = new UsersController();
    }

    public function index(Request $request)
    {
        $lectiveYears = LectiveYear::with(['currentTranslation'])->get();
        $faseActual = FaseCandidaturaUtil::faseActual();
        $lectiveYearSelected = $faseActual->id_years ?? 6;
        return view('Users::candidate.graduado.index', compact('lectiveYears', 'lectiveYearSelected'));
    }

    public function ajax_graduado(Request $request)
    {
        $graduados = GraduadoCandidaturaUtil::graduado($request->lective_year ?? null);
        return Datatables::of($graduados)
            ->addColumn('actions', function ($item) {
                return view('Users::candidate.graduado.datatables.actions')->with('item', $item);
            })
            ->rawColumns(['actions'])
            ->addIndexColumn()
            ->toJson();
    }

    public function copy_graduado($id)
    {
        try {

            $user = User::with(['user_parameters' => function ($query) {
                $query->whereNotIn('parameters_id', [19]);
            }])->find($id);

            if (isset($user->is_duplicate) && $user->is_duplicate) {
                Toastr::warning("Utilizador não pode ser duplicado", __('toastr.warning'));
                return back();
            }

            $faseActual = FaseCandidaturaUtil::faseActual();

            if (!isset($faseActual->id)) {
                Toastr::warning("A forLEARN detectou que a data actual não faz parte das fases da candidatura", __('toastr.warning'));
                return back();
            }

            $join = str_replace(" ", ",", $user->name);
            $json = $this->userController->convertToEmail($join);
            $data = $json->getData();
            $email = $data->email;

            if (User::where("email", $email)->exists()) {
                Toastr::warning("Erro na criação do email", __('toastr.warning'));
                return back();
            }

            $person = User::create([
                'name' => $user->name,
                'email' => $email,
                'image' => $user->image,
                'password' => $user->password,
                'duplicate' => $user->id,
                'is_duplicate' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => Auth::user()->id,
            ]);

            $person->syncRoles(15);

            $parameters = $user->user_parameters->map(function ($q) use ($person) {
                $q->update(["is_duplicate" => 1]);
                $q->created_at = Carbon::now();
                $q->updated_at = Carbon::now();
                $q->created_by = Auth::user()->id;
                $q->users_id = $person->id;
                $q->is_duplicate = 0;
                unset($q->id);
                return $q;
            })->all();

            foreach ($parameters as $parameter) {
                UserParameter::create($parameter->toArray());
            }

            $nextCode = GraduadoCandidaturaUtil::generatorCode();

            UserCandidate::create([
                "user_id" => $person->id,
                "code" => $nextCode,
                "year" => $faseActual->id_years,
                "year_fase_id" => $faseActual->id,
                "created_by" => Auth::user()->id,
                'updated_by' => Auth::user()->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $user->update(["is_duplicate" => 1]);

            return redirect()->route('candidates.edit', $person->id);
        } catch (Exception $e) {
            Toastr::error("Erro na execução desta operação", __('toastr.error'));
            return back();
        }
    }
}
