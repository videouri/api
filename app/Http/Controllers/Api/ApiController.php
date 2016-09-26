<?php

namespace Videouri\Http\Controllers\Api;

use Auth;
use Videouri\Entities\User;
use Videouri\Http\Controllers\Controller;
use Videouri\Services\Scout\Scout;

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
     * @var Scout
     */
    protected $scout;

    /**
     * ApiController constructor.
     */
    public function __construct()
    {
        $this->user = Auth::user();
        $this->scout = app('videouri.scout');
    }
}
