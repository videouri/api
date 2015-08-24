<?php

namespace Videouri\Services;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Contracts\Auth\Guard;
use Laravel\Socialite\Contracts\Factory as Socialite;
use Videouri\Repositories\UserRepository as Users;

/**
 * 
 */
class Authentication
{
    public $socialite;

    public $auth;

    public $request;

    public $users;

    public function __construct(Socialite $socialite, Guard $auth, Request $request, Users $users)
    {
        $this->socialite = $socialite;
        $this->auth      = $auth;
        $this->request   = $request;
        $this->users     = $users;
    }

    public function execute($request = null, $provider)
    {
        if (!$request) return $this->getAuthorizationFirst($provider);

        try {
            $userData = $this->getSocialUser($provider);
            $user = $this->users->findOrCreateSocial($userData, $provider);

            $this->auth->login($user, true);
            return redirect('/');
        } catch (Exception $e) {
            return redirect('/login')->with('session', $e->getMessage());
        }
    }

    private function getAuthorizationFirst($provider)
    {
        return $this->socialite->driver($provider)->redirect();
    }

    private function getSocialUser($provider)
    {
        return $this->socialite->driver($provider)->user();
    }
}