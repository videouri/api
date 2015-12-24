<?php

namespace App\Traits;

use Socialite;
use Auth;

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

        return redirect('login');
    }

    public function handleProviderCallback($provider)
    {
        $userData = Socialite::driver($provider)->user();

        try {
            $user = $this->userRepository->findOrCreateSocial($userData, $provider);

            Auth::login($user, true);
            return redirect('/');
        } catch (Exception $e) {
            return redirect('/login')->with('session', $e->getMessage());
        }
    }
}
