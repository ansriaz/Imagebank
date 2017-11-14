<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use App\Logic\Services\EmailService;
use Illuminate\Http\Request;
use Log;
use Auth;
use Redirect;

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

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware($this->guestMiddleware(), ['except' => 'logout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
            'organization' => 'required|max:1024',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'organization' => $data['organization'],
            'user_role' => 2
        ]);
        Log::info($user);
        return $user;
    }

    // Override function to disable auto login after registration
    public function register(Request $request)
    {
        $this->redirectTo = '/register/thankyou';

        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }

        // Removed to prevent auto login
        //Auth::guard($this->getGuard())->login($this->create($request->all()));
        $user = $this->create($request->all());
        if(isset($user))
        {
            $EmailService = new EmailService;
            $job = $EmailService->SendRegisterEmailToAdmin($user->id);
        }

        return redirect($this->redirectPath());
    }

    // override function to check whether a user is confirmed by admin or not
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        $throttles = $this->isUsingThrottlesLoginsTrait();

        if ($throttles && $lockedOut = $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $credentials = $this->getCredentials($request);
        // Log::info($credentials);
        // if($credentials['email'] == "ansriazch@gmail.com")
        // {
        //     $this->redirectTo = '/admin/home';
        // } else {
        //     $this->redirectTo = '/home';
        // }

        if (Auth::validate($credentials)) {
            $user = Auth::getLastAttempted();
            if($user->user_role == 1)
            {
                $this->redirectTo = '/admin';
            } else {
                $this->redirectTo = '/home';
            }

            if ($user->confirmed) {
                Auth::login($user, $request->has('remember'));
                return redirect()->intended($this->redirectPath());
            } else {
                return Redirect::to('notallowed');
            }
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        if ($throttles && ! $lockedOut) {
            $this->incrementLoginAttempts($request);
        }

        return $this->sendFailedLoginResponse($request);
    }

}
