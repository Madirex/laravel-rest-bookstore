<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\User;

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
    protected $redirectTo = '/books';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Get the login username/email to be used by the controller.
     * @return string The username/email.
     */
    public function username()
    {
        $login = request()->input('login');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$field => $login]);
        return $field;
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param \Illuminate\Http\Request $request The request
     * @return array
     */
    protected function credentials(\Illuminate\Http\Request $request)
    {
        $credentials = $request->only($this->username(), 'password');

        // Check if the user is deleted
        $user = User::where($this->username(), $credentials[$this->username()])->first();
        if ($user && $user->isDeleted) {
            $request->session()->flash('error', 'Cuenta eliminada.');
            return [];
        }

        return $credentials;
    }
}
