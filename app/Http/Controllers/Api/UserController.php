<?php

namespace App\Http\Controllers\Api;

use App\Entities\Video;
use Illuminate\Http\Request;

/**
 * Class UserController
 * @package App\Http\Controllers\Api
 */
class UserController extends ApiController
{
    /**
     * @param Request $request
     * @return mixed
     */
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

    /**
     * [postFavorite description]
     *
     * @param  Request $request [description]
     * @return [type]           [description]
     */
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

    /**
     * [returnSuccessfullVideoAction description]
     *
     * @param  Video  $video [description]
     * @return [type]        [description]
     */
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
