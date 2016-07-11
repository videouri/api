<?php

namespace Videouri\Http\Controllers;

use Videouri\Http\Requests;
use Videouri\Services\ApiFetcher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;

/**
 * @package Videouri\Http\Controllers
 */
class VideoController extends Controller
{
    /**
     * @var ApiFetcher
     */
    protected $apiFetcher;

    /**
     * @param ApiFetcher $apiFetcher
     */
    public function __construct(ApiFetcher $apiFetcher)
    {
        $this->apiFetcher = $apiFetcher;
    }

    /**
     * @return RedirectResponse|Redirector
     */
    public function index()
    {
        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @var int $id
     * @var string $slug
     *
     * @return View
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

            default:
                abort(404);
                break;
        }

        # Return cached video or fetch it new
        if (!$video = $this->fetcher->getVideoInfo($api, $originalId)) {
            return abort(404);
        }

        $data['thumbnail'] = $video['thumbnail'];
        $data['video'] = json_encode($video);

        $relatedVideos = $this->fetcher->getRelatedVideos($api, $video['original_id']);
        if (!empty($relatedVideos)) {
            $relatedVideos = json_encode($relatedVideos);
        }

        $data['recommended'] = $relatedVideos;

        // Metadata
        $data['title'] = $video['title'] . ' - Videouri';
        $data['description'] = str_limit($video['description'], 100);
        $data['canonical'] = 'video/' . $video['custom_id'];
        $data['bodyId'] = 'videoPage';

        return view('videouri.public.video', $data);
    }
}
