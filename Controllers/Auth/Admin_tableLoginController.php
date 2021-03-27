<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class Admin_tableLoginController extends Controller
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
    protected $redirectTo = '/accounting/dashboard';

   /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:admin_table')->except('logout');
    }
       /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('admin_table');
    }
public function username()
    {
        return 'username';
    } 

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('account_login');
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

        if (Auth::guard('admin_table')->attempt(['username' => $request->username, 'password' => $request->password, 'active' => 1], $request->get('remember'))) {

            return redirect()->intended('/accounting/dashboard');
        }
        return back()->withErrors(['msg', 'The Message'])->withInput($request->only('username', 'remember'));
    }

   public function logout(Request $request)
    {
        $this->guard('admin_table')->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect('/accounting');
    }

   
}
