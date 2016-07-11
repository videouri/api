<?php

namespace Videouri\Http\Controllers\Api;

use Videouri\Services\ApiFetcher;

/**
 * @package Videouri\Http\Controllers\Api
 */
class HistoryController extends ApiController
{
    /**
     * @param ApiFetcher
     */
    protected $apiFetcher;

    /**
     * @param ApiFetcher $apiFetcher
     */
    public function __construct(ApiFetcher $apiFetcher)
    {
        parent::__construct();

        $this->apiFetcher = $apiFetcher;
    }

    /**
     * Fetch videos watched by current user
     *
     * @return string
     */
    public function getVideos()
    {
        $records = $this->user->videosWatched()
            ->distinct()
            // ->select(['title', 'thumbnail', 'description', 'custom_id'])
            ->limit(50)
            ->get();

        $records = $records->all();

        if (count($records) > 0) {
            $records = $this->apiFetcher->transformVideos($records);
        }

        return response()->success($records);
    }
}
