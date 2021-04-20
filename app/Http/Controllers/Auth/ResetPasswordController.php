<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Lang;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */
    use ResetsPasswords;
    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
    
    public function showResetForm(Request $request, $token = null)
    {
        $reset = \DB::table('password_resets')->select('email')->where('token', '=', $token)->first();
        if ($reset) {
            $email = $reset->email;
            return view('auth.reset')->with(
                ['token' => $token, 'email' => $email]
            );
        } else {
            return \Redirect::to('/')->with('fails', Lang::get('lang.reset-token-expired-or-not-found'));
        }
    }
    public function reset(Request $request)
    {
        $this->validate(
            $request,
            $this->rules(),
            $this->validationErrorMessages()
        );
        $credentials = $request->all();
        $email = $credentials['email'];
        $password = $credentials['password'];
        $token = $credentials['token'];
        $response = "fails";
        $password_tokens = \DB::table('password_resets')->where('email', '=', $email)->first();
        if ($password_tokens) {
            if ($password_tokens->token == $token) {
                $users = new \App\User();
                $user = $users->where('email', $email)->first();
                if ($user) {
                    $user->password = \Hash::make($password);
                    $user->save();
                    $response = "success";
                } else {
                    $response = "fails";
                }
            }
        }
        if ($response == "success") {
            \DB::table('password_resets')->where('email', '=', $email)->delete();
            return redirect('/auth/login')->with('status', Lang::get('lang.password-reset-successfully'));
        } else {
            return redirect('/home')->with('fails', Lang::get('lang.password-can-not-reset'));
        }
    }
}
