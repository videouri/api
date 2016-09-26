<?php

namespace Videouri\Http\Controllers\Api\User;

use Videouri\Entities\Video;
use Videouri\Http\Controllers\Api\ApiController;
use Videouri\Http\Requests\Request;

/**
 * @package Videouri\Http\Controllers\Api
 */
class FavoritesController extends ApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth');
    }

    /**
     * Current user's favorites
     *
     * @return array
     */
    public function getFavorites()
    {
        $records = $this->user->favorites()
            ->distinct()
            ->select(['title', 'thumbnail', 'description', 'custom_id'])
            ->limit(10)
            ->get();

        $records = $this->scout->transformVideos($records);

        return response()->success($records);
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
            $favorite = $video->favorite();

            if (!$favorite->first()) {
                // Add it for later
                $favorite->attach($this->user->id);
            } else {
                // Remove it
                $favorite->detach($this->user->id);
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
        $video['favorite'] = $video->isFavorite($this->user->id);

        unset($video['created_at']);
        unset($video['updated_at']);
        unset($video['deleted_at']);

        return response()->success($video);
    }
}
