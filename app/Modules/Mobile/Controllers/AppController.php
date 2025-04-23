<?php

namespace App\Modules\Mobile\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\GA\Models\Course;
use App\Modules\GA\Models\Discipline;
use App\Modules\Mobile\Models\Grade;
use App\Modules\Mobile\Requests\GradeRequest;
use App\Modules\Users\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;
use Carbon\Carbon;
use Toastr;
use Yajra\DataTables\Facades\DataTables;
use PDF;
use Auth;
use App\Modules\Users\Models\ParameterGroup;
use App\Modules\GA\Models\LectiveYear;

use App\Exports\CandidateExport;
use Maatwebsite\Excel\Facades\Excel;

class AppController extends Controller

{

    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $courses = Course::with([
            'currentTranslation'
        ])
            ->where('id', '!=', 22)
            ->where('id', '!=', 18);

        $lectiveYears = LectiveYear::with(['currentTranslation'])
            ->get();

        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
            ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
            ->first();
        $lectiveYearSelected = $lectiveYearSelected->id ?? 6;

        $data = [
            'courses' => $courses->get(),
            'lectiveYearSelected' => $lectiveYearSelected,
            'lectiveYears' => $lectiveYears,
            'url' => "$_SERVER[HTTP_HOST]"
        ];
        return view("Mobile::index")->with($data);
    }




    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response




     */

    public function menu($id)
    {

        try {
            //id do usuario
            $id_user = base64_decode($id);
            $count = count_notificationApp($id_user);

            $data = [
                'Menu' => "todos",
                'url' => "$_SERVER[HTTP_HOST]",
                'notify' => count($count)
            ];

            return view("Mobile::menu")->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            Toastr::error(__('Grades::grades.store_error_message'), __('toastr.error'));
            return response()->json(['error' => $e], 500);
        }
    }





    public function notification($id)
    {

        try {
            //id do usuario
            $id_user = base64_decode($id);
            $notification = all_notificationApp($id_user);

            $data = [
                'Menu' => "todos",
                'url' => "$_SERVER[HTTP_HOST]",
                'notification' => $notification
            ];

            return view("Mobile::notificacao.notificacao")->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            Toastr::error(__('Grades::grades.store_error_message'), __('toastr.error'));
            return response()->json(['error' => $e], 500);
        }
    }






    public function single_notification($id, $notify_id)
    {

        try {
            //id do usuario

            $id_user = base64_decode($id);

            $notification_single = DB::table('tb_notification')
                ->join('users', 'users.id', 'tb_notification.sent_by')
                ->leftJoin('user_parameters as fullname', function ($join) {
                    $join->on('users.id', '=', 'fullname.users_id')
                        ->where('fullname.parameters_id', 1);
                })
                ->select(['fullname.value as fullname', 'users.email', 'users.image', 'tb_notification.*'])
                ->where('tb_notification.id', $notify_id)
                ->where('tb_notification.sent_to', $id_user)
                ->first();
            //marcar como lida

            $let = DB::table('tb_notification')
                ->where('id', $notify_id)
                ->where('sent_to', $id_user)
                ->update(['state_read' => 1]);


            $data = [
                'Menu' => "todos",
                'url' => "$_SERVER[HTTP_HOST]",
                'notification' => $notification_single
            ];

            return view("Mobile::notificacao.singleNotification")->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            Toastr::error(__('Grades::grades.store_error_message'), __('toastr.error'));
            return response()->json(['error' => $e], 500);
        }
    }



 




    public function perfil()
    {

        try {

            $data = [
                'Menu' => "todos",
                'url' => "$_SERVER[HTTP_HOST]"
            ];


            return view("Mobile::perfil")->with($data);

            return redirect()->back();
        } catch (Exception | Throwable $e) {
            Log::error($e);
            Toastr::error(__('Grades::grades.store_error_message'), __('toastr.error'));
            return response()->json(['error' => $e], 500);
        }
    }


    public function matricula($id)
    {

        try {

            //id do usuario
            $id_user = base64_decode($id);

            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();

            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();
            $lectiveYearSelected = $lectiveYearSelected->id ?? 6;

            $data = [

                'lectiveYearSelected' => $lectiveYearSelected,
                'lectiveYears' => $lectiveYears,
                'url' => "$_SERVER[HTTP_HOST]"
            ];



            return view("Mobile::matricula.matricula")->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            Toastr::error(__('Grades::grades.store_error_message'), __('toastr.error'));
            return response()->json(['error' => $e], 500);
        }
    }



    public function dadosMatricula($anoLectivo, $id)
    {

        try {

            //id do usuario
            $id_user = base64_decode($id);



            $lectiveYearSelected = DB::table('lective_years')
                ->where('id', $anoLectivo)
                ->first();

            //agrupar por ano academico 
            $matricula = DB::table('matriculations as mat')
                ->join('users as u', 'u.id', 'mat.user_id')
                // ->leftJoin('matriculation_classes as mc', 'mc.matriculation_id', '=', 'mat.id')
                // ->leftJoin('classes as turma', 'turma.id', '=', 'mc.class_id')
                ->leftJoin('matriculation_disciplines as mat_disc', 'mat_disc.matriculation_id', '=', 'mat.id')
                ->leftJoin('disciplines as d', 'd.id', '=', 'mat_disc.discipline_id')
                ->leftJoin('study_plan_edition_disciplines as edpt_disc', 'edpt_disc.discipline_id', '=', 'mat_disc.discipline_id')
                ->leftJoin('study_plan_editions as edpt', 'edpt.id', '=', 'edpt_disc.study_plan_edition_id')
                ->leftJoin('study_plans as stp', 'stp.id', '=', 'edpt.study_plans_id')
                ->leftJoin('disciplines_translations as dt', function ($join) {
                    $join->on('dt.discipline_id', '=', 'd.id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                })
                // ->where('mat.user_id',2730)
                ->where('mat.user_id', $id_user)
                ->where('edpt.lective_years_id', $anoLectivo)
                ->whereNull('mat.deleted_by')
                ->whereNull('mat.deleted_at')
                ->select(['mat.id as matricula_secret', 'mat.course_year', 'dt.display_name', 'd.code as codigo_discipline', 'stp.id as plano', 'edpt.course_year as ano_edicao', 'mat.code as matCodigo'])
                ->whereBetween('mat.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                ->orderBy('ano_edicao')
                ->distinct()
                ->get();
            //agrupar por ano curricular
            $group = collect($matricula)->groupBy('ano_edicao')
                ->filter(function ($item) {
                    return $item;
                });

            //Turma
            $turma = DB::table('matriculations as mat')
                ->join('users as u', 'u.id', 'mat.user_id')
                ->leftJoin('matriculation_classes as mc', 'mc.matriculation_id', '=', 'mat.id')
                ->leftJoin('classes as turma', 'turma.id', '=', 'mc.class_id')
                ->where('mat.user_id', $id_user)
                // ->where('mat.user_id',2730)
                ->select(['turma.display_name', 'turma.year as ano'])
                ->whereBetween('mat.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                ->distinct()
                ->get();

            return response()->json(["Disciplinas_matricula" => $matricula, "MatAnoLectivo_" . $anoLectivo => $matricula[0]->matCodigo ?? null, "studant_class" => $turma, "codigo_mat" => $matricula[0]->matCodigo ?? null]);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            Toastr::error(__('Grades::grades.store_error_message'), __('toastr.error'));
            return response()->json(['error' => $e], 500);
        }
    }







    public function propina()
    {

        try {

            //Pegar o ano lectivo na select
            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();

            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();

            $lectiveYearSelected = $lectiveYearSelected->id ?? 6;



            $data = [
                'Menu' => "todos",
                'url' => "$_SERVER[HTTP_HOST]",
                'lectiveYearSelected' => $lectiveYearSelected,
                'lectiveYears' => $lectiveYears,

            ];


            return view("Mobile::tesouraria.propina")->with($data);

            return redirect()->back();
        } catch (Exception | Throwable $e) {
            Log::error($e);
            Toastr::error(__('Grades::grades.store_error_message'), __('toastr.error'));
            return response()->json(['error' => $e], 500);
        }
    }











    public function avaliacao()
    {

        try {

            //Pegar o ano lectivo na select
            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();

            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();

            $lectiveYearSelected = $lectiveYearSelected->id ?? 6;



            $data = [
                'Menu' => "todos",
                'url' => "$_SERVER[HTTP_HOST]",
                'lectiveYearSelected' => $lectiveYearSelected,
                'lectiveYears' => $lectiveYears,
            ];


            return view("Mobile::avaliacao.avaliacao")->with($data);

            return redirect()->back();
        } catch (Exception | Throwable $e) {
            Log::error($e);
            Toastr::error(__('Grades::grades.store_error_message'), __('toastr.error'));
            return response()->json(['error' => $e], 500);
        }
    }


    public function detalhes($id)
    {

        try {



            $emolunento = DB::table('article_requests as artR')
                ->join('articles  as art', 'art.id', 'artR.article_id')
                ->leftJoin('article_translations as ct', function ($join) {
                    $join->on('ct.article_id', '=', 'art.id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })
                ->leftJoin('transaction_article_requests as TartR', 'TartR.article_request_id', 'artR.id')
                ->leftJoin('transactions as tr', 'tr.id', 'TartR.transaction_id')
                ->leftJoin('transaction_receipts as recibo', 'recibo.transaction_id', 'tr.id')


                ->leftJoin('disciplines as disc', 'disc.id', 'artR.discipline_id')
                ->leftJoin('disciplines_translations as dt', function ($join) {
                    $join->on('dt.discipline_id', '=', 'disc.id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                })
                ->select('ct.display_name as emolumento', 'artR.id as id_artiRequest', 'artR.base_value as valor_base', 'artR.month as mes', 'artR.status', 'artR.year', 'artR.discipline_id', 'dt.display_name as disciplina', 'disc.code as codigo', 'recibo.path', 'recibo.code as numero_recibo', 'recibo.id as id_redibo', 'recibo.created_at as data_pagamento', 'tr.id as id_transation', 'tr.value as valor_pago')
                ->whereNull('artR.deleted_by')
                ->whereNull('artR.deleted_at')
                ->whereNull('tr.deleted_by')
                ->whereNull('tr.deleted_at')
                ->where('artR.id', $id)
                ->distinct()
                ->get()
                ->filter(function ($item) {
                    if ($item->status == "total" && $item->path != null) {
                        return $item;
                    } else if ($item->status == "partial" && $item->path != null) {
                        return $item;
                    } else {
                        return $item;
                    }
                });




            $data = [
                'Menu' => "todos",
                'url' => "$_SERVER[HTTP_HOST]",
                'detail' => $emolunento->last(),
            ];
            

            return view("Mobile::tesouraria.detalhe")->with($data);

            return redirect()->back();
        } catch (Exception | Throwable $e) {
            Log::error($e);
            Toastr::error(__('Grades::grades.store_error_message'), __('toastr.error'));
            return response()->json(['error' => $e], 500);
        }
    }






    public function finance($type, $anoLectivo, $id_user)
    {

        try {


            $id_u = base64_decode($id_user);

            $lectiveYearSelected = DB::table('lective_years')
                ->where('id', $anoLectivo)
                ->first();

            switch ($type) {

                case "1":
                    $propina = $this->emolumento($id_u, $lectiveYearSelected);
                    $dados = $propina->where('art.id_code_dev', 2)->orderBy('artR.year', 'asc')->get()
                        ->map(function ($item) {
                            $estado = ["total" => "#57b846", "pending" => "#09bade", "partial" => "#FFCC00"];
                            $icon = ["total" => "fa-solid fa-circle-check", "pending" => "fa-solid fa-circle-xmark", "partial" => "fa-solid fa-circle-exclamation"];
                            $mes = [1 => "Janeiro", 2 => "Fevereiro", 3 => "Março", 4 => "Abril", 5 => "Maio", 6 => "Junho", 7 => "Julho", 8 => "Agosto", 9 => "Setembro", 10 => "Outubro", 11 => "Novembro", 12 => "Dezembro"];
                            return [
                                "emolumento" => $item->emolumento,
                                "id_artiRequest" => $item->id_artiRequest,
                                "valor_base" => $item->valor_base,
                                "mes" => $item->mes,
                                "display_month" => $mes[$item->mes] . "-" . $item->year,
                                "status" => $item->status,
                                "year" => $item->year,
                                "color" => $estado[$item->status],
                                "icon" => $icon[$item->status],
                                "discipline_id" => $item->discipline_id
                            ];
                        });

                    return response()->json(["propina" => $dados, "Type" => 1]);
                    break;
                case "2":
                    //saldo em carteira
                    $Saldo = DB::table('users')->where('id', $id_u)->select(['credit_balance as saldo_em_carteira'])->first();


                    $total =  number_format($Saldo->saldo_em_carteira, 2, ".", ",");
                    return response()->json(["saldo" => $total, "Type" => 2]);

                    break;

                case "3":

                    $extra = $this->emolumento($id_u, $lectiveYearSelected);
                    $dados = $extra->where('art.id_code_dev', '!=', 2)
                        ->get()
                        ->map(function ($item) {
                            $estado = ["total" => "#57b846", "pending" => "#09bade", "partial" => "#FFCC00"];
                            $icon = ["total" => "fa-solid fa-circle-check", "pending" => "fa-solid fa-circle-xmark", "partial" => "fa-solid fa-circle-exclamation"];
                            $mes = [1 => "Janeiro", 2 => "Fevereiro", 3 => "Março", 4 => "Abril", 5 => "Maio", 6 => "Junho", 7 => "Julho", 8 => "Agosto", 9 => "Setembro", 10 => "Outubro", 11 => "Novembro", 12 => "Dezembro"];
                            return [
                                "emolumento" => $item->emolumento,
                                "id_artiRequest" => $item->id_artiRequest,
                                "valor_base" => $item->valor_base,
                                "mes" => $item->mes,
                                "display_month" => $item->mes != null ? $mes[$item->mes] . "-" . $item->year : null,
                                "status" => $item->status,
                                "year" => $item->year != null ? $item->year : null,
                                "color" => $estado[$item->status],
                                "icon" => $icon[$item->status],
                                "discipline_id" => $item->discipline_id,
                                "discipline_name" => $item->disciplina != null ? $item->disciplina : null,
                                "discipline_code" => $item->codigo != null ? $item->codigo : null
                            ];
                        });

                    return response()->json(["emolumentoExtra" => $dados, "Type" => 3]);
                    break;
                case "4":

                    $valor = "";
                    $extra = $this->emolumento($id_u, $lectiveYearSelected);
                    $dados = $extra->where('artR.status', 'pending')->get()
                        ->map(function ($item) {
                            $estado = ["total" => "#57b846", "pending" => "#09bade", "partial" => "#FFCC00"];
                            $icon = ["total" => "fa-solid fa-circle-check", "pending" => "fa-solid fa-circle-xmark", "partial" => "fa-solid fa-circle-exclamation"];
                            $mes = [1 => "Janeiro", 2 => "Fevereiro", 3 => "Março", 4 => "Abril", 5 => "Maio", 6 => "Junho", 7 => "Julho", 8 => "Agosto", 9 => "Setembro", 10 => "Outubro", 11 => "Novembro", 12 => "Dezembro"];

                            return [
                                "emolumento" => $item->emolumento,
                                "id_artiRequest" => $item->id_artiRequest,
                                "valor_base" => $item->valor_base,
                                "mes" => $item->mes,
                                "display_month" => $item->mes != null ? $mes[$item->mes] . "-" . $item->year : null,
                                "status" => $item->status,
                                "year" => $item->year != null ? $item->year : null,
                                "color" => $estado[$item->status],
                                "icon" => $icon[$item->status],
                                "discipline_id" => $item->discipline_id,
                                "discipline_name" => $item->disciplina != null ? $item->disciplina : null,
                                "discipline_code" => $item->codigo != null ? $item->codigo : null
                            ];
                        });

                    $total =  number_format($dados->sum('valor_base'), 2, ".", ",");
                    return response()->json(["divida" => $dados, "Type" => 4, "total" => $total]);
                    break;
            }


            return response()->json($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            Toastr::error(__('Grades::grades.store_error_message'), __('toastr.error'));
            return response()->json(['error' => $e], 500);
        }
    }




    private function emolumento($id, $lectiveYearSelected)
    {

        $emolumento = DB::table('article_requests as artR')
            ->join('articles  as art', 'art.id', 'artR.article_id')
            ->leftJoin('article_translations as ct', function ($join) {
                $join->on('ct.article_id', '=', 'art.id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })
            ->leftJoin('disciplines as disc', 'disc.id', 'artR.discipline_id')
            ->leftJoin('disciplines_translations as dt', function ($join) {
                $join->on('dt.discipline_id', '=', 'disc.id');
                $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dt.active', '=', DB::raw(true));
            })
            ->select('ct.display_name as emolumento', 'artR.id as id_artiRequest', 'artR.base_value as valor_base', 'artR.month as mes', 'artR.status', 'artR.year', 'artR.discipline_id', 'dt.display_name as disciplina', 'disc.code as codigo')
            ->where('artR.user_id', $id)
            ->whereNull('artR.deleted_by')
            ->whereNull('artR.deleted_at')
            ->whereBetween('art.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
            ->distinct();


        return $emolumento;
    }


    private function propina_estudantes($id)
    {
    }




    public function store(Request $request)
    {

        try {
            $course = $request->get('course');
            $discipline = $request->get('discipline');
            $data = [
                'grades' => $request->get('grades'),
                'students' => $request->get('students')
            ];
            // DB::transaction(function () use ($data, $course, $discipline) {


            // });


            // Success message
            Toastr::success(__('Grades::grades.store_success_message'), __('toastr.success'));

            return redirect()->back();
        } catch (Exception | Throwable $e) {
            Log::error($e);
            Toastr::error(__('Grades::grades.store_error_message'), __('toastr.error'));
            return response()->json(['error' => $e], 500);
        }
    }







    /**
     * Update the specified resource in storage.
     *
     * @param GradeRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
        } catch (Exception | Throwable $e) {

            Log::error($e);

            Toastr::error(__('Grades::grades.store_error_message'), __('toastr.error'));

            return response()->json(['error' => $e], 500);
        }
    }


    /*Methodo de apagar um registro */
    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */







    public function exportListaExcel(Request $request)
    {

        return Excel::download(new CandidateExport, 'Lista_de_candidatos.xlsx');
        try {
        } catch (Exception | Throwable $e) {
            return redirect()->back();
        }
    }






    public function show($id)
    {
        try {


            // Success message
            Toastr::success(__('Grades::grades.destroy_success_message'), __('toastr.success'));
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Grades::grades.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }







    /*LOGIN */

    public function login(Request $request)
    {

        try {

            // return $request;

            if (Auth::attempt(['email' => $request->email, 'password' => $request->secret])) {

                // Autenticação bem-sucedida
                $user = DB::table('users')
                    ->Join('user_courses', 'users.id', '=', 'user_courses.users_id')
                    ->Join('courses', 'user_courses.courses_id', '=', 'courses.id')

                    ->leftJoin('user_parameters as fullname', function ($join) {
                        $join->on('users.id', '=', 'fullname.users_id')
                            ->where('fullname.parameters_id', 1);
                    })
                    ->leftJoin('user_parameters as matriculaNumber', function ($join) {
                        $join->on('users.id', '=', 'matriculaNumber.users_id')
                            ->where('matriculaNumber.parameters_id', 14);
                    })
                    ->leftJoin('user_parameters as fotografia', function ($join) {
                        $join->on('users.id', '=', 'fotografia.users_id')
                            ->where('fotografia.parameters_id', 25);
                    })
                    ->select(['fullname.value as fullname', 'users.email', 'users.name', 'matriculaNumber.value as matricula', 'fotografia.value as image'])
                    ->where('users.id', Auth::user()->id)
                    ->first();

                // const img = "{{ asset('storage/attachment') }}/" + dados['user'].image;
                // url_back = "/mobile/menu/" + dados['user_secret'].user_secret;

                // $("#TItulo").text(dados['user'].name);
                // $("#u_fullname").text(dados['user'].fullname);
                // $("#u_email").text(dados['user'].email);
                // $("#u_curso").text(dados['user'].curso);
                // $("#u_ano").text(dados['user'].curso);
                // $("#u_matricula").text(dados['user'].matricula);
                // $("#u_turma").text(dados['user'].matricula);

                // Casual Ruanda R



                return response()->json(['status' => "sucesso", 'user_secret' => base64_encode(Auth::user()->id), 'user' => $user]);
            } else {
                // Autenticação falhou
                return response()->json(['status' => "negado", 'errors' => ['auth' => ['E-mail ou senha inválidos.']]], 422);
            }
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
















    public function destroy($id)
    {
        try {


            // Success message
            Toastr::success(__('Grades::grades.destroy_success_message'), __('toastr.success'));
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Grades::grades.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
}
