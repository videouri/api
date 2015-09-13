<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class InfoController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($view, $part = null)
    {
        switch ($view) {
            case 'legal':
                if (!in_array($part, ['dmca', 'terms-of-use']))
                    return redirect('/');

                return view('videouri.legal.'.$part);
                break;
            
            default:
                return redirect('/');
                break;
        }
    }
}