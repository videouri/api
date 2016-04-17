<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Jobs\SaveVideo;
use App\Jobs\RegisterView;
use App\Services\ApiFetcher;

use Auth;

/**
 * Class VideoController
 * @package App\Http\Controllers
 */
class VideoController extends Controller
{
    /**
     * @var ApiFetcher
     */
    protected $fetcher;

    /**
     * VideoController constructor.
     */
    public function __construct()
    {
        $this->fetcher = app('api.fetcher');
    }

    /**
     * @return mixed
     */
    public function index()
    {
        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @var int    $id
     * @var string $slug
     *
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

        if (!$video = $this->fetcher->getVideoInfo($api, $originalId)) {
            return abort(404);
        }

        $this->dispatch(new SaveVideo($video, $api));

        // dd($video);

        // If there's a user logged, register the video view
        if ($user = Auth::user()) {
            $delay = 30; // seconds
            $originalId = $video['original_id'];

            $job = (new RegisterView($originalId, $user))->delay($delay);

            $this->dispatch($job);
        }

        $data['thumbnail'] = $video['thumbnail'];
        $data['video'] = json_encode($video);

        $relatedVideos = $this->fetcher->getRelatedVideos($api, $video['original_id']);
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
}
