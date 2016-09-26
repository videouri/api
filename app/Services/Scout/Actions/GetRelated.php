<?php

namespace Videouri\Services\Scout\Actions;

use Videouri\Services\Scout\Actions\Traits\Paginated;
use Videouri\Services\Scout\Actions\Traits\VideoId;

/**
 * @package Videouri\Services\Scout
 */
class GetRelated extends AbstractAction
{
    use Paginated, VideoId;

    /**
     * @return array
     */
    public function process()
    {
        $api = $this->getSources()[0];

        $agent = $this->getAgent($api);
        $videos = $agent->getRelated($this->getVideoId(), $this->getMaxResults());
        $videos = $agent->parseVideos($videos);

        return $videos;
    }
}
