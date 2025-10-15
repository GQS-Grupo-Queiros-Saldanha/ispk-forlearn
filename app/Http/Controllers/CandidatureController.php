<?php

namespace App\Http\Controllers;

use App\Mail\NewLinkAccessCandidate;
use App\Modules\Users\Models\Role;
use App\Modules\Users\Models\User;
use App\Modules\Users\Models\UserCandidate;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class CandidatureController extends Controller
{
    //
    public function register()
    {
        return view('candidatures.form');
    }


    public function submitData(Request $request)
    {

        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email'
        ]);

        $email = $request->get('email');
        //avaliar se o email ja tinha sido submetido antes

        $emailExists = DB::table('candidatures_registration')
                        ->where('email', $email)
                        // ->where('state','!=', 'open')
                        ->get();

        $generatedPassword = "";
        $candidateId = "";
        if ($emailExists->isEmpty()) {
            $generatedPassword = Str::random(8);
            DB::table('candidatures_registration')->insert([
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'link_send_times' => 1,
                'password' => $generatedPassword,
                'created_at' => now(),
            ]);

            $candidate = DB::table('candidatures_registration')->where('email', $email)->first();
            $candidateId = $candidate->id;

            $data = [
                "name" => $request->get('name'),
                "email" => $request->get('email'),
                "password" => $generatedPassword,
                "link_to_access" => "https://forlearn.ispm.co.ao/pt/candidaturas/candidate-login"
            ];

            Mail::send('emails.link-access', $data, function($info) use ($data){
                 $info->to($data['email'], $data['name'])->subject('Candidatura | Pré-registo');
                $info->from('candidaturas@forlearn.ispm.co.ao', 'Candidatura | Pré-registo');
            });

            $candidateHashed = base64_encode($candidateId);
            return redirect()->route('message-success', compact('candidateHashed'));
        } else {
           $code=1;
            return view('candidatures.message-erro',['code'=>$code,'email'=> $email ]);
        }

        //Mail::to("f.campos.gqs@gmail.com")->send(new NewLinkAccessCandidate);
        // $data = ["name" => $request->get('name'), "email" => $request->get("email")];

        // Mail::send('emails.link-access', $data, function($info){
        //     $info->from('dev.franciscocampos@gmail.com', 'Example');
        //     $info->to('f.campos.gqs@gmail.com', 'Andre')->subject('Laravel email');
        // });

        // $to_name = 'RECEIVER_NAME';
        // $to_email = 'dev.franciscocampos@gmail.com';
        // $data = array('name'=>"Ogbonna Vitalis(sender_name)", "body" => "A test mail");
        // Mail::send('emails.link-access', $data, function($message) use ($to_name, $to_email) {
        // $message->to($to_email, $to_name)
        // ->subject('Laravel Test Mail');
        // $message->from("franciiscocampos170@gmail.com",'Test Mail');
        // });

        // return "email enviado!";
    }

    public function callMessageSuccess($id)
    {
        $id = base64_decode($id);
        $candidateExists = DB::table('candidatures_registration')->where('id', $id)->where('state', "open")->count();

        if ($candidateExists > 0) {
            $candidate = DB::table('candidatures_registration')->where('id', $id)->first();
            $candidateEmail = $candidate->email;
            return view('candidatures.message-success', compact('candidateEmail'));
        }else{
            return redirect()->route('candidaturas');
        }
    }

    public function callMessageReSend($id)
    {
        //$id = base64_decode($id);

        $candidateExists = DB::table('candidatures_registration')->where('id', $id)->where('state', "open")->count();

        if ($candidateExists > 0) {
            $candidate = DB::table('candidatures_registration')->where('id', $id)->first();
            $candidateEmail = $candidate->email;
            return view('candidatures.message-success', compact('candidateEmail'));
        }else{
            return redirect()->route('candidaturas');
        }
    }

    public function login()
    {
        return view('candidatures.login');
    }

    public function makeLogin(Request $request)
    {
        try{
            $td = DB::table('candidatures_registration')
                        ->where('email', $request->get('email'))
                        ->where('password', $request->get('password'))
                        ->where('state', "open")
                        ->count();

            if ($td > 0) {
                $this->validate($request, [
                        'email'   => 'required|email',
                        'password'  => 'required|alphaNum|min:3'
                    ]);

                $user_data = array(
                        'email'  => $request->get('email'),
                        'password' => $request->get('password')
                    );


                $user = DB::table('candidatures_registration')
                            ->where('email', $request->get('email'))
                            ->where('password', $request->get('password'))
                            ->where('state', "open")
                            ->count();


                $userInfo = DB::table('candidatures_registration')
                                ->where('email', $request->get('email'))
                                ->where('password', $request->get('password'))
                                ->where('state', "open")
                                ->first();



                if ($user > 0) {
                    $userId = $userInfo->id;
                    $userIdHashed = base64_encode($userId);
                    return redirect()->route('candidate.form', compact('userIdHashed'));
                } else {
                    $code=3;
                    return view('candidatures.message-erro',['code'=>$code,'email'=>$request->get('email')]);
                }


            } else {
                 $code=2;
                return view('candidatures.message-erro', ['code'=>$code,'email'=>$request->get('email')]);

            }
        }catch (Exception | Throwable $e) {
            Log::error($e);
            Toastr::error($e->getMessage(), __('toastr.error'));
            return redirect()->back();
        }
    }

    public function candidateForm($id)
    {
        try {
            $id = base64_decode($id);

            $user = DB::table('candidatures_registration')
                        ->where('id', $id)
                        ->first();

            $nameExploded = explode(" ",$user->name);

            foreach($nameExploded as $key => $element) {
                if ($key === array_key_first($nameExploded))
                {
                    $firstName = $element;
                }

                if ($key === array_key_last($nameExploded))
                {
                    $lastName = $element;
                }
            }

            $name = $firstName . " " . $lastName;

            $lenght = strlen($firstName);

            $emailGenerated = "";

            $specialCharacters = [
                 "á" => "a", "à" => "a", "â" => "a", "ã" => "a" ,"Á" => "A", "À" => "A", "Â" => "A", "Ã" => "A",
                 "È" => "E", "É" => "E", "è" => "e", "é" => "e", "Ê" => "E", "ê" => "e",
                 "Ì" => "I", "Í" => "I", "ì" => "i", "í" => "i", "Î" => "I", "î" => "i",
                 "ó" =>"o", "ò" =>"o", "Ó" => "O", "Ò" => "O", "Ô" => "O", "Õ" => "O", "õ" => "o", "ô" => "o",
                 "Ù" => "U", "Ú" => "U", "ù" =>"u", "ú" =>"u", "û" => "u", "Û" => "U" ];


            for ($i=0; $i <= $lenght; $i++) {
                $letter = strtr($firstName, $specialCharacters);
                $email = strtolower(substr($letter, 0, $i + 1) .".". $lastName . "@ispm.co.ao");

                $checkEmail = User::where('users.email', '=', $email)
                            ->get();

                if ($checkEmail->isEmpty()) {
                    $emailGenerated = $email;
                    break;
                    //return response()->json($email);
                }

            }

            // $name = $firstName . " " . $lastName;

            // $lenght = strlen($firstName);

            // $emailGenerated = "";
            //     for ($i=0; $i <= $lenght; $i++) {

            //     $letter = strtr(substr($firstName, 0, $i + 1), $specialCharacters);
            //     $email = strtolower($letter .".". $lastName . "@ispm.co.ao");

            //     $checkEmail = User::where('users.email', '=', $email)
            //                 ->get();

            //         if ($checkEmail->isEmpty()) {
            //             $emailGenerated = $email;
            //             break;
            //         }
            //     }

            //aqui fazer um fetch com o utilizador se o estado ja for mudado nao retornar para pagina de candidatura

            $roles = Role::with([
                'currentTranslation'
            ])->where('id', 15)->first();

            $user = DB::table('candidatures_registration')
                        ->where('id', $id)
                        ->first();

            $data = [
                'action' => 'create',
                //'parameters' => $parameters,
                //'parameter_groups' => $parameter_groups,
                'roles' => $roles,
                'user' => $user,
                'name' => $name,
                'email' => $emailGenerated
            ];

            return view('candidatures.candidate-form')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            Toastr::error($e->getMessage(), __('toastr.error'));
            return redirect()->back();
        }
    }

    public function convertToEmail($name)
    {
        $pieces = explode(",", $name);
        //contar a quantidade de letras no nome para usar
        $lenght = strlen($pieces[0]);

        //avaliar caso existir um email com pieces
        //$email = strtolower(substr($pieces[0], 0, 1) .".". $pieces[1] . "@ispm.co.ao");

        $specialCharacters = [
                 "á" => "a", "à" => "a", "Á" => "A", "À" => "A",
                 "È" => "E", "É" => "E", "è" => "e", "é" => "e",
                 "Ì" => "I", "Í" => "I", "ì" => "i", "í" => "i",
                 "ó" =>"o", "ò" =>"o", "Ó" => "O", "Ò" => "O",
                 "Ù" => "U", "Ú" => "U", "ù" =>"u", "ú" =>"u" ];

        for ($i=0; $i <= $lenght; $i++) {
            $letter = strtr(substr($pieces[0], 0, $i + 1), $specialCharacters);

            $email = strtolower($letter .".". $pieces[1] . "@ispm.co.ao");

            $checkEmail = User::where('users.email', '=', $email)
                         ->get();

            if ($checkEmail->isEmpty()) {
                return $email;//response()->json($email);
            }
        }
    }

    public function store(Request $request)
    {
        //validar aqui tambem....
        try {
            DB::beginTransaction();

            // Check if it was deleted
            $user = User::withTrashed()->where('email', $request->get('email'))->first();
            if ($user) {

                // Update
                $user->name = $request->get('name');
                $user->email = $request->get('email');
                $user->password = bcrypt($request->get('id_number'));
                $user->updated_by = 1;
                $user->deleted_at = null;
                $user->save();
            } else {

                // Create
                $user = User::create([
                    'name' => $request->get('name'),
                    'email' => $request->get('email'),
                    'password' => bcrypt($request->get('id_number')),
                    'created_by' => 1 ?? 0
                ]);

                $user->save();
            }

            // ****************************************************************************************************** //

            // full_name
            $user_parameters[] = [
                'parameters_id' => 1,
                'created_by' => 1 ?? 0,
                'parameter_group_id' => 2,
                'value' => $request->get('full_name')
            ];

            $user->parameters()->sync($user_parameters);

            // Roles
            $user->syncRoles($request->get('roles'));


                $latestsCandidate = UserCandidate::latest()->first();
                if ($latestsCandidate && Carbon::parse($latestsCandidate->created_at)->year === Carbon::now()->year) {
                    $nextCode = 'CE' . ((int)ltrim($latestsCandidate->code, 'CE') + 1);
                } else {
                    $nextCode = 'CE' . Carbon::now()->format('y') . '0001';
                }

                UserCandidate::create([
                    'user_id' => $user->id,
                    'code' => $nextCode,
                    'created_at' => Carbon::now()
                ]);

                //candidate_number
                $user_number[] = [
                    'parameters_id' => 311,
                    'created_by' => 1 ?? 0,
                    'parameter_group_id' => 1,
                    'value' => $nextCode
                ];

                $user->parameters()->attach($user_number);


                //candidate_email
                $user_mail[] = [
                    'parameters_id' => 312,
                    'created_by' => 1 ?? 0,
                    'parameter_group_id' => 1,
                    'value' => $request->get('email')
                ];

                $user->parameters()->attach($user_mail);

                DB::table('candidatures_registration')
                        ->where('id', $request->get('candidateId'))
                        ->update(['state' => "closed"]);

                $candidatx = DB::table('candidatures_registration')
                    ->where('id', $request->get('candidateId'))
                    ->first();

                //candidate_number
                $personal_email[] = [
                    'parameters_id' => 34,
                    'created_by' => 1 ?? 0,
                    'parameter_group_id' => 6,
                    'value' => $candidatx->email
                ];

                $user->parameters()->attach($personal_email);

            DB::commit();
             $data = [

                    "name" => $candidatx->name ,
                    "emailCandidato" => $candidatx->email ,
                    "email" => $request->get('email')
             ];

            if ((int)$request->get('roles') === 15) {
                // Toastr::success(__('Users::users.store_success_message'), __('toastr.success'));
                /*$user = User::whereId($user->id)->first();
                $data = [
                    'action' => 'edit'
                ];*/
              Mail::send('emails.link-dados', $data, function($info) use ($data){
                    $info->to($data['emailCandidato'], $data['name'])->subject('Candidatura | Perfil de candidato');
                    $info->from('candidaturas@forlearn.ispm.co.ao', 'Candidatura | Perfil de candidato');
                });
                $code=4;
                 return view('candidatures.message-erro',['code'=>$code,'email'=> $request->get('email') ]);
            }
            // Success message
                 Mail::send('emails.link-dados', $data, function($info) use ($data){
                    $info->to($data['emailCandidato'], $data['name'])->subject('Candidatura | Perfil de candidato');
                    $info->from('candidaturas@forlearn.ispm.co.ao', 'Candidatura | Perfil de candidato');
                });
                $code=4;
                 return view('candidatures.message-erro',['code'=>$code,'email'=> $request->get('email') ]); 


            Toastr::success(__('Users::users.store_success_message'), __('toastr.success'));
            return redirect()->route('login');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function reSendEmail(Request $request)
    {

        try {

        switch ($request->submitButton) {
            case 'Reenviar e-mail':
                $email = DB::table('candidatures_registration')
                            ->where('email', $request->get('email'))
                            ->where('state', "open")
                            ->first();

                                DB::transaction(function() use($request, $email){

                                    $newGeneratedPassword = Str::random(8);
                                    
                                    $data = [
                                        "name" => $email->name,
                                        "email" => $request->email,
                                        "password" => $newGeneratedPassword,
                                        "link_to_access" => "https://forlearn.ispm.co.ao/pt/candidaturas/candidate-login"
                                            ];

                                    DB::table('candidatures_registration')
                                            ->where('email', $request->get('email'))
                                            ->where('state', "open")
                                            ->update(['password' => $newGeneratedPassword, 'link_send_times' => $email->link_send_times + 1]);

                                    Mail::send('emails.link-access', $data, function ($info) use ($data) {
                                        $info->to($data['email'], $data['name'])->subject('Recuperação de dados de acesso');
                                        $info->from('candidaturas@forlearn.ispm.co.ao', 'Candidatura | Pré-registo');
                                    });

                                });
                                $candidateId = $email->id;
                                return redirect()->route('message-resend', compact('candidateId'));
                break;

            default:
                # code...
                break;
        }
        } catch (Exception | Throwable $e) {
            Log::error($e);
            Toastr::error($e->getMessage(), __('toastr.error'));
            return redirect()->back();
        }

    }
}
