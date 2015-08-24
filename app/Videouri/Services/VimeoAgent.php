<?php

namespace Videouri\Services;

class VimeoAgent
{
    /**
    * The function that interacts with Vimeo API Library to retrieve data
    *
    * @param array $parameters containing the data to be sent when querying the api
    * @return the json_decoded array data.
    */
    function data(array $parameters = array())
    {
        if (isset($parameters['sort'])) {

            switch($parameters['sort']) {
                case 'relevance':
                    $sort = 'relevant';
                    break;

                case 'published':
                    $sort = 'newest';
                    break;

                case 'views':
                    $sort = 'most_played';
                    break;

                case 'rating':
                    $sort = 'most_liked';
                    break;
            }
        }

        // $data = Vimeo::buildAuthorizationEndpoint('http://local.videouri.com/', ['public', 'private'], '12QWGAEg1235!');
        // $token = Vimeo::clientCredentials(['public', 'private']);
        // dd($token);

        switch ($parameters['content'])
        {
            /* Search and tags content */
            case 'search':
                $result = Vimeo::request('/videos', [
                    'page'     => $parameters['page'],
                    'per_page' => $parameters['maxResults'],
                    'sort'     => $sort,
                    'query'    => $parameters['searchQuery']
                ]);
                break;

            case 'tag':
                $result = Vimeo::request('/videos/getByTag', [
                    'page'     => $parameters['page'],
                    'per_page' => $parameters['maxResults'],
                    'sort'     => $sort,
                    'tag'      => $parameters['searchQuery']
                ]);
                break;

            /* Video page with video data and related videos */
            case 'getVideoEntry':
                $result = Vimeo::request("/videos/{$parameters['videoId']}");
                break;

            case 'getRelatedVideos':
                $result = Vimeo::request("/videos/{$parameters['videoId']}/videos", [
                    'page'     => $parameters['page'],
                    'per_page' => $parameters['maxResults'],
                    'filter'   => 'related',
                ]);
                break;

        }

        return $result;
    }
}