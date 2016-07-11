<?php

namespace Videouri\Http\Controllers\Api;

use Illuminate\Http\Request;
use Videouri\Entities\Video;

/**
 * @package Videouri\Http\Controllers\Api
 */
class UserController extends ApiController
{
    /**
     * @param Request $request
     *
     * @return string
     */
    public function postWatchLater(Request $request)
    {
        $id = $request->input('original_id');

        /** @var Video $video */
        $video = Video::where('original_id', '=', $id)->first();

        if ($video) {
            $watchLater = $video->watchLater();

            if (!$watchLater->first()) {
                // Add it for later
                $watchLater->attach($this->user->id);
            } else {
                // Remove it
                $watchLater->detach($this->user->id);
            }

            return $this->returnSuccessfulVideoAction($video);
        }

        return response()->error('There was an error saving your video for later. Please try again!');
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    public function postFavorite(Request $request)
    {
        $id = $request->input('original_id');

        /** @var Video $video */
        $video = Video::where('original_id', '=', $id)->first();

        if ($video) {
            $favorited = $video->favorited();

            if (!$favorited->first()) {
                // Add it for later
                $favorited->attach($this->user->id);
            } else {
                // Remove it
                $favorited->detach($this->user->id);
            }

            return $this->returnSuccessfulVideoAction($video);
        }

        return response()->error('There was add video to your favorites. Please try again!');
    }

    /**
     * @param Video $video
     *
     * @return string
     */
    private function returnSuccessfulVideoAction(Video $video)
    {
        $video['saved_for_later'] = $video->savedForLater($this->user->id);
        $video['favorited'] = $video->isFavorited($this->user->id);

        unset($video['created_at']);
        unset($video['updated_at']);
        unset($video['deleted_at']);

        return response()->success($video);
    }
}
