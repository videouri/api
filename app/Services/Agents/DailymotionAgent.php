<?php

namespace App\Services\Agents;

use App\Entities\Video;
use App\Interfaces\ApiAgentInterface;
use Dailymotion;
use Session;

/**
 * Class DailymotionAgent
 * @package App\Services\Agents
 */
class DailymotionAgent implements ApiAgentInterface
{
    private $dailymotion;

    private static $commonFields = [
        'id', 'duration', 'url', 'title', 'description',
        'channel', 'thumbnail_120_url', 'thumbnail_360_url',
        'rating', 'views_total'
    ];

    /**
     * DailymotionAgent constructor.
     */
    public function __construct()
    {
        $this->dailymotion = new Dailymotion;
    }

    /**
     * @param $content
     * @param $parameters
     * @return array|mixed
     * @throws \DailymotionApiException
     * @throws \DailymotionAuthException
     * @throws \DailymotionAuthRequiredException
     */
    public function getContent($content, $parameters)
    {
        switch ($parameters['sort']) {
            case 'published':
                $parameters['sort'] = 'recent';
                break;

            case 'views':
                $parameters['sort'] = 'visited';
                break;

            case 'rating':
                $parameters['sort'] = 'rated';
                break;
        }

        switch ($parameters['period']) {
            case 'today':
                $period = '-today';
                break;

            case 'week':
                $period = '-week';
                break;

            case 'month':
                $period = '-month';
                break;

            case 'ever':
            default:
                $period = '';
                break;
        }

        $country = Session::get('country');
        if (isset($parameters['country'])) {
            $country = $parameters['country'];
        }

        $results = [];

        switch ($content) {
            case 'newest':
                $results = $this->dailymotion->call(
                    '/videos',
                    array(
                        'fields'        => self::$commonFields,
                        'limit'         => $parameters['maxResults'],
                        'sort'          => "recent",
                        'family_filter' => Session::get('family_filter'),
                        'country'       => $country,
                    )
                );
                break;

            case 'top_rated':
                $results = $this->dailymotion->call(
                    '/videos',
                    array(
                        'fields'        => self::$commonFields,
                        'limit'         => $parameters['maxResults'],
                        'sort'          => "rated{$period}",
                        'family_filter' => Session::get('family_filter'),
                        'country'       => $country,
                    )
                );
                break;

            case 'most_viewed':
                $results = $this->dailymotion->call(
                    '/videos',
                    array(
                        'fields'        => self::$commonFields,
                        'limit'         => $parameters['maxResults'],
                        'sort'          => "visited{$period}",
                        'family_filter' => Session::get('family_filter'),
                        'country'       => $country,
                    )
                );
                break;
        }

        return $results;
    }

    /**
     * @param $parameters
     * @return mixed
     * @throws \DailymotionApiException
     * @throws \DailymotionAuthException
     * @throws \DailymotionAuthRequiredException
     */
    public function searchVideos($parameters)
    {
        switch ($parameters['sort']) {
            case 'published':
                $parameters['sort'] = 'recent';
                break;

            case 'views':
                $parameters['sort'] = 'visited';
                break;

            case 'rating':
                $parameters['sort'] = 'rated';
                break;
        }

        switch ($parameters['period']) {
            case 'today':
                $period = '-today';
                break;

            case 'week':
                $period = '-week';
                break;

            case 'month':
                $period = '-month';
                break;

            case 'ever':
            default:
                $period = '';
                break;
        }

        $country = Session::get('country');
        if (isset($parameters['country'])) {
            $country = $parameters['country'];
        }

        $results = $this->dailymotion->call(
            '/videos',
            array(
                'fields'        => self::$commonFields,
                'search'        => $parameters['searchQuery'],
                'page'          => $parameters['page'],
                'limit'         => $parameters['maxResults'],
                'sort'          => $parameters['sort'],
                'family_filter' => Session::get('family_filter'),
                'country'       => $country,
            )
        );

        return $results;
    }

    /**
     * @param $videoId
     * @return mixed
     * @throws \DailymotionApiException
     * @throws \DailymotionAuthException
     * @throws \DailymotionAuthRequiredException
     */
    public function getVideoInfo($videoId)
    {
        return $this->dailymotion->call(
            "/video/$videoId",
            [
                'fields' => array_merge(self::$commonFields, ['embed_html', 'channel', 'tags', 'swf_url']),
            ]
        );
    }

    /**
     * @param $videoId
     * @param int $maxResults
     * @return mixed
     * @throws \DailymotionApiException
     * @throws \DailymotionAuthException
     * @throws \DailymotionAuthRequiredException
     */
    public function getRelatedVideos($videoId, $maxResults = 10)
    {
        return $this->dailymotion->call(
            "/video/$videoId/related",
            array(
                'fields'        => self::$commonFields,
                'family_filter' => Session::get('family_filter'),
            )
        );
    }

    /**
     * Parse data from source
     *
     * @param $videos
     * @param null $videoContent
     *
     * @return array
     */
    public function parseVideos($videos, $videoContent = null)
    {
        $index = 0;
        $results = array();

        // This is because of getVideoInfo
        if (!isset($videos['list'])) {
            $videos = [
                'list' => [
                    $videos,
                ],
            ];
        }

        foreach ($videos['list'] as $video) {
            preg_match('@video/([^_]+)_([^/]+)@', $video['url'], $match);

            $originalId = $match[1];
            $customId = substr($originalId, 0, 1) . 'd' . substr($originalId, 1);

            // $slug = $match[2];

            // $url = $customId . '/' . $slug;
            // $url = url('video/' . $customId);

            $thumbnailUrl = preg_replace("/^http:/i", "https:", $video['thumbnail_360_url']);
            $originalUrl = preg_replace("/^http:/i", "https:", $video['url']);

            $videoObject = new Video;

            $videoObject->provider = 'Dailymotion';
            $videoObject->original_id = $originalId;
            $videoObject->custom_id = $customId;
            $videoObject->original_url = $originalUrl;

            $videoObject->title = $video['title'];
            $videoObject->description = $video['description'];
            // $videoObject->author     = '';
            // $videoObject->category     = '';
            $videoObject->thumbnail = $thumbnailUrl;

            $videoObject->rating = $video['rating'];
            $videoObject->duration = $video['duration'];
            $videoObject->views = $video['views_total'];
            $videoObject->tags = isset($video['tags']) ? $video['tags'] : [];

            $results[$index] = $videoObject;

            if ($videoContent !== null) {
                $results[$index]['content'] = $videoContent;
            }

            $index++;
        }

        return $results;
    }
}
