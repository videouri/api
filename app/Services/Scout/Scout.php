<?php

namespace Videouri\Services\Scout;

use Videouri\Entities\Video;
use Videouri\Services\Scout\Actions\GetContent;
use Videouri\Services\Scout\Actions\GetRelated;
use Videouri\Services\Scout\Actions\GetVideo;
use Videouri\Services\Scout\Actions\Search;

/**
 * @package Videouri\Services\Scout
 */
class Scout
{
    /**
     * @param string $term
     * @param array $apis
     * @param integer $page
     * @param integer $maxResults
     *
     * @return mixed
     */
    public function search($term, array $apis, $page = 1, $maxResults = 5)
    {
        $search = new Search();

        $search->setQuery($term);
        $search->setSources($apis);
        $search->setPage($page);
        $search->setMaxResults($maxResults);

        $results = $search->process();

        return $results;
    }

    /**
     * @param string $api
     * @param string $videoId
     *
     * @return Video
     */
    public function getVideo($api, $videoId)
    {
        $getVideo = new GetVideo();

        $getVideo->setVideoId($videoId);
        $getVideo->setSources([$api]);

        $video = $getVideo->process();

        return $video;
    }

    /**
     * @param string $api
     * @param string $videoId
     * @param int $maxResults
     *
     * @return array
     */
    public function getRelated($api, $videoId, $maxResults = 10)
    {
        $getRelated = new GetRelated();

        $getRelated->setSources([$api]);
        $getRelated->setVideoId($videoId);
        $getRelated->setMaxResults($maxResults);

        $results = $getRelated->process();

        return $results;
    }

    /**
     * @param string $apis
     * @param string $content
     * @param int $maxResults
     *
     * @return array
     */
    public function getContent($apis, $content, $maxResults = 10)
    {
        $getContent = new GetContent();

        $getContent->setSources($apis);
        $getContent->setContent($content);
        $getContent->setMaxResults($maxResults);

        $results = $getContent->process();

        return $results;
    }
}
