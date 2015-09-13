<?php

namespace Videouri\Services;

use Alaouy\Youtube\Facades\Youtube;
use Session;

class YoutubeAgent
{
    /**
    * The function that will retrieve Youtube's api response data
    *
    * @param array $parameters containing the data to be sent when querying the api
    * @return the json_decoded or rss response from Youtube.
    */
    public function data($parameters = array())
    {
        $this->page = isset($parameters['page']) ? 1 + ($parameters['page'] - 1) * 10 : 1;

        if ((isset($parameters['sort'])) && ($parameters['sort'] === 'views')) {
            $parameters['sort'] = 'viewCount';
        }

        switch ((isset($parameters['period']) ? $parameters['period'] : '')) {
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

        switch ($parameters['content']) {
            /* Home content */
            case 'newest':
                $result = json_decode(Youtube::getMostRecentVideoFeed(
                    array(
                        'max-results' => $parameters['maxResults'],
                        'fields'      => 'entry(id,title,author,gd:rating,yt:rating,yt:statistics,media:group(media:category(),media:description(),media:thumbnail(@url),yt:duration(@seconds)))',
                        'time'        => $period,
                        'alt'         => 'json',
                        // 'region'      => $this->session->userdata('country'),
                        )
                    )
                ,TRUE);
                break;

            case 'top_rated':
                $result = json_decode(Youtube::getTopRatedVideoFeed(
                    array(
                        'max-results' => $parameters['maxResults'],
                        //'fields'       => '*',
                        'fields'      => 'entry(id,published,title,author,gd:rating,yt:rating,yt:statistics,media:group(media:category(),media:description(),media:thumbnail(@url),yt:duration(@seconds)))',
                        'time'        => $period,
                        'alt'         => 'json',
                        // 'region'      => $this->session->userdata('country'),
                        )
                    )
                ,TRUE);
                break;

            case 'most_viewed':
                $result= Youtube::getPopularVideos(Session::get('country'));
                break;

            /* Search based on query/tag */
            case 'search':
                $result = Youtube::searchVideos($parameters['searchQuery'], $parameters['maxResults'], $parameters['sort'], ['id', 'snippet']);
                break;

            /* Video page with video data and related videos */
            case 'getVideoEntry':
                $result = Youtube::getVideoInfo($parameters['videoId']);
                break;

            case 'getRelatedVideos':
                $result = Youtube::getRelatedVideos($parameters['videoId'], $parameters['maxResults']);
                break;

        }

        #dd($result);

        return $result;
    }

}

/* End of file c_youtube.php */
/* Location: ./application/modules/apis/controllers/c_youtube.php */