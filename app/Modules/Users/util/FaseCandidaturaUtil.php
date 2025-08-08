<?php

namespace App\Modules\Users\util;


use App\Modules\Users\Models\UserCandidate;
use App\Modules\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Toastr;
use Auth;


class FaseCandidaturaUtil
{

    public static function negativeInFase($user_id, $fase_id){
        $db = DB::table('exame_candidates_status')->where([
            'user_id' => $user_id, 'fase_id' => $fase_id
        ])->first();
        if(!isset($db->id)) return true;
        return $db->status == 0;
    }

    public static function faseActual() {
        $currentDate = Carbon::now()->format('Y-m-d'); // Apenas data (sem hora)
        return DB::table('lective_candidate_calendarie')
            ->where('data_inicio', '<=', $currentDate)
            ->where('data_fim', '>=', $currentDate)
            ->first();
    }

    public static function userCandidate($user_id){
        $fase = FaseCandidaturaUtil::faseActual();
        $userCandidateLastYear = UserCandidate::where([
            ['user_id', "=", $user_id],
            ['year', "=", $fase->id_years ?? 0],
        ])->orderBy('id')->first();
        return $userCandidateLastYear;
     }

    public static function existUserInHistorico($user_id,$fase_id){
        $historico = DB::table('lective_candidate_historico_fase as lchf')
                        ->join('historic_classe_candidate as hcc','lchf.id','=','hcc.id_historic_user_candidate')
                        ->where('lchf.user_id',$user_id)
                        ->first();
        return isset($historico->id);
    }

    public static function existTwoUserCources($user_id){
        $courses_default = DB::table('courses_default')->where('users_id',$user_id)->first();

        if(isset($courses_default->id)) return false;

        $user = User::with('courses')->whereId($user_id)->first();
        $tam = sizeof($user->courses);
        return $tam > 1;
    }    

    public static function existUserInFaseNext($user_id, $fase_id): bool
    {
        $matricula = DB::table('matriculations')->where('user_id', $user_id)->first();
        if (isset($matricula->id)) return true;
        $currentData = Carbon::now();

        $faseActual = DB::table('lective_candidate')->find($fase_id);
        $faseLast = DB::table('lective_candidate')
                        ->where('id_years',$faseActual->id_years)
                        ->orderBy('id','DESC')
                        ->first();

        /* se é a última fase */
        //if($faseActual->id == $faseLast->id) return true;
       
        $faseNext = FaseCandidaturaUtil::faseActual();

        if (!isset($faseNext->id)) return false;

        $userCandidate = DB::table('user_candidate')->where('user_id', $user_id)->where('year_fase_id', $faseNext->id)->first();
        return isset($userCandidate->user_id);
    }

    public static function verifyUserCandidaturaAnoPassado($id): bool
    {

        $matricula = DB::table('matriculations')->where('user_id', $id)->first();
        if (isset($matricula->id)) return false;

        $userCandidate = DB::table('user_candidate as uc')
            ->where('uc.user_id', $id)->first();

        if (!isset($userCandidate->created_at)) return false;
        $userLective = FaseCandidaturaController::calculateLective($userCandidate->created_at);

        $lectiveActual = FaseCandidaturaController::calculateLective(Carbon::now());
        if (!isset($lectiveActual->created_at)) return false;

        $data_start_user = strtotime($userLective->start_date);
        $data_start_actual = strtotime($lectiveActual->start_date);

        //dd($userLective ,$lectiveActual);

        if ($data_start_user < $data_start_actual) {
            $year_user = date('Y', $data_start_user);
            $year_actual = date('Y', $data_start_actual);
            //dd($year_user ,$year_actual);
            return $year_actual - 1 == $year_user;
        }

        return false;
    }

    public static function tudoEstaPago($cursos, $state)
    {
        $contadorUser = 0;
        $contadorTotal = 0;
        foreach ($cursos as $item)
            if ($item->usuario_id == $state->id) {
                $contadorUser++;
                if ($item->state == 'total')
                    $contadorTotal++;
            }
        return    $contadorUser == $contadorTotal && $contadorTotal > 1 ? 'yes' : 'no';
    }

    public static function temCursoHistoricoGet($id_fase, $user_id)
    {
        $coursesHistorico = DB::table('lective_candidate_historico_fase as lchf')
            ->join('courses_translations as ct', 'lchf.id_curso', '=', 'ct.courses_id')
            ->where('lchf.id_fase', $id_fase)
            ->where('lchf.user_id', $user_id)
            ->where('ct.active', 1)
            ->select('lchf.*', 'ct.display_name as curso')
            ->get();

        if (sizeof($coursesHistorico) == 0) return "no";

        $value = "";
        foreach ($coursesHistorico as $historico)
            $value .= $value == "" ? $historico->curso : "," . $historico->curso;

        return $value;
    }

    public function validateExist($lectiveCandidate, $msg1)
    {
        if (!isset($lectiveCandidate->id)) {
            Toastr::warning(_($msg1), __('toastr.warning'));
            return redirect()->back();
        }
    }

