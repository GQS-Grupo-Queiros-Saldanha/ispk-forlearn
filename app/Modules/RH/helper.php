<?php
use App\Modules\GA\Models\DayOfTheWeek;
use App\Modules\GA\Models\Schedule;
use App\Modules\GA\Models\ScheduleType;
use App\Modules\Users\Models\User;
use App\Modules\GA\Models\LectiveYear;
use App\Helpers\LanguageHelper;
use App\Modules\Cms\Models\Language;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;




function horas_docente($id_funcionario) {
    $user = User::whereId($id_funcionario)->with([
        'classes',
        'disciplines' => function ($q) {
            $q->with([
                'course' => function ($q) {
                    $q->with([
                       'classes'
                   ]);
                },
            ]);
        }
    ])->firstOrFail();



    // Find
    //$schedule = Schedule::whereId(14)->with([
    $discipline_list = $user->disciplines->pluck('id')->all();
    
    $schedule_id = Schedule::whereHas('events.discipline', function ($q) use ($discipline_list) {
        $q->whereIn('id', $discipline_list);
        })->with([
            'events' => function ($q) use ($discipline_list) {
                $q->whereHas('discipline', function ($q) use ($discipline_list) {
                    $q->whereIn('id', $discipline_list);
                });
                $q->with([
                    'discipline' => function ($q) {
                        $q->with([
                        'course' => function ($q) {
                            $q->with([
                                'currentTranslation'
                                ]);
                        }
                        ]);
                        $q->with([
                            'currentTranslation'
                        ]);
                    },
                    'room' => function ($q) {
                        $q->with([
                            'currentTranslation'
                        ]);
                    }
                ]);
            },
    ])->orderBy('schedule_type_id', 'ASC')->get();
    
    $events_by_type = [];
    $schedule_id->groupBy('type.id')->each(function ($item, $key) use (&$events_by_type) {
        $events = collect([]);
            
        $item->each(function ($item, $key) use (&$events) {
            $events = $events->merge($item->events);
        });
        $events_by_type[$key] = $events;
    });
    
   

    $days_of_the_week = DayOfTheWeek::with([
        'currentTranslation'
    ])->get();

    $schedule_types = ScheduleType::with([
        'times',
        'currentTranslation'
    ])->get();
    $event=null;       
    $qtd=0;       
    foreach ($events_by_type as $key => $item) {
        foreach ($schedule_types->where('id', $key) as $type) {
            foreach ($type->times as $time) {
                foreach ($days_of_the_week as $dayOfWeek) {
                    $event = $item->where('schedule_type_time_id', $time->id)
                        ->where('day_of_the_week_id', $dayOfWeek->id)->first();
                        if(!empty($event)){
                            $qtd++;
                        }else{

                        }
                }
            }   
        }
    }
    return $qtd;
}


