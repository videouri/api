<?php

namespace Videouri\Services\Scout\Agents;

use Videouri\Entities\Source;
use Videouri\Entities\Video;
use Vimeo;

/**
 * @package Videouri\Services\Agents
 */
class VimeoAgent implements AgentInterface
{
    /**
     * @param $content
     * @param $parameters
     *
     * @return array
     */
    public function getContent($content, array $parameters)
    {
        $results = [];
        return $results;
    }

    /**
     * @param $parameters
     *
     * @return mixed
     */
    public function search($parameters)
    {
        $results = Vimeo::request('/videos', [
            'page' => $parameters['page'],
            'per_page' => $parameters['maxResults'],
            'query' => $parameters['query']
        ], 'GET');

        return $results;
    }

    /**
     * @param $videoId
     *
     * @return mixed
     */
    public function getVideo($videoId)
    {
        return Vimeo::request("/videos/{$videoId}");
    }

    /**
     * @param $videoId
     * @param int $maxResults
     *
     * @return mixed
     */
    public function getRelated($videoId, $maxResults = 10)
    {
        return Vimeo::request("/videos/{$videoId}/videos", [
            'per_page' => $maxResults,
            'filter' => 'related'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function parseVideo($video)
    {
        $video = $video['body'];
        $originalId = explode('/', $video['uri'])[2];
        $customId = substr($originalId, 0, 1) . 'v' . substr($originalId, 1);

        $tags = array();
        if (!empty($video['tags'])) {
            foreach ($video['tags'] as $tag) {
                $tags[] = $tag['name'];
            }
        }

        return new Video([
            'provider' => Source::VIMEO,
            'original_id' => $originalId,
            'custom_id' => $customId,

            'author    ' => $video['user']['name'],
            'duration' => $video['duration'],
            'views' => $video['stats']['plays'],
            'ratings' => $video['metadata']['connections']['likes']['total'],

            'data' => [
                'url' => 'https://vimeo.com/' . $originalId,
                'title' => $video['name'],
                'description' => $video['description'],
                'thumbnail' => $video['pictures']['sizes'][2]['link'],
                'tags' => $tags,
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

        foreach ($videos['body']['data'] as $rawVideo) {
            $video['body'] = $rawVideo;
            $results[] = $this->parseVideo($video);
        }

        return $results;
    }
}
