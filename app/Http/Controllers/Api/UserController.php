<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Auth;

use App\Http\Controllers\Controller;
use App\Entities\Video;
use App\Services\ApiProcessing;

class UserController extends Controller
{
    protected $user;

    // protected $apiprocessing;

    public function __construct(ApiProcessing $apiprocessing)
    {
        $this->middleware('auth');

        // $this->apiprocessing = $apiprocessing;
        $this->user = Auth::user();
    }

    public function postWatchLater(Request $request)
    {
        $id = $request->input('video_id');
        $video = Video::find($id);

        if ($video) {
            $watchLater = $video->watchLater();

            if (!$watchLater->first()) {
                // Add it for later
                $watchLater->attach($this->user->id);
            } else {
                // Remove it
                $watchLater->detach($this->user->id);
            }

            return $this->returnSuccessfullVideoAction($video);
        }

        return response()->error('There was an error saving your video for later. Please try again!');
    }

    public function postFavorite(Request $request)
    {
        $id = $request->input('video_id');
        $video = Video::find($id);

        if ($video) {
            $favorited = $video->favorited();

            if (!$favorited->first()) {
                // Add it for later
                $favorited->attach($this->user->id);
            } else {
                // Remove it
                $favorited->detach($this->user->id);
            }

            return $this->returnSuccessfullVideoAction($video);
        }

        return response()->error('There was add video to your favorites. Please try again!');
    }

    private function returnSuccessfullVideoAction(Video $video)
    {
        $video['saved_for_later'] = $video->savedForLater($this->user->id);
        $video['favorited'] = $video->isFavorited($this->user->id);

        unset($video['created_at']);
        unset($video['updated_at']);
        unset($video['deleted_at']);

        return response()->success($video);
    }
}
