<?php

namespace App\Services\Agents;

use App\Entities\Video;
use App\Interfaces\ApiAgentInterface;
use Vinkla\Vimeo\Facades\Vimeo;
use Session;

/**
 * Class VimeoAgent
 * @package App\Services\Agents
 */
class VimeoAgent implements ApiAgentInterface
{
    /**
     * @param $content
     * @param $parameters
     * @return array
     */
    public function getContent($content, $parameters)
    {
        $results = [];
        return $results;
    }

    /**
     * @param $parameters
     * @return mixed
     */
    public function searchVideos($parameters)
    {
        switch ($parameters['sort']) {
            case 'published':
                $sort = 'newest';
                break;

            case 'views':
                $sort = 'most_played';
                break;

            case 'rating':
                $sort = 'most_liked';
                break;

            case 'relevance':
            default:
                $sort = 'relevant';
                break;
        }

        $country = Session::get('country');
        if (isset($parameters['country'])) {
            $country = $parameters['country'];
        }

        $results = Vimeo::request('/videos', [
            'page'     => $parameters['page'],
            'per_page' => $parameters['maxResults'],
            'sort'     => $sort,
            'query'    => $parameters['searchQuery']
        ], 'GET');

        return $results;
    }

    /**
     * @param $videoId
     * @return mixed
     */
    public function getVideoInfo($videoId)
    {
        return Vimeo::request("/videos/{$videoId}");
    }

    /**
     * @param $videoId
     * @param int $maxResults
     * @return mixed
     */
    public function getRelatedVideos($videoId, $maxResults = 10)
    {
        return Vimeo::request("/videos/{$videoId}/videos", [
            // 'page'     => $parameters['page'],
            'per_page' => $maxResults,
            'filter'   => 'related'
        ]);
    }

    /**
     * Parse data from source
     *
     * @param $videos
     * @param null $videoContent
     * @throws NotFoundHttpException
     *
     * @return array
     */
    public function parseVideos($videos, $videoContent = null)
    {
        $index = 0;
        $results = [];

        if (empty($videos) || array_key_exists('error', $videos)) {
            throw new NotFoundHttpException('Video not found');
        }

        // // No results returned
        // if (isset($videos['body']['data']) && empty($videos['body']['data'])) {
        //     return [];
        // }

        // $videos = $videos['body'];

        // if (isset($videos['data'])) {
        //     $videos = $videos['data'];
        // }

        if (empty($videos['body'])) {
            return [];
        }

        // This is for getVideoInfo function
        if (!empty($videos['body']) && !isset($videos['body']['data'])) {
            $videos['body'] = [
                'data' => [
                    $videos['body'],
                ],
            ];
        }

        foreach ($videos['body']['data'] as $video) {
            $originalId = explode('/', $video['uri'])[2];
            $customId = substr($originalId, 0, 1) . 'v' . substr($originalId, 1);

            $videoObject = new Video;

            $videoObject->provider = 'Vimeo';
            $videoObject->original_id = $originalId;
            $videoObject->custom_id = $customId;
            $videoObject->original_url = 'https://vimeo.com/' . $originalId;

            $videoObject->title = $video['name'];
            $videoObject->description = $video['description'];
            // $videoObject->author     = $video['user']['name'];
            // $videoObject->category   = '';
            $videoObject->thumbnail = $video['pictures']['sizes'][2]['link'];

            $videoObject->rating = $video['metadata']['connections']['likes']['total'];
            $videoObject->duration = $video['duration'];
            $videoObject->views = $video['stats']['plays'];
            $videoObject->tags = $video['stats']['plays'];

            $tags = array();
            if (!empty($video['tags'])) {
                foreach ($video['tags'] as $tag) {
                    $tags[] = $tag['name'];
                }
            }

            $videoObject->tags = $tags;

            $results[$index] = $videoObject;

            if ($videoContent !== null) {
                $results[$index]['content'] = $videoContent;
            }

            // if ($index === $this->maxResults) {
            //     break;
            // }

            $index++;
        }

        return $results;
    }
}
