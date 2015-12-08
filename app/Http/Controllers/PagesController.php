<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function home()
    {
        return view('videouri.public.home');
    }

    public function search(Request $request)
    {
        $this->validate($request, [
            'query' => 'required|string|min:2'
        ]);

        $input = $request->only(['query']);

        return view('videouri.public.search', $input);
    }

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
