<?php

namespace App\Traits;

use Socialite;
use App\Repositories\UserRepository as Users;

trait SocialAuth
{
    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    // public function redirectToProvider(Request $request, $provider = null)
    public function redirectToProvider($provider)
    {
        if (in_array($provider, ['facebook', 'twitter'])) {
            return Socialite::driver($provider)->redirect();
        }

        return redirect(url('login'));
    }

    public function handleProviderCallback($provider)
    {
        $user = Socialite::driver($provider)->user();

        // try {
        //     $userData = $this->getSocialUser($provider);
        //     $user = $this->users->findOrCreateSocial($userData, $provider);

        //     $this->auth->login($user, true);
        //     return redirect('/');
        // } catch (Exception $e) {
        //     return redirect('/login')->with('session', $e->getMessage());
        // }

        dd($user);
    }
}
