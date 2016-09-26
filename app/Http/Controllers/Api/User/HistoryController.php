<?php

namespace Videouri\Http\Controllers\Api\User;

use Videouri\Http\Controllers\Api\ApiController;

/**
 * @package Videouri\Http\Controllers\Api
 */
class HistoryController extends ApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth');
    }

    /**
     * Current user's watched videos
     *
     * @return string
     */
    public function getWatchedVideos()
    {
        $records = $this->user->videosWatched()
            ->distinct()
            ->select(['title', 'thumbnail', 'description', 'custom_id'])
            ->limit(10)
            ->get();

        $records = $this->scout->transformVideos($records);

        return response()->success($records);
    }
}
