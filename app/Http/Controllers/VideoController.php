<?php

namespace Videouri\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;
use Videouri\Maps\Source;
use Videouri\Services\Scout\Scout;
use Videouri\Services\Transformer\Transform;
use Videouri\Services\Transformer\VideoTransformer;

/**
 * @package Videouri\Http\Controllers
 */
class VideoController extends Controller
{
    /**
     * @var Scout
     */
    private $scout;

    public function __construct()
    {
        $this->scout = app('videouri.scout');
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
     * @param string $slug
     * @param string $customId
     *
     * @return View
     */
    public function show($slug, $customId)
    {
        $api = substr($customId, 1, 1);
        $originalId = substr_replace($customId, '', 1, 1);

        switch ($api) {
            case 'd':
                $api = Source::DAILYMOTION;
                break;

            case 'v':
                $api = Source::VIMEO;
                break;

            case 'y':
                $api = Source::YOUTUBE;
                break;

            default:
                abort(404);
                break;
        }

        $video = $this->scout->getVideo($api, $originalId);

        $video = Transform::item($video, new VideoTransformer());

        $data['video'] = json_encode($video);
        $data['thumbnail'] = $video['data']['thumbnail'];

        // Metadata
        $data['title'] = $video['data']['title'] . ' - Videouri';
        $data['description'] = str_limit($video['data']['description'], 100);
        $data['canonical'] = 'video/' . $video['custom_id'];
        $data['bodyId'] = 'videoPage';

        return view('public.video', $data);
    }
}
