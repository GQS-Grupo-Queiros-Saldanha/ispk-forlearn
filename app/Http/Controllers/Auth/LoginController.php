<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Model\checked;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = '/profile';

    protected $redirectTo = 'painelinicial';
 
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    /*public function __construct()
    {
        $this->middleware('guest')->except(['logout','guest']);
    }*/

    public function logout () {
        Auth::logout();
        return redirect()->route('home');
    }

    protected function authenticated($request, $user)
    {   
        DB::table('tb_acess_control_log')->insert([
            'id_user' => $user->id,
        ]);
        
          DB::table('tb_acess_control_log')->insert([
         'id_user' => auth()->user()->id,
         ]);  
        
        \Log::channel('auth')->info($user->email);
    }
    
    public function loginApi(Request $request)
    {
        try {
           
            if (Auth::attempt($request->only('email', 'password'))) {
                
                DB::table('tb_acess_control_log')->insert(['id_user' => auth()->user()->id]);  
                // devolve imediatamente a view/redirect que WhatsappChecked gerar
                //return $this->WhatsappChecked($request);
                return redirect()->route('main.index');

            }
            return redirect()->route('main.index');
            //return redirect("https://forlearn.ao?login_invalid=0");
        } catch (\Exception $e) {
            dd($e->getMessage());
            Log::error($e->getMessage());
            return redirect()->route('main.index');
           // return redirect("https://forlearn.ao?login_invalid=1");
        }
    }
    /*public function WhatsappChecked(Request $request)
    {
        
        $userId = auth()->id();
        $utilizador = DB::table('users')
            ->where('id', $userId)
            ->where('user_whatsapp', '945347861')
            ->whereNull('deleted_at')
            ->first();

        if (! $utilizador) {
            return redirect()->route('main.index');
        }

        $ordem = 'exibir';   // porque o utilizador foi encontrado

        return \Illuminate\Support\Facades\View::file(base_path('app/Modules/Users/Views/forlearn/criterio.blade.php'), ['id' => $userId, 'ordem' => $ordem]); // Passando o utilizador para a view
    }*/    
    
}
