<?php

namespace App\Http\Controllers\Auth;

use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;

use App\Entities\User;
use App\Services\Authentication;
use App\Traits\AuthenticatesAndRegistersUsers;

// use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    public $authentication;

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

    // protected $redirectTo  = 'path';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct(Authentication $authentication)
    {
        $this->middleware('guest', ['except' => 'getLogout']);

        $this->authentication = $authentication;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    // protected function validator(array $data)
    // {
    //     return Validator::make($data, [
    //         'name' => 'required|max:255',
    //         'email' => 'required|email|max:255|unique:users',
    //         'password' => 'required|confirmed|min:6',
    //     ]);
    // }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    // protected function create(array $data)
    // {
    //     return User::create([
    //         'name' => $data['name'],
    //         'email' => $data['email'],
    //         'password' => bcrypt($data['password']),
    //     ]);
    // }

    /**
     * [accountIsActive description]
     * @param  [type] $code [description]
     * @return [type]       [description]
     */
    public function accountIsActive($email, $code)
    {
        $user = User::where('email', '=', $email)
            ->where('activation_code', '=', $code)
            ->first();

        $user->active = 1;
        $user->activation_code = '';

        if ($user->save()) {
            Auth::login($user);
        }

        return true;
    }
}
