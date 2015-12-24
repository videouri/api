<?php

namespace App\Interfaces;

interface ApiAgentInterface
{
    public function getContent($content, $parameters);

    public function searchVideos($parameters);

    public function getVideoInfo($videoId);

    public function getRelatedVideos($videoId, $maxResults = 10);
}
