<?php

namespace App\Interfaces;

use App\Presenters\Video;

/**
 * Interface ApiAgentInterface
 * @package App\Interfaces
 */
interface ApiAgentInterface
{
    /**
     * @param $content
     * @param $parameters
     * @return mixed
     */
    public function getContent($content, $parameters);

    /**
     * @param $parameters
     * @return mixed
     */
    public function searchVideos($parameters);

    /**
     * @param $videoId
     * @return Video
     */
    public function getVideoInfo($videoId);

    /**
     * @param $videoId
     * @param int $maxResults
     * @return array
     */
    public function getRelatedVideos($videoId, $maxResults = 10);

    /**
     * @param $videos
     * @param string $videoContent
     * @return mixed
     */
    public function parseVideos($videos, $videoContent = null);
}
