<?php

namespace App\Services;

use App\Interfaces\ApiAgentInterface;
use Vinkla\Vimeo\Facades\Vimeo;
use Session;

class VimeoAgent implements ApiAgentInterface
{
    /**
     * The function that interacts with Vimeo API Library to retrieve data
     *
     * @param array $parameters containing the data to be sent when querying the api
     * @return the json_decoded array data.
     */
    // public function data(array $parameters = array())
    // {

    //     // $data = Vimeo::buildAuthorizationEndpoint('http://local.videouri.com/', ['public', 'private'], '12QWGAEg1235!');
    //     // $token = Vimeo::clientCredentials(['public', 'private']);
    //     // dd($token);

    //     switch ($parameters['content']) {
    //         case 'tag':
    //             $results = Vimeo::request('/videos/getByTag', [
    //                 'page'     => $parameters['page'],
    //                 'per_page' => $parameters['maxResults'],
    //                 'sort'     => $sort,
    //                 'tag'      => $parameters['searchQuery']
    //             ]);
    //             break;
    //     }

    //     return $result;
    // }

    public function getContent($content, $parameters)
    {
        $results = [];
        return $results;
    }

    public function searchVideos($parameters)
    {
        if (isset($parameters['sort'])) {
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
        }

        $results = Vimeo::request('/videos', [
            'page'     => $parameters['page'],
            'per_page' => $parameters['maxResults'],
            'sort'     => $sort,
            'query'    => $parameters['searchQuery']
        ], 'GET');

        return $results;
    }

    public function getVideoInfo($videoId)
    {
        return Vimeo::request("/videos/{$videoId}");
    }

    public function getRelatedVideos($videoId, $maxResults = 10)
    {
        return Vimeo::request("/videos/{$videoId}/videos", [
            // 'page'     => $parameters['page'],
            'per_page' => $maxResults,
            'filter'   => 'related'
        ]);
    }
}
