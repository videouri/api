<?php

namespace App\Repositories;

use App\Entities\User;
use Cocur\Slugify\Slugify;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Redirect;
use Validator;

class UserRepository
{
    use ValidatesRequests;

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
            $user->avatar = $userData->avatar;
            $user->email = $userData->email;
            $user->username = $userData->nickname;
            $user->save();
        }
    }

    // private function saveUser(User $user)
    // {
    //     if ($user->save()) {
    //         $this->generateActivationCodeAndMailIt($user);
    //         return true;
    //     } else {
    //         throw new CreateUserException("Your account couldn\'t be create please try again", 1);
    //     }
    // }

    // private function generateActivationCodeAndMailIt(User $user)
    // {
    //     // $generatedKey = sha1(mt_rand(10000, 99999) . time() . $email);
    //     $activation_code = str_random(60) . $user->email;

    //     $user->activation_code = $activation_code;
    //     $user->save();

    //     $data = array(
    //         'name' => $user->username,
    //         'code' => $activation_code,
    //     );

    //     // Mail::queue('emails.activateAccount', $data, function($message) use ($user) {
    //     //     $message->to($user->email, $user->username)->subject('Please activate your account.');
    //     // });

    //     if (count(Mail::failures()) > 0) {
    //         throw new SendMailException("Failed to send activation email.");
    //     }
    // }
}
