<?php

namespace Videouri\Traits;

use Auth;
use Cocur\Slugify\Slugify;
use Log;
use Redirect;
use Socialite;
use Validator;
use Videouri\Entities\User;
use Videouri\Exceptions\SocialAuthException;

/**
 * @package Videouri\Traits
 */
trait SocialAuth
{
    /**
     * @param string $provider
     *
     * @return Redirect
     */
    public function redirectToProvider($provider)
    {
        if (in_array($provider, ['facebook', 'twitter'])) {
            return Socialite::driver($provider)->redirect();
        }

        return redirect('login');
    }

    /**
     * @param string $provider
     *
     * @throws SocialAuthException
     * @return Redirect
     */
    public function handleProviderCallback($provider)
    {
        /** @var \Laravel\Socialite\Contracts\User $userData */
        $userData = Socialite::driver($provider)->user();

        ///////////////////////
        // Collect user data //
        ///////////////////////
        $userId = $userData->getId();
        $email = $userData->getEmail();
        $fullName = $userData->getName();
        $userName = $userData->getNickname();
        $avatar = $userData->getAvatar();

        if (empty($email)) {
            $email = mt_rand(100, 9999) . '-' . time() . '@missingemail.com';
        }

        if (empty($userName)) {
            $slugify = new Slugify();
            $userName = $slugify->slugify($fullName);
        }

        $user = User::where('provider_id', '=', $userId)->first();
        if ($user) {
            Auth::login($user, true);
            return redirect('/');
        }

        $emailExists = User::where('email', '=', $email)->first();
        if (!$user && $emailExists) {
            return redirect('/login')->withErrors([
                'Email ' . $email . ' is already in use.',
            ]);
        }

        /////////////////////////
        // Validate and create //
        /////////////////////////

        $userData = [
            'username' => $userName,
            'email' => $email,
            'avatar' => $avatar,
            'provider' => $provider,
            'provider_id' => $userId,
        ];

        $this->validateParameters($userData);

        $user = new User($userData);

        if ($user->save()) {
            Auth::login($user, true);
            return redirect('/');
        } else {
            throw new SocialAuthException('Error occurred trying to register your account. Please try again.');
        }
    }

    /**
     * @param $userData
     *
     * @return mixed
     */
    private function validateParameters($userData)
    {
        $validator = Validator::make($userData, [
            'username' => 'required|max:255|unique:users',
            'email' => 'required|email|max:255|unique:users',
        ]);

        if ($validator->fails()) {
            return redirect('/login')
                ->withErrors($validator)
                ->withInput();
        }
    }
}
