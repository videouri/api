<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Videouri\Entities\Favorite;

class FavoritesController extends Controller
{
    public function index()
    {
        return view('videouri.private.favorites');
    }
}
