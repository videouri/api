<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Entities\User;

class ProfileController extends Controller
{
    public function index()
    {
        return view('videouri.user.profile');
    }
}
