<?php

namespace Videouri\Services;

use Dailymotion;
use Session;

class DailymotionAgent
{
    private $dailymotion;

    private $commonFields = array('id', 'duration', 'url', 'title', 'description', 'channel', 'thumbnail_120_url', 'thumbnail_360_url', 'rating', 'views_total');

    function __construct()
    {
        $this->dailymotion = new Dailymotion();
    }

    /**
    * The function that interacts with Dailymotion API Library to retrieve data
    *
    * @param array $parameters containing the data to be sent when querying the api
    * @return the json_decoded array data.
    */
    function data($parameters = array())
    {
        if (isset($parameters['sort'])) {
            switch($parameters['sort']) {
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

        switch ($parameters['content']) {
            /* Home content */
            case 'newest':
                $result = $this->dailymotion->call(
                    '/videos',
                    array(
                        'fields'        => $this->commonFields,
                        'limit'         => $parameters['maxResults'],
                        'sort'          => "recent",
                        'family_filter' => Session::get('family_filter'),
                        'country'       => Session::get('country'),
                    )
                );
                break;

            case 'top_rated':
                $result = $this->dailymotion->call(
                    '/videos',
                    array(
                        'fields'        => $this->commonFields,
                        'limit'         => $parameters['maxResults'],
                        'sort'          => "rated{$period}",
                        'family_filter' => Session::get('family_filter'),
                        'country'       => Session::get('country'),
                    )
                );
                break;

            case 'most_viewed':
                $result = $this->dailymotion->call(
                    '/videos',
                    array(
                        'fields'        => $this->commonFields,
                        'limit'         => $parameters['maxResults'],
                        'sort'          => "visited{$period}",
                        'family_filter' => Session::get('family_filter'),
                        'country'       => Session::get('country'),
                    )
                );
                break;

            /* Search and tags content */
            case 'search':
                $result = $this->dailymotion->call(
                    '/videos',
                    array(
                        'fields'        => $this->commonFields,
                        'search'        => $parameters['searchQuery'],
                        'page'          => $parameters['page'],
                        'limit'         => $parameters['maxResults'],
                        'sort'          => $parameters['sort'],
                        'family_filter' => Session::get('family_filter'),
                        'country'       => Session::get('country'),
                    )
                );
                break;

            /* Video page with video data and related videos */
            case 'getVideoEntry':
                $result = $this->dailymotion->call(
                    '/video/'.$parameters['videoId'],
                    array(
                        'fields' => array_merge($this->commonFields, array('embed_html', 'channel', 'tags', 'swf_url'))
                    )
                );
                break;

            case 'getRelatedVideos':
                $result = $this->dailymotion->call(
                    '/video/' . $parameters['videoId'] . '/related',
                    array(
                        'fields'        => $this->commonFields,
                        'family_filter' => Session::get('family_filter')
                    )
                );
                break;

            default:
                $result = '';
                break;
        }

        return $result;
    }
}