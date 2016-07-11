<?php

namespace Videouri\Http\Controllers\User;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;
use Videouri\Http\Controllers\Controller;
use Videouri\Http\Requests;

/**
 * @package Videouri\Http\Controllers\User
 */
class HistoryController extends Controller
{
    /**
     * @return RedirectResponse|Redirector
     */
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
     *
     * @return Redirector|View
     */
    public function show($user, $type)
    {
        if (!in_array($type, ['videos'])) {
            return redirect('/');
        }

        return view('videouri.user.history.' . $type);
    }
}
