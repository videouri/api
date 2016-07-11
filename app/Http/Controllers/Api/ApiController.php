<?php

namespace Videouri\Http\Controllers\Api;

use Auth;
use Videouri\Entities\User;
use Videouri\Http\Controllers\Controller;

/**
 * @package Videouri\Http\Controllers\Api
 */
abstract class ApiController extends Controller
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
        $this->user = Auth::user();
    }
}
