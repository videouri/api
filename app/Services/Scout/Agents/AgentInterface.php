<?php

namespace Videouri\Services\Scout\Agents;

use Videouri\Entities\Video;

/**
 * @package Videouri\Interfaces
 */
interface AgentInterface
{
    /**
     * @param $parameters
     *
     * @return mixed
     */
    public function search($parameters);

    /**
     * @param string $content
     * @param array $parameters
     *
     * @return mixed
     */
    public function getContent($content, array $parameters);

    /**
     * @param string $videoId
     *
     * @return array|\stdClass
     */
    public function getVideo($videoId);

    /**
     * @param string $videoId
     * @param int $maxResults
     *
     * @return array
     */
    public function getRelated($videoId, $maxResults = 10);

    /**
     * @param array|\stdClass $video
     *
     * @return Video
     */
    public function parseVideo($video);

    /**
     * @param array|\stdClass $videos
     *
     * @return Video
     */
    public function parseVideos($videos);
}
