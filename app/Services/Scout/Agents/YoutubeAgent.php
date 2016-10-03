<?php

namespace Videouri\Services\Scout\Agents;

use Alaouy\Youtube\Youtube;
use Session;
use Videouri\Maps\Source;
use Videouri\Entities\Video;

/**
 * @package Videouri\Services\Agents
 */
class YoutubeAgent implements AgentInterface
{
    /**
     * @var Youtube
     */
    private $youtube;

    /**
     * YoutubeAgent constructor.
     */
    public function __construct()
    {
        $this->youtube = new Youtube(config('youtube.KEY'));
    }

    /**
     * @param $content
     * @param $parameters
     *
     * @return array|mixed
     */
    public function getContent($content, array $parameters)
    {
        $country = Session::get('country');
        if (isset($parameters['country'])) {
            $country = $parameters['country'];
        }

        return $this->youtube->getPopularVideos($country);
    }

    /**
     * @param $parameters
     *
     * @return array
     */
    public function search($parameters)
    {
        $results = $this->youtube->search(
            $parameters['query'],
            $parameters['maxResults'],
            ['id', 'snippet']
        );

        return $results;
    }

    /**
     * @param $videoId
     *
     * @return \StdClass
     */
    public function getVideo($videoId)
    {
        return $this->youtube->getVideoInfo($videoId, ['id', 'snippet', 'contentDetails', 'statistics']);
    }

    /**
     * @param $videoId
     * @param int $maxResults
     *
     * @return array
     */
    public function getRelated($videoId, $maxResults = 10)
    {
        return $this->youtube->getRelatedVideos($videoId, $maxResults);
    }

    /**
     * @inheritdoc
     */
    public function parseVideo($video, $transform = true)
    {
        $originalId = is_object($video->id) ? $video->id->videoId : $video->id;
        $customId = substr($originalId, 0, 1) . 'y' . substr($originalId, 1);

        $duration = 0;
        $views = 0;
        $likes = 0;
        $dislikes = 0;

        // Related videos don't return contentDetails
        if (isset($video->contentDetails)) {
            $seconds = $video->contentDetails->duration;
            $duration = ISO8601ToSeconds($seconds);

            $views = $video->statistics->viewCount;
            $likes = $video->statistics->likeCount;
            $dislikes = $video->statistics->dislikeCount;
        }

        return new Video([
            'provider' => Source::YOUTUBE,
            'original_id' => $originalId,
            'custom_id' => $customId,

            'author' => $video->snippet->channelTitle,
            'duration' => $duration,
            'views' => $views,
            'likes' => $likes,
            'dislikes' => $dislikes,

            'data' => [
                'url' => 'https://www.youtube.com/watch?v=' . $originalId,
                'title' => $video->snippet->title,
                'description' => $video->snippet->description,
                'thumbnail' => $video->snippet->thumbnails->medium->url,
                'tags' => isset($video->snippet->tags) ? $video->snippet->tags : [],
            ]
        ]);
    }

    /**
     * @inheritdoc
     */
    public function parseVideos($videos)
    {
        $results = [];

        if (empty($videos)) {
            return $results;
        }

        foreach ($videos as $video) {
            $video = $this->parseVideo($video);

            $results[] = $video;
        }

        return $results;
    }
}
