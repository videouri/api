<?php

namespace Videouri\Repositories;

use Videouri\Entities\User;
use Videouri\Exceptions\UserRepositoryExceptions;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Validator;
use Mail;
use Redirect;
use Session;

/**
 * @NOTE
 *     The exceptions are managed at app/Exceptions/Handler.php
 */
class UserRepository
{
    use ValidatesRequests;

    public function findOrCreateSocial($userData, $provider)
    {
        $user = User::where('provider_id', '=', $userData->getId())->first();
        $emailExists = User::where('email', '=', $userData->getEmail())->first();

        // somebody is using that email,
        // and has no provider_id set
        if (!$user && $emailExists) {
            throw new UserExistsException("Email is already in use");
        }

        $user = new User([
            'username'        => $userData->nickname,
            'email'           => $userData->email,
            'avatar'          => $userData->avatar,
            'provider'        => $provider,
            'provider_id'     => $userData->id
        ]);

        return $this->saveUser($user);
    }

    public function findOrCreateRegular($request)
    {
        $rules = [
            'username' => 'required|max:255|unique:users',
            'email'    => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $this->throwValidationException($request, $validator);
        }

        // $user = User::where('email', '=', $request->input('email'))->first();

        // if (!$user) {
            $user = new User;
            $user->username = $request->input('username');
            $user->email    = $request->input('email');
            $user->password = bcrypt($request->input('password'));

            return $this->saveUser($user);
        // }

        // return true;

    }

    /**
     * [checkIfUserNeedsUpdating description]
     * @param  [type] $userData [description]
     * @param  [type] $user     [description]
     * @return [type]           [description]
     */
    public function checkIfUserNeedsUpdating($userData, $user)
    {
        $userData = [
            'avatar'   => $userData->getAvatar(),
            'email'    => $userData->getEmail(),
            'username' => $userData->getNickname(),
        ];

        $dbData = [
            'avatar'   => $user->avatar,
            'email'    => $user->email,
            'username' => $user->username,
        ];

        if (!empty(array_diff($userData, $dbData))) {
            $user->avatar   = $userData->avatar;
            $user->email    = $userData->email;
            $user->username = $userData->nickname;
            $user->save();
        }
    }

    private function saveUser(User $user)
    {
        if ($user->save()) {
            $this->generateActivationCodeAndMailIt($user);
            return true;
        }
        else {
            throw new CannotCreatUserException("Your account couldn\'t be create please try again", 1);
        }
    }

    private function generateActivationCodeAndMailIt(User $user)
    {
        $activation_code = str_random(60) . $user->email;

        $user->activation_code = $activation_code;
        $user->save();

        $data = array(
            'name' => $user->username,
            'code' => $activation_code,
        );

        // Mail::queue('emails.activateAccount', $data, function($message) use ($user) {
        //     $message->to($user->email, $user->username)->subject('Please activate your account.');
        // });

        if (count(Mail::failures()) > 0){
            throw new ActivationMailNotSentException("Failed to send activation email.");
        }
    }
}