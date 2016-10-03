<?php

namespace Videouri\Services\Scout\Agents;

use Dailymotion;
use Session;
use Videouri\Maps\Source;
use Videouri\Entities\Video;

/**
 * @package Videouri\Services\Agents
 */
class DailymotionAgent implements AgentInterface
{
    /**
     * @var Dailymotion
     */
    private $dailymotion;

    /**
     * @var array
     */
    private static $commonFields = [
        'id',
        'channel',
        'bookmarks_total',
        'description',
        'duration',
        'owner.id',
        'owner.screenname',
        'owner.username',
        'title',
        'thumbnail_120_url',
        'thumbnail_360_url',
        'url',
        'views_total'
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
     *
     * @return array|mixed
     * @throws \DailymotionApiException
     * @throws \DailymotionAuthException
     * @throws \DailymotionAuthRequiredException
     */
    public function getContent($content, array $parameters)
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
                        'fields' => self::$commonFields,
                        'limit' => $parameters['maxResults'],
                        'sort' => "recent",
                        'family_filter' => Session::get('family_filter'),
                        'country' => $country,
                    )
                );
                break;

            case 'top_rated':
                $results = $this->dailymotion->call(
                    '/videos',
                    array(
                        'fields' => self::$commonFields,
                        'limit' => $parameters['maxResults'],
                        'sort' => "rated{$period}",
                        'family_filter' => Session::get('family_filter'),
                        'country' => $country,
                    )
                );
                break;

            case 'most_viewed':
                $results = $this->dailymotion->call(
                    '/videos',
                    array(
                        'fields' => self::$commonFields,
                        'limit' => $parameters['maxResults'],
                        'sort' => "visited{$period}",
                        'family_filter' => Session::get('family_filter'),
                        'country' => $country,
                    )
                );
                break;
        }

        return $results;
    }

    /**
     * @param $parameters
     *
     * @return mixed
     * @throws \DailymotionApiException
     * @throws \DailymotionAuthException
     * @throws \DailymotionAuthRequiredException
     */
    public function search($parameters)
    {
        $country = Session::get('country');
        if (isset($parameters['country'])) {
            $country = $parameters['country'];
        }

        $results = $this->dailymotion->call(
            '/videos',
            [
                'fields' => self::$commonFields,
                'search' => $parameters['query'],
                'page' => $parameters['page'],
                'limit' => $parameters['maxResults'],
                'family_filter' => Session::get('family_filter'),
                'country' => $country
            ]
        );

        return $results;
    }

    /**
     * @param $videoId
     *
     * @return mixed
     * @throws \DailymotionApiException
     * @throws \DailymotionAuthException
     * @throws \DailymotionAuthRequiredException
     */
    public function getVideo($videoId)
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
     *
     * @return mixed
     * @throws \DailymotionApiException
     * @throws \DailymotionAuthException
     * @throws \DailymotionAuthRequiredException
     */
    public function getRelated($videoId, $maxResults = 10)
    {
        return $this->dailymotion->call(
            "/video/$videoId/related",
            [
                'fields' => self::$commonFields,
                'family_filter' => Session::get('family_filter')
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function parseVideo($video)
    {
        preg_match('@video/([^_]+)_([^/]+)@', $video['url'], $match);

        $originalId = $match[1];
        $customId = substr($originalId, 0, 1) . 'd' . substr($originalId, 1);

        $thumbnail = preg_replace("/^http:/i", "https:", $video['thumbnail_360_url']);
        $url = preg_replace("/^http:/i", "https:", $video['url']);

        $likes = 0;
        $dislikes = 0;

        return new Video([
            'provider' => Source::DAILYMOTION,
            'original_id' => $originalId,
            'custom_id' => $customId,

            'author' => $video['owner.username'],
            'duration' => $video['duration'],
            'views' => $video['views_total'],
            'likes' => $likes,
            'dislikes' => $dislikes,

            'data' => [
                'url' => $url,
                'title' => $video['title'],
                'description' => $video['description'],
                'thumbnail' => $thumbnail,
                'tags' => isset($video['tags']) ? $video['tags'] : [],
            ]
        ]);
    }

    /**
     * @inheritdoc
     */
    public function parseVideos($videos)
    {
        $results = [];

        foreach ($videos['list'] as $video) {
            $results[] = $this->parseVideo($video);
        }

        return $results;
    }
}
