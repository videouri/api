<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Jobs\SaveVideo;
use App\Jobs\RegisterView;
use App\Transformers\VideoTransformer;

use App\Entities\Video;
use App\Services\ApiProcessing;

use League\Fractal\Resource\Item as FractalItem;
use League\Fractal\Manager as FractalManager;
use League\Fractal\Serializer\ArraySerializer;
use Illuminate\Http\Request;
use Auth;

class VideoController extends Controller
{
    /**
     * @var App\Services\ApiProcessing
     */
    protected $apiprocessing;

    /**
     * @var League\Fractal\Resource\Item
     */
    protected $fractalItem;

    public function __construct(ApiProcessing $apiprocessing)
    {
        $this->apiprocessing = $apiprocessing;
        // $this->fractalItem = $item;
    }

    public function index()
    {
        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($customId, $slug = null)
    {
        $api = substr($customId, 1, 1);
        $originalId = substr_replace($customId, '', 1, 1);

        switch ($api) {
            case 'd':
                $api = 'Dailymotion';
                break;

            case 'v':
                $api = 'Vimeo';
                break;

            case 'y':
                $api = 'Youtube';
                break;

            case 'm':
                $api = 'Metacafe';
                $long_id = $originalId . '/' . $slug;
                break;

            default:
                abort(404);
                #show_error(lang('video_id',$customId));
                break;
        }

        /**
         * If no video is fetched from DB, call API and follow
         * the process to store it for next time.
         *   '
         *       What is caching?
         *       Baby don\'t cache me, don\'t cache me!
         *       No more!
         *   '
         */
        // $video = Video::where('original_id', '=', $originalId)->first();
        // // dump($video);
        // dump($video->favorited()->whereUserId(Auth::user()->id)->get());
        // // dump($video->watchLater);

        if (!$video = $this->apiprocessing->getVideoInfo($api, $originalId)) {
            return abort(404);
        }

        $this->dispatch(new SaveVideo($video, $api));

        // dd($video);

        // $video = Video::where('original_id', $originalId)->first();
        // $video['related'] = $this->relatedVideos($api, $originalId);

        // If there's a user logged, register the video view
        if ($user = Auth::user()) {
            $delay = 30; // seconds
            $originalId = $video['original_id'];

            $job = (new RegisterView($originalId, $user))->delay($delay);

            $this->dispatch($job);
        }

        $data['thumbnail'] = $video['thumbnail'];
        $data['video'] = json_encode($video);

        $relatedVideos = $this->apiprocessing->getRelatedVideos($api, $video['original_id']);
        if (!empty($relatedVideos)) {
            $relatedVideos = json_encode($relatedVideos);
        }

        $data['recommended'] = $relatedVideos;

        // $data['source'] = $api;

        // Metadata
        $data['title'] = $video['title'] . ' - Videouri';
        $data['description'] = str_limit($video['description'], 100);
        $data['canonical'] = 'video/' . $video['custom_id'];

        $data['bodyId'] = 'videoPage';

        return view('videouri.public.video', $data);
    }

    /**
     * This function will retrieve related videos according to its id or some of its tags
     *
     * @param string $originalId The id for which to look for data
     * @return the php response from parsing the data.
     */
    private function relatedVideos($api, $originalId = null)
    {
        $this->apiprocessing->content = 'getRelatedVideos';
        $this->apiprocessing->maxResults = 8;
        // $this->apiprocessing->api = $api;
        // $this->apiprocessing->videoId = $originalId;

        $response = $this->apiprocessing->individualCall($api);
        $response = $this->apiprocessing->parseApiResult($api, $response);

        $related = $response;

        return $related;
    }
}