function ajaxfuncionarioTotalHoras($id_func, $id_contrato,$dataIncio,$dataFim){           
        // return 1;

        $presenca_func = DB::table('rh_controle_presenca as controle_presenca')        
            ->leftJoin('fun_with_type_contrato as fun_type_contrato',function ($q){
                $q->on('controle_presenca.id_fun_with_contrato','=','fun_type_contrato.id_cargo');
            })
            ->leftJoin('user_parameters as fullName', function ($join) {
                $join->on('controle_presenca.id_funcionario', '=', 'fullName.users_id')
                ->where('fullName.parameters_id', 1);
            })
            ->leftJoin('user_parameters as fullName2', function ($join) {
                $join->on('controle_presenca.created_by', '=', 'fullName2.users_id')
                ->where('fullName2.parameters_id', 1);
            })
            ->leftJoin('user_parameters as fullName1', function ($join) {
                $join->on('controle_presenca.update_by', '=', 'fullName1.users_id')
                ->where('fullName1.parameters_id', 1);
            })
            ->Join('roles as role',function ($q){
                $q->on('role.id','=','fun_type_contrato.id_cargo');
            })
            ->join('role_translations as role_trans', function ($join) {
                $join->on('role_trans.role_id', '=', 'fun_type_contrato.id_cargo')
                ->where('role_trans.language_id', 1)
                ->where('role_trans.active', 1);
            })
        ->select([
            'controle_presenca.id as id',
            'controle_presenca.id_funcionario as funcionario_id',
            'fullName.value as fullName',
            'controle_presenca.id_fun_with_contrato as contrato_id',
            'controle_presenca.data as data', 
            'controle_presenca.entrada as entrada', 
            'controle_presenca.saida as saida',
            'fullName2.value as created_by',
            'controle_presenca.created_at as created_at',
            'fullName1.value as update_by',
            'controle_presenca.update_at as update_at',
            // 'role.name as contrato',
            'role_trans.display_name as contrato'
        ])                    
        ->whereNull('controle_presenca.deleted_at')
            ->whereNull('controle_presenca.deleted_by')
            ->where('controle_presenca.id_funcionario', '=', $id_func)
            ->where('fullName.users_id', '=', $id_func)
            ->where('controle_presenca.id_fun_with_contrato', '=', $id_contrato)
            ->where('controle_presenca.falta','!=','Justificada')
            // ->whereBetween('controle_presenca.data', [$dataInicial, $dataFinal])
        // ->groupBy('controle_presenca.id_funcionario')~
        ->whereBetween('controle_presenca.data', [$dataIncio, $dataFim])

        ->distinct()
        ->get();

        // return $presenca_func;

        
        $totalHorasTrabalho = 0;
        $horasTrabalho = 0;
        $totalHoras = 0;
        $totalMinutos = 0;

        foreach ($presenca_func as $presenca) {
            
            $horasTrabalho = intervalo_duas_horas($presenca->entrada, $presenca->saida);

            $totalHoras += $horasTrabalho[0];
            $totalMinutos += $horasTrabalho[1];
        }

        $totalHorasTrabalho = [$totalHoras, $totalMinutos];

        return $totalHorasTrabalho;


        return $presenca_func;
        // return DataTables::of($presenca_func)
        // ->addColumn('actions', function ($item) {
        //     return view('RH::salarioHonorario.controle-presencas.datatables.actions',compact('item'));
        // })
        // // ->addColumn('time', function ($item) {
        // //     return view('RH::configuracoes.horaLaboral.datatables.time',compact('item'));
        // // })
        // ->rawColumns(['actions'])
        // ->addIndexColumn()
        // ->toJson();
        // // ->make(true);

}
// MÉTODO QUE RETORNA O INTERVALO DE HORAS ENTRE DOIS PERÍODOS
function intervalo_duas_horas($hora_entrada, $hora_saida) {
  
    if ($hora_entrada!=null && $hora_saida!=null) {
            # code...
        
        if (strtotime($hora_entrada) <= strtotime($hora_saida)){
            // dd("A hora final é maior.");
            // TOTAL DE HORAS MANHÃ
            $entrada = explode(':', $hora_entrada);
            $saida = explode(':', $hora_saida);
            $intervalo_horas = ($saida[0] - $entrada[0]);
            $intervalo_minutos = ($saida[1] + $entrada[1]);

            if ($intervalo_minutos<0) {
                $intervalo_minutos=$intervalo_minutos*(-1);
            }
            
        }
        else{
            // dd("A hora inicial é maior.");
            // TOTAL DE HORAS MANHÃ
            $entrada = explode(':', $hora_entrada);
            $saida = explode(':', $hora_saida);
            $intervalo_horas = ($entrada[0] - $saida[0]);
            $intervalo_minutos = ($entrada[1] + $saida[1]);

            if ($intervalo_minutos<0) {
                $intervalo_minutos=$intervalo_minutos*(-1);
            }
        }
        
        while ($intervalo_minutos>=60) {
            $intervalo_minutos=$intervalo_minutos-60;
            $intervalo_horas=$intervalo_horas+1;
            
        }
        
        // if($intervalo_minutos>=60){
        //     $intervalo_minutos=$intervalo_minutos-60;
        //     $intervalo_horas=$intervalo_horas+1;            
        // }

        return [$intervalo_horas, $intervalo_minutos];
    }else{
        return [0, 0];

    }

}

