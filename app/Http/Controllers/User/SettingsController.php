<?php

namespace Videouri\Http\Controllers\User;

use Illuminate\View\View;
use Videouri\Http\Controllers\Controller;

/**
 * @package Videouri\Http\Controllers\User
 */
class SettingsController extends Controller
{
    /**
     * @return View
     */
    public function index()
    {
        return view('user.settings');
    }
}
