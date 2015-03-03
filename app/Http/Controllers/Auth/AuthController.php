<?php namespace FireflyIII\Http\Controllers\Auth;

use FireflyIII\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use Mail;
use Session;

/**
 * Class AuthController
 *
 * @package FireflyIII\Http\Controllers\Auth
 */
class AuthController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers;

    public $redirectTo = '/';

    /**
     * Create a new authentication controller instance.
     *
     * @param  \Illuminate\Contracts\Auth\Guard     $auth
     * @param  \Illuminate\Contracts\Auth\Registrar $registrar
     *
     */
    public function __construct(Guard $auth, Registrar $registrar)
    {
        $this->auth      = $auth;
        $this->registrar = $registrar;

        $this->middleware('guest', ['except' => 'getLogout']);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function postRegister(Request $request)
    {
        $validator = $this->registrar->validator($request->all());

        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }

        $this->auth->login($this->registrar->create($request->all()));

        // get the email address
        $email = $this->auth->user()->email;

        // send email.
        Mail::send(
            'emails.registered', [], function ($message) use ($email) {
            $message->to($email, $email)->subject('Welcome to Firefly III!');
        }
        );

        // set flash message
        Session::flash('success','You have registered successfully!');


        return redirect($this->redirectPath());
    }

}