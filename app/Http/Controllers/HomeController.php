<?php

namespace App\Http\Controllers;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Model\Institution;
use App\Util\CritpyFor;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function index(Request $request)
    {
        if(Auth::check())
            return redirect()->route('main.index');
        else{
           if(PHP_SESSION_ACTIVE != session_status()) session_start();
            
            $name = Institution::select(["nome"])->first();
            $name = $name->nome;
            
            $email = $request->okwnaks ?? null;
            $password = $request->ysekl ?? null;
            
            if($email != null){
                $_SESSION['forlean_email'] = $email;
                $_SESSION['forlean_password'] = CritpyFor::decode($password);
                return redirect()->route('home');    
            }else{
                if(!isset($_SESSION['forlean_email']) || !isset($_SESSION['forlean_email']) ){
                   // return redirect("https://forlearn.ao");
                }
            }
            
            return view('auth.login',["name" => $name, "email" => $email]);
        }
    }
}
