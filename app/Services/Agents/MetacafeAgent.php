<?php

namespace App\Services\Agents;

/**
 * Class MetacafeController
 * @package App\Services\Agents
 */
class MetacafeController
{
    /**
     * MetacafeController constructor.
     * @TODO figure Metacafe connection out
     */
    public function __construct()
    {
        $this->metacafe = new Metacafe();
    }

    /**
    * The function that interacts with Metacafe API Library to retrieve data
    *
    * @param array $parameters containing the data to be sent when querying the api
    * @return the json_decoded array data.
    */
    function data(array $parameters = array())
    {
        $page = isset($parameters['page']) ? 1 + ($parameters['page']-1) * 10 : 1;

        /*switch ($parameters['sort']) {
            case 'relevance':
                $parameters['sort'] = 'rating';
                break;

            case 'published':
                $parameters['sort'] = 'updated';
                break;

            case 'views':
                $parameters['sort'] = 'viewCount';
                break;
        }*/

        switch ($parameters['content']) {
            /* Home content */
            case 'newest':
                $result = $this->metacafe->getMostRecentVideoFeed($parameters['period']);
                break;

            case 'top_rated':
                $result = $this->metacafe->getTopRatedVideoFeed($parameters['period']);
                break;

            case 'most_viewed':
                $result = $this->metacafe->getMostViewedVideoFeed($parameters['period']);
                break;

            /* Search and tags content */
            case 'search':
                $result = $this->metacafe->getKeywordVideoFeed($parameters['query'], array('start-index'=>$page, 'max-results' => 10));
                break;
            case 'tag':
                $result = $this->metacafe->getTagVideosFeed($parameters['query']);
                break;

            /* Video page with data and related videos */
            case 'getVideoEntry':
                $result = $this->metacafe->getItemData($id[0]);
                break;
        }

        if ($parameters['content'] == "getVideoEntry") {
            $result = simplexml_load_string($result);
            $result = $result->channel->item;
            $result['embed'] = $this->metacafe->getEmbedData($parameters['id']);
            return $result;
        }
        
        else {
            return simplexml_load_string($result);
        }
    }

    function related($id)
    {
        $result = $this->metacafe->getRelatedVideos($id);
        return simplexml_load_string($result);
    }

    /**
     * @param $videos
     * @return array|bool
     */
    public function parseVideos($videos)
    {
        $index = 1;
        $results = array();

        if (!$data) {
            return false;
        }

        foreach ($data->channel->item as $video) {
            $video = (array) $video;
            preg_match('/http:\/\/[w\.]*metacafe\.com\/watch\/([^?&#"\']*)/is', $video['link'], $match);
            $id = substr($match[1], 0, -1);
            $url = videouri_url('video/' . substr($id, 0, 1) . 'M' . substr($id, 1));

            $results['Metacafe'][$index] = array(
                'url'         => $url,
                'title'       => $video['title'],
                'description' => $this->parseDescription($video['title']),
                // 'author'      => $video['author'],
                // 'category'    => $video['category'],
                'thumbnail'   => "http://www.metacafe.com/thumb/{$video['id']}.jpg",

                'rating'      => isset($video['rank']) ? $video['rank'] : 0,
                'views'       => 0,

                'source'      => 'Metacafe',
            );

            if ($this->videoContent) {
                $results[$index]['content'] = $this->videoContent;
            }

            if ($index === $this->maxResults) {
                break;
            }

            $index++;
        }

        return $results;
    }
}