<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\token;

class TeacherLoginController extends Controller
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
    protected $redirectTo = '/teacher/dashboard';

/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:teacher')->except('logout');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('teacher_login');
    }


    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {     
       $this->validate($request, [
            'username'   => 'required',
            'password' => 'required'
        ]);

        if (Auth::guard('teacher')->attempt(['username' => $request->username, 'password' => $request->password, 'active' => 1], $request->get('remember'))) {

            return redirect()->intended('/teacher/dashboard')->with(['token' => $request->token,'token_type' => $request->token_type,'version_code' => $request->version_code]);
        }
        return back()->withErrors(['msg', 'The Message'])->withInput($request->only('username', 'remember'));
    }

     public function logout(Request $request)
    {  if($request->token!=null){
        $std = token::Where(['token'=>$request->token,'active'=>'1'])->delete();
        }
        $this->guard('teacher')->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect('/teacher');
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('teacher');
    }
public function username()
    {
        return 'username';
    }
}
