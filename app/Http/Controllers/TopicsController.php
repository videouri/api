<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class TopicsController extends Controller
{
    public function music()
    {
        return 'music';
    }

    public function sports()
    {
        return 'sports';
    }

    public function trailers()
    {
        return 'trailers';
    }

    public function news()
    {
        return 'news';
    }

    public function bestOfWeek()
    {
        return 'best-of-week';
    }
}
