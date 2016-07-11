<?php

namespace Videouri\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\ResetsPasswords;
use Videouri\Http\Controllers\Controller;

/**
 * @package Videouri\Http\Controllers\Auth
 */
class PasswordController extends Controller
{
    use ResetsPasswords;

    /**
     * @var string
     */
    public $redirectTo = '/';

    /**
     * Create a new password controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
}
