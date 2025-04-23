<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

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
    
}
