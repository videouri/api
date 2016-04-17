<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Class PagesController
 * @package App\Http\Controllers
 */
class PagesController extends Controller
{
    /**
     * @return mixed
     */
    public function home()
    {
        return view('videouri.public.home');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function search(Request $request)
    {
        $this->validate($request, [
            'query' => 'required|string|min:2',
        ]);

        $input = $request->only(['query']);

        return view('videouri.public.search', $input);
    }

    /**
     * @param $view
     * @param null $part
     * @return mixed
     */
    public function info($view, $part = null)
    {
        switch ($view) {
            case 'legal':
                if (!in_array($part, ['dmca', 'terms-of-use'])) {
                    return redirect('/');
                }

                return view('videouri.legal.' . $part);
                break;

            default:
                return redirect('/');
                break;
        }
    }
}
