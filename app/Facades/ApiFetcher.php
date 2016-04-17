<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ApiFetcher extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'api.fetcher';
    }
}
