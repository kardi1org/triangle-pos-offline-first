<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        $mytime = Carbon::now();

        //if ($user->is_active != 1) {
        if (($user->is_active != 1) || ($user->valid_date < $mytime->format('Y-m-d')) && ($user->id != 1)) {
            Auth::logout();
            if (($user->is_active == 2) && ($user->id > 1)) {
                $valid = '';
            } else {
                $valid = '<br> End Valid ' . $user->valid_date;
            }
            return back()->with([
                'account_deactivated' => 'Your account is deactivated! Please contact with Super Admin.' . $valid
            ]);
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }
}