function getApiWebhookNewMatriculation($id_student){
    try{
        // DB::beginTransaction();
        $getstudent= DB::table('tb_fila_new_matriculation_webhook')
            ->where('id_user',$id_student)
            ->get();
        if ($getstudent->isEmpty()) {
            $insertfila=DB::table('tb_fila_new_matriculation_webhook')->insert([ 'id_user' => $id_student, 'created_at' => Carbon::Now(),  ]);
        }    
        $currentData = Carbon::now();
        $Proprina_month = Carbon::now()->format('m');
        $Proprina_year =  Carbon::now()->format('Y');
        $id_estudent=[];
        
            $getFila_new_matricula= DB::table('tb_fila_new_matriculation_webhook')
            ->get();
                foreach ($getFila_new_matricula as $key => $value) {
                    
                    
                    $lectiveYearSelected = DB::table('lective_years')
                        ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
                    ->first();

                    $getUser_article=DB::table('article_requests as artR')
                        ->join('articles as art', function ($join) {
                                $join->on('artR.article_id', '=', 'art.id');
                        }) 
                        ->join('article_translations as at', function ($join) {
                                $join->on('art.id', '=', 'at.article_id');
                                $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                                $join->on('at.active', '=', DB::raw(true));
                        })
                        ->leftJoin('code_developer as code_dev','code_dev.id','art.id_code_dev')
                        ->where('code_dev.code', "propina") 
                        ->where('artR.status','!=','total') 
                        ->select([
                            'artR.user_id as user_id',
                            'artR.status as estado_do_mes',
                            'artR.month as month',
                            'artR.year as year',
                        ])
                        ->whereNull('artR.deleted_at') 
                        ->whereNull('artR.deleted_by')  
                        ->where('artR.user_id',$value->id_user) 
                        ->whereBetween('art.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                    ->get();

                    $model = DB::table('users as usuario')
                        ->join('model_has_roles as usuario_cargo', 'usuario.id', '=', 'usuario_cargo.model_id')  
                        ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')  
                        ->join('role_translations as cargo_traducao', 'cargo_traducao.role_id', '=', 'cargo.id') 
                        ->leftJoin('user_parameters as user_namePar',function($join){
                            $join->on('user_namePar.users_id', '=', 'usuario.id')
                            ->where('user_namePar.parameters_id',1);
                        }) 
                        ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'usuario.id')
                        ->join('courses_translations as ct', function ($join) {
                            $join->on('ct.courses_id', '=', 'uc.courses_id');
                            $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                            $join->on('ct.active', '=', DB::raw(true));
                        })
                        ->leftJoin('user_parameters as up_meca', function ($join) {
                            $join->on('usuario.id', '=', 'up_meca.users_id')
                                ->where('up_meca.parameters_id', 19);
                        })
                        ->where('cargo_traducao.active',1)
                        ->where('cargo_traducao.language_id',1)
                        ->where('usuario_cargo.model_type',"App\Modules\Users\Models\User")
                        ->where("cargo_traducao.role_id",6)
                        ->select([
                            'user_namePar.value as nome_usuario',
                            'usuario.email as email',
                            'usuario.id as id_usuario',
                            'up_meca.value as numero_matricula',
                            'ct.display_name as nome_curso'
                            ])
                            ->orderBy('usuario.id','ASC')
                            ->whereNull('usuario.deleted_by') 
                            ->whereNull('usuario.deleted_at') 
                            ->where('usuario.id',$value->id_user) 
                    ->get(); 

                    foreach ($getUser_article as $key => $item) {
                        $resultMonth=$Proprina_month-$item->month;
                        $resultyear=$Proprina_year-$item->year;
                        if ($resultyear>0 || $resultMonth>1) {
                            in_array($item->user_id,$id_estudent) ? 0 : $id_estudent[]=$item->user_id;
                        }
                    }
                    
                    foreach ($model as $key => $item) {
                        if (in_array($item->id_usuario,$id_estudent)) {
                            $item->{'bloqueado'}=1;
                        }else{
                            $item->{'acessivel'}=0;
                        }
                    } 
                    $user=$value->id_user;
                    return (new ApiSendWebhook())->sendWebhook($model,$user);
                }
        
        // DB::commit();

    }catch (\Exception $e) {
        return $e;
        return response()->json(['message' => $e->getMessage()], 404);
    }
}

