<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Videouri\Entities\User;

class SettingsController extends Controller
{
    public function index()
    {
        return view('videouri.user.settings');
    }
}
