<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Auth;

use App\Http\Controllers\Controller;
use App\Entities\Video;

class UserController extends Controller
{
    public function postWatchLater(Request $request)
    {
        $originalId = $request->input('original_id');
        $video = Video::where('original_id', $originalId)->first();

        return response($video);
    }

    public function postFavorite(Request $request)
    {
        $originalId = $request->input('original_id');
        $video = Video::where('original_id', $originalId)->first();

        return response($video);
    }
}
