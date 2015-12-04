<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Videouri\Entities\Video;
use Videouri\Entities\SearchHistory;
use Videouri\Entities\UserVideoHistory;

class HistoryController extends Controller
{
    public function index()
    {
        return redirect('/');
    }

    public function show($type)
    {
        // if (!in_array($type, ['videos', 'search'])) {
        //     return redirect('/');
        // }

        return view('videouri.user.history');
    }
}
