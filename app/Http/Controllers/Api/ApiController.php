<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Entities\User;
use Auth;

class ApiController extends Controller
{
    /**
     * @var User
     */
    protected $user;

    /**
     * ApiController constructor.
     */
    public function __construct()
    {
        /** @var User user */
        $this->user = Auth::user();
    }
}