class ApiSendWebhook 
{
    public function sendWebhook($model,$user)
    {
        try {
            $messageReturno=null;
            $getClientWebHook= DB::table('client_webhook')
            ->whereNull('deleted_at')
            ->whereNull('deleted_by')
            ->where('servico','=','matricula')
            ->where('status','=','ativo')
            ->get();
            $vetorEndpoint=[];
            $model = json_encode($model);
            foreach ($getClientWebHook as $key => $item) {
                 $messageReturno =$this->sendNewMatraculation($item,$model);
                 $messageReturno= $messageReturno==false ? true : $this->againSendNewMatriculations($item,$model);
                if($messageReturno != false){
                    DB::table('tb_fila_new_matriculation_webhook')->where('id_user', $user)->delete();
                }else{

                }
            }
        } catch (\Throwable $e) {
            return $e;
        }
    }
    private function sendNewMatraculation($item,$model)
    {
       
        
        $client = new Client();
        // $url = 'https://dev.forlearn.ao/pt/api/recebeMensagem';
        $url =$item->endpoint;
        $headers=['Content-Type' => 'application/json'];

        $request=[
            'model' => $model,
            'matricula' => 'forLEARN',
        ];
        
        $request = json_encode ($request);
        $response = $client->request('POST',$url,[
            'headers' => $headers,
            "body" => $request,
        ]);
         $dataReturn=json_decode((string)$response->getBody(), true);
         
        if (isset($dataReturn['responsse']['status'])) {
           return $dataReturn['responsse']['status']==200 ? true : false;
        }
        else{ return false;}

        // return json_decode((string)$response->getBody(), true);
        //  return  $response->getStatusCode();
        // ['access_token'];
        // return json_decode($response->status(),true);
    }
    private function againSendNewMatriculations($item,$model){
        $client = new Client();
        $url =$item->endpoint;
        $headers=['Content-Type' => 'application/json'];

        $request=[
            'model' => $model,
            'matricula' => 'forLEARN',
        ];
        
        $request = json_encode ($request);
        $response = $client->request('POST',$url,[
            'headers' => $headers,
            "body" => $request,
        ]);
         $dataReturn=json_decode((string)$response->getBody(), true);
         
        if (isset($dataReturn['responsse']['status'])) {
           return $dataReturn['responsse']['status']==200 ? true : false;
        }
        else{ return false;}

    }
}

function getInformationCatraca($id_funcionario,$data){
    try{

        DB::beginTransaction();
            $client = new Client();
            // $request = $client->get('https://dev.forlearn.ao/pt/api/catraca/'.$id_funcionario.'/'.$data);
            if($id_funcionario==0){
                $request = $client->get('http://sysgestapi.somee.com/api/GetLogDeviceByData?data='.$data);

            }else{
                $request = $client->get('http://sysgestapi.somee.com/api/GetLogDeviceByFuncionarioData?numinterno='.$id_funcionario.'&data='.$data);
            }
            $response = $request->getBody();
            return $request->getStatusCode()==200 ? json_decode($response) : $response;
       DB::commit();

    }catch (\Exception $e) {
        return $e;
        return response()->json(['message' => $e->getMessage()], 404);
    }
}   

