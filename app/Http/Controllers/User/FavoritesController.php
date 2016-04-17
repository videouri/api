<?php

namespace App\Http\Controllers\User;

use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * Class FavoritesController
 * @package App\Http\Controllers\User
 */
class FavoritesController extends Controller
{
    /**
     * @return mixed
     */
    public function index()
    {
        return view('videouri.user.favorites');
    }
}
