<?php

namespace App\Http\Controllers\Api;

/**
 * Class HistoryController
 * @package App\Http\Controllers\Api
 */
class HistoryController extends ApiController
{
    /**
     * @return mixed
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

    // public function searches()
    // {
    //     $records = $user->searches();
    // }
}
