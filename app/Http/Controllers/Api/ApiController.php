<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Presenters\User;
use App\Services\ApiFetcher;
use Auth;

class ApiController extends Controller
{
    /**
     * @var ApiFetcher
     */
    public $apiFetcher;

    /**
     * @var User
     */
    public $user;

    /**
     * ApiController constructor.
     */
    public function __construct()
    {
        $this->apiFetcher = app('api.fetcher');
        $this->user = Auth::user();
    }
}