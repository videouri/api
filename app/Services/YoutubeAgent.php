<?php

namespace App\Services;

use Alaouy\Youtube\Facades\Youtube;
use App\Interfaces\ApiAgentInterface;
use Session;

class YoutubeAgent implements ApiAgentInterface
{
    /**
     * The function that will retrieve Youtube's api response data
     *
     * @param array $parameters containing the data to be sent when querying the api
     * @return the json_decoded or rss response from Youtube.
     */
    public function data($parameters = array())
    {
        // $this->page = isset($parameters['page']) ? 1 + ($parameters['page'] - 1) * 10 : 1;

        // if ((isset($parameters['sort'])) && ($parameters['sort'] === 'views')) {
        //     $parameters['sort'] = 'viewCount';
        // }

        // switch ((isset($parameters['period']) ? $parameters['period'] : '')) {
        //     case 'today':
        //         $period = 'today';
        //         break;

        //     case 'week':
        //         $period = 'this_week';
        //         break;

        //     case 'month':
        //         $period = 'this_month';
        //         break;

        //     case 'ever':
        //     default:
        //         $period = 'all_time';
        //         break;
        // }

        // switch ($parameters['content']) {
        /* Home content */
        // case 'newest':
        //     $results = json_decode(Youtube::getMostRecentVideoFeed(
        //         array(
        //             'max-results' => $parameters['maxResults'],
        //             'fields'      => 'entry(id,title,author,gd:rating,yt:rating,yt:statistics,media:group(media:category(),media:description(),media:thumbnail(@url),yt:duration(@seconds)))',
        //             'time'        => $period,
        //             'alt'         => 'json'
        //             // 'region'      => $this->session->userdata('country'),
        //         )
        //     )
        //         , true);
        //     break;

        // case 'top_rated':
        //     $results = json_decode(Youtube::getTopRatedVideoFeed(
        //         array(
        //             'max-results' => $parameters['maxResults'],
        //             //'fields'       => '*',
        //             'fields'      => 'entry(id,published,title,author,gd:rating,yt:rating,yt:statistics,media:group(media:category(),media:description(),media:thumbnail(@url),yt:duration(@seconds)))',
        //             'time'        => $period,
        //             'alt'         => 'json'
        //             // 'region'      => $this->session->userdata('country'),
        //         )
        //     )
        //         , true);
        //     break;

        //     case 'most_viewed':
        //         $results = Youtube::getPopularVideos(Session::get('country'));
        //         break;

        // }
    }

    public function getContent($content, $parameters)
    {
        // $this->page = isset($parameters['page']) ? 1 + ($parameters['page'] - 1) * 10 : 1;

        if ((isset($parameters['sort'])) && ($parameters['sort'] === 'views')) {
            $parameters['sort'] = 'viewCount';
        }

        switch ($parameters['period']) {
            case 'today':
                $period = 'today';
                break;

            case 'week':
                $period = 'this_week';
                break;

            case 'month':
                $period = 'this_month';
                break;

            case 'ever':
            default:
                $period = 'all_time';
                break;
        }

        $country = Session::get('country');
        if (isset($parameters['country'])) {
            $country = $parameters['country'];
        }

        $results = [];

        switch ($content) {
            case 'newest':
                $results = json_decode(Youtube::getMostRecentVideoFeed([
                    'max-results' => $parameters['maxResults'],
                    'fields'      => 'entry(id,title,author,gd:rating,yt:rating,yt:statistics,media:group(media:category(),media:description(),media:thumbnail(@url),yt:duration(@seconds)))',
                    'time'        => $period,
                    'alt'         => 'json',
                    // 'region'      => $country,
                ]), true);
                break;

            case 'top_rated':
                $results = json_decode(Youtube::getTopRatedVideoFeed([
                    'max-results' => $parameters['maxResults'],
                    //'fields'       => '*',
                    'fields'      => 'entry(id,published,title,author,gd:rating,yt:rating,yt:statistics,media:group(media:category(),media:description(),media:thumbnail(@url),yt:duration(@seconds)))',
                    'time'        => $period,
                    'alt'         => 'json',
                    // 'region'      => $this->session->userdata('country'),
                ]), true);
                break;

            case 'most_viewed':
                $results = Youtube::getPopularVideos($country);
                break;
        }

        return $results;
    }

    public function searchVideos($parameters)
    {
        $results = Youtube::searchVideos(
            $parameters['searchQuery'],
            $parameters['maxResults'],
            $parameters['sort'],
            ['id', 'snippet']
        );

        return $results;
    }

    public function getVideoInfo($videoId)
    {
        return Youtube::getVideoInfo($videoId);
    }

    public function getRelatedVideos($videoId, $maxResults = 10)
    {
        return Youtube::getRelatedVideos($videoId, $maxResults);
    }
}
