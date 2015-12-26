<?php

namespace App\Services;

use App\Interfaces\ApiAgentInterface;
use Dailymotion;
use Session;

class DailymotionAgent implements ApiAgentInterface
{
    private $dailymotion;

    private $commonFields = [
        'id', 'duration', 'url', 'title', 'description',
        'channel', 'thumbnail_120_url', 'thumbnail_360_url',
        'rating', 'views_total'
    ];

    public function __construct()
    {
        $this->dailymotion = new Dailymotion;
    }

    /**
     * The function that interacts with Dailymotion API Library to retrieve data
     *
     * @param array $parameters containing the data to be sent when querying the api
     * @return the json_decoded array data.
     */
    // public function data($parameters = array())
    // {
    //     if (isset($parameters['sort'])) {
    //         switch ($parameters['sort']) {
    //             case 'published':
    //                 $parameters['sort'] = 'recent';
    //                 break;

    //             case 'views':
    //                 $parameters['sort'] = 'visited';
    //                 break;

    //             case 'rating':
    //                 $parameters['sort'] = 'rated';
    //                 break;
    //         }
    //     }

    //     switch ($parameters['period']) {
    //         case 'today':
    //             $period = '-today';
    //             break;

    //         case 'week':
    //             $period = '-week';
    //             break;

    //         case 'month':
    //             $period = '-month';
    //             break;

    //         case 'ever':
    //         default:
    //             $period = '';
    //             break;
    //     }

    //     switch ($parameters['content']) {
    //         /* Home content */
    //         case 'newest':
    //             $results = $this->dailymotion->call(
    //                 '/videos',
    //                 array(
    //                     'fields'        => $this->commonFields,
    //                     'limit'         => $parameters['maxResults'],
    //                     'sort'          => "recent",
    //                     'family_filter' => Session::get('family_filter'),
    //                     'country'       => Session::get('country'),
    //                 )
    //             );
    //             break;

    //         case 'top_rated':
    //             $results = $this->dailymotion->call(
    //                 '/videos',
    //                 array(
    //                     'fields'        => $this->commonFields,
    //                     'limit'         => $parameters['maxResults'],
    //                     'sort'          => "rated{$period}",
    //                     'family_filter' => Session::get('family_filter'),
    //                     'country'       => Session::get('country'),
    //                 )
    //             );
    //             break;

    //         case 'most_viewed':
    //             $results = $this->dailymotion->call(
    //                 '/videos',
    //                 array(
    //                     'fields'        => $this->commonFields,
    //                     'limit'         => $parameters['maxResults'],
    //                     'sort'          => "visited{$period}",
    //                     'family_filter' => Session::get('family_filter'),
    //                     'country'       => Session::get('country'),
    //                 )
    //             );
    //             break;
    //     }

    //     return $result;
    // }

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
                        'fields'        => $this->commonFields,
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
                        'fields'        => $this->commonFields,
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
                        'fields'        => $this->commonFields,
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
                'fields'        => $this->commonFields,
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

    public function getVideoInfo($videoId)
    {
        return $this->dailymotion->call(
            "/video/$videoId",
            [
                'fields' => array_merge($this->commonFields, ['embed_html', 'channel', 'tags', 'swf_url']),
            ]
        );
    }

    public function getRelatedVideos($videoId, $maxResults = 10)
    {
        return $this->dailymotion->call(
            "/video/$videoId/related",
            array(
                'fields'        => $this->commonFields,
                'family_filter' => Session::get('family_filter'),
            )
        );
    }
}
