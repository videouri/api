<?php

namespace Videouri\Traits;

use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Validator;

use Session;
use Auth;

use Videouri\Entities\User;
use Videouri\Repositories\UserRepository;

trait AuthenticatesAndRegistersUsers
{
    /**
     * The registrar implementation.
     *
     * @var Registrar
     */
    protected $registrar;

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Foundation\Http\FormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function postRegister(Request $request)
    {
        $users = new UserRepository;

        $users->findOrCreateRegular($request);

        return redirect('login')->with('status', 'An email with your activation code has been sent.');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogin(Request $request, $provider = null)
    {
        if (!empty($provider)) {
            if (in_array($provider, ['facebook', 'twitter'])) {
                return $this->authentication->execute($request->all(), $provider);
            } else {
                return redirect(url('login'));
            }
        }

        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postLogin(Request $request)
    {
        $this->validate($request, [
            'email' => 'required', 'password' => 'required',
        ]);

        // @TODO: Implement throttle login
        // AuthenticateUsers.php:46

        $user = User::where('email', '=', $request->email)->first();

        // if ($user) {
        //     if ($user->provider != 'laravel') {
        //         return Redirect::back()->withErrors([
        //             'email' => 'This email address has already been registered',
        //         ]);
        //     }
        // }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->has('remember'))) {
            return redirect()->intended($this->redirectPath());
        }

        // Redirect to previous page with error message
        return Redirect::back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ]);
    }

    /**
     * Log the user out of the application.
     *
     */
    public function getLogout()
    {
        Auth::logout();

        return redirect('/');
    }

    /**
     * Get the post register / login redirect path.
     *
     * @return string
     */
    public function redirectPath()
    {
        if (property_exists($this, 'redirectPath')) {
            return $this->redirectPath;
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/';
    }

}