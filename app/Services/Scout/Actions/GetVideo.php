<?php

namespace Videouri\Services\Scout\Actions;

use Auth;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Videouri\Entities\Video;
use Videouri\Jobs\RegisterView;
use Videouri\Jobs\SaveVideo;
use Videouri\Services\Scout\Actions\Traits\VideoId;

/**
 * @package Videouri\Services\Scout
 */
class GetVideo extends AbstractAction
{
    use VideoId, DispatchesJobs;

    /**
     * @return Video
     * @throws \Exception
     */
    public function process()
    {
        $originalId = $this->getVideoId();
        $api = $this->getSources()[0];

        $video = $this->fetchFromDb($originalId);

        if (!$video) {
            $video = $this->fetchFromSource($api, $originalId);
        }

        /**
         * If there's a user logged, register the video view
         */
        if ($user = Auth::user()) {
            $originalId = $video['original_id'];

            $job = (new RegisterView($originalId, $user))->onQueue('post_video_saved');
            $this->dispatch($job);
        }

        return $video;
    }

    /**
     * @param string $originalId
     *
     * @return Video
     * @throws \Exception
     */
    public function fetchFromDb($originalId)
    {
        $video = Video::where('original_id', '=', $originalId)->first();

        if ($video !== null) {
            if ($video->dmca_claim) {
                throw new \Exception('Video cannot be displayed due to DMCA claim.');
            }
        }

        return $video;
    }

    /**
     * @param string $api
     * @param string $originalId
     *
     * @return Video
     */
    public function fetchFromSource($api, $originalId)
    {
        $agent = $this->getAgent($api);

        $video = $agent->getVideo($originalId);
        if (!$video) {
            throw new NotFoundHttpException('Video not found.');
        }

        $video = $agent->parseVideo($video);

        # Job to save video if new
        $job = (new SaveVideo($video))->onQueue('pre_video_saved');
        $this->dispatch($job);

        return $video;
    }
}
