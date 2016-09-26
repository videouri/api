<?php

namespace Videouri\Services\Scout\Actions\Traits;

/**
 * @package Videouri\Services\Scout\Actions\Traits
 */
trait VideoId
{
    /**
     * @var string
     */
    public $videoId;

    /**
     * @return string
     */
    public function getVideoId()
    {
        return $this->videoId;
    }

    /**
     * @param string $videoId
     *
     * @return $this
     */
    public function setVideoId($videoId)
    {
        $this->videoId = $videoId;
        return $this;
    }
}
