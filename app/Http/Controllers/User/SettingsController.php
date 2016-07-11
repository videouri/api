<?php

namespace Videouri\Http\Controllers\User;

use Illuminate\View\View;
use Videouri\Http\Controllers\Controller;
use Videouri\Http\Requests;

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
        return view('videouri.user.settings');
    }
}
