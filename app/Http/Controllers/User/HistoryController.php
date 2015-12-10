<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;

use Videouri\Entities\Video;
use Videouri\Entities\Search;

class HistoryController extends Controller
{
    public function index()
    {
        return redirect('/');
    }

    /**
     * $user comes from
     *     'prefix'     => 'user/{name}',
     *
     * @param  string $user
     * @param  string $type
     * @return view
     */
    public function show($user, $type)
    {
        if (!in_array($type, ['videos', 'search'])) {
            return redirect('/');
        }

        $user = Auth::user();

        switch ($type) {
            case 'videos':
                $records = $user->watched;
                break;

            case 'search':
                $records = $user->searches;
                break;
        }

        return view('videouri.user.history.' . $type, compact('records'));
    }
}
