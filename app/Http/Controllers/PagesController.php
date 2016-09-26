<?php

namespace Videouri\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;

/**
 * @package Videouri\Http\Controllers
 */
class PagesController extends Controller
{
    /**
     * @return View
     */
    public function home()
    {
        return view('public.home');
    }

    /**
     * @param Request $request
     *
     * @return View
     */
    public function search(Request $request)
    {
        $this->validate($request, [
            'query' => 'required|string|min:2',
        ]);

        $input = $request->only(['query']);

        return view('public.search', $input);
    }

    /**
     * @param string $view
     * @param string $part
     *
     * @return RedirectResponse|Redirector|View
     */
    public function info($view, $part = null)
    {
        switch ($view) {
            case 'legal':
                if (!in_array($part, ['dmca', 'terms-of-use'])) {
                    return redirect('/');
                }

                return view('legal.' . $part);
                break;

            default:
                return redirect('/');
                break;
        }
    }
}
