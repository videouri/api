<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;

use App\Entities\Video;
use App\Entities\Search;
use App\Services\ApiProcessing;

class HistoryController extends Controller
{
    protected $apiprocessing;
    protected $user;

    public function __construct(ApiProcessing $apiprocessing)
    {
        $this->middleware('auth');

        $this->apiprocessing = $apiprocessing;
        $this->user = Auth::user();
    }

    public function getVideos()
    {
        $records = $this->user->videosWatched()
                        ->distinct()
                        // ->select(['title', 'thumbnail', 'description', 'custom_id'])
                        ->limit(50)
                        ->get();

        $records = $records->all();

        if (count($records) > 0) {
            $records = $this->apiprocessing->transformVideos($records);
        }

        return response()->success($records);
    }

    // public function searches()
    // {
    //     $records = $user->searches();
    // }
}