    public function validateExistIsTermina($lectiveCandidate, $msg1, $msg2)
    {

        $this->validateExist($lectiveCandidate, $msg1);

        if (isset($lectiveCandidate->is_termina) && $lectiveCandidate->is_termina) {
            Toastr::warning(_($msg2), __('toastr.warning'));
            return redirect()->back();
        }
    }

    public function validRequest(Request $request)
    {

        $data_start = strtotime($request->data_start);
        $data_end = strtotime($request->data_end);

        if ($data_start > $data_end) {
            Toastr::warning(_('Conflito entre as datas: a data de inicio não pode ser maior que a data de termino, por favor verifica o intervalo entre as datas'), __('toastr.warning'));
            return redirect()->route('fase-candidatura');
        }

        $obj = DB::table('lective_years')->where('id', $request->lective_year)->select('id', 'start_date', 'end_date')->first();

        $data_start = strtotime($obj->start_date);
        $data_end = strtotime($obj->end_date);

        if ($data_start > $data_end) {
            Toastr::warning(_('Ano lectivo se encontra encerrado.'), __('toastr.warning'));
            return redirect()->route('fase-candidatura');
        }
    }

    public function getClassAndCourse($user_id)
    {
        return   DB::table('user_classes as uc')
            ->join('classes as c', 'c.id', '=', 'uc.class_id')
            ->where('uc.user_id', $user_id)
            ->select('c.courses_id as course_id', 'uc.class_id', 'uc.user_id')
            ->get();
    }

    public function insertInHistorico($user_id, $faseNextId, $id_curso, $id_turma)
    {
        return  DB::table('lective_candidate_historico_fase')->updateOrInsert([
            'user_id' => $user_id,
            'id_fase' => $faseNextId,
            'id_curso' => $id_curso,
            'id_turma' => $id_turma
        ], [
            'user_id' => $user_id,
            'id_fase' => $faseNextId,
            'id_curso' => $id_curso,
            'id_turma' => $id_turma,
            'created_by' => Auth::user()->id,
            'created_at' => Carbon::now(),
            'updated_by' => Auth::user()->id,
            'updated_at' => Carbon::now()
        ]);
    }

    public function nextCode()
    {
        $latestsCandidate = UserCandidate::latest()->first();
        if ($latestsCandidate && Carbon::parse($latestsCandidate->created_at)->year === Carbon::now()->year) {
            $nextCode = 'CE' . ((int)ltrim($latestsCandidate->code, 'CE') + 1);
        } else {
            $nextCode = 'CE' . Carbon::now()->format('Y') . '0001';
        }
        $Verificar = DB::table('user_candidate')->where('code', $nextCode)->get();
        if (count($Verificar) > 0) {
            Toastr::error("Atenção ! não foi possivível prosseguir com a candidatura porque no momento de gerar o número automático de candidato houve um conflito com um registo já existente, tente novamente, no caso de persitir o erro contacte o Apoio a forLEARN. code: " . $nextCode, __('toastr.error'));
            return back();
        }
        return $nextCode;
    }

    public function createUserCandidate($nextCode, $user_id, $lectiveCandidate, $lectiveCandidateNext)
    {
        $userCandidate = UserCandidate::where([
            ['user_id', "=", $user_id],
            ['year', "=", $lectiveCandidateNext->id_years],
            ['year_fase_id', "=", $lectiveCandidateNext->id],
        ])->first();

        if (!isset($userCandidate->user_id)) {
            $userCandidate = UserCandidate::create([
                'user_id' => $user_id,
                'code' => $nextCode,
                'year' => $lectiveCandidateNext->id_years,
                'year_fase_id' => $lectiveCandidateNext->id,
                'created_at' => Carbon::now(),
                'updated_by' => Auth::user()->id,
                'updated_at'=> Carbon::now()
            ]);

            $userCandidateLastYear = UserCandidate::where([
                ['user_id', "=", $user_id],
                ['year_fase_id', "=", $lectiveCandidate->id],
            ])->first();

            $candidato = User::with('classes')->whereId($user_id)->first();

            $candidatoHistorico = DB::table('lective_candidate_historico_fase')->where([
                'id_fase' => $lectiveCandidate->id, 'user_id' => $user_id
            ])->first(); 

            
            if(!isset($candidatoHistorico->id)){
                $auth = Auth::user()->id;
                $current = Carbon::now();
                $id = DB::table('lective_candidate_historico_fase')->insertGetId([
                    'id_fase' => $lectiveCandidate->id,
                    'user_id' => $user_id,
                    'updated_by' => $auth,
                    'created_by' => $auth,
                    'updated_at' => $current,
                    'created_at' => $current
                ]);
            }else{
                $id = $candidatoHistorico->id;
            }

            foreach($candidato->classes as $class)
                DB::table('historic_classe_candidate')->updateOrInsert(
                    ['id_historic_user_candidate'=>$id,'id_classe'=>$class->id],
                    ['id_historic_user_candidate'=>$id,'id_classe'=>$class->id]
                );
            
        }

        return  $userCandidate;
    }

}
