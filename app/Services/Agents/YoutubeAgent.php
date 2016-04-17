<?php

namespace App\Services\Agents;

use Alaouy\Youtube\Youtube;
use App\Interfaces\ApiAgentInterface;
use App\Entities\Video;
use Session;

/**
 * Class YoutubeAgent
 * @package App\Services\Agents
 */
class YoutubeAgent implements ApiAgentInterface
{
    /**
     * YoutubeAgent constructor.
     */
    public function __construct()
    {
        $this->youtube = new Youtube(config('youtube.KEY'));
    }

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
        //     $results = json_decode($this->youtube->getMostRecentVideoFeed(
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
        //     $results = json_decode($this->youtube->getTopRatedVideoFeed(
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
        //         $results = $this->youtube->getPopularVideos(Session::get('country'));
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
                $results = json_decode($this->youtube->getMostRecentVideoFeed([
                    'max-results' => $parameters['maxResults'],
                    'fields'      => 'entry(id,title,author,gd:rating,yt:rating,yt:statistics,media:group(media:category(),media:description(),media:thumbnail(@url),yt:duration(@seconds)))',
                    'time'        => $period,
                    'alt'         => 'json',
                    // 'region'      => $country,
                ]), true);
                break;

            case 'top_rated':
                $results = json_decode($this->youtube->getTopRatedVideoFeed([
                    'max-results' => $parameters['maxResults'],
                    //'fields'       => '*',
                    'fields'      => 'entry(id,published,title,author,gd:rating,yt:rating,yt:statistics,media:group(media:category(),media:description(),media:thumbnail(@url),yt:duration(@seconds)))',
                    'time'        => $period,
                    'alt'         => 'json',
                    // 'region'      => $this->session->userdata('country'),
                ]), true);
                break;

            case 'most_viewed':
                $results = $this->youtube->getPopularVideos($country);
                break;
        }

        return $results;
    }

    /**
     * @param $parameters
     * @return \StdClass
     */
    public function searchVideos($parameters)
    {
        $results = $this->youtube->searchVideos(
            $parameters['searchQuery'],
            $parameters['maxResults'],
            $parameters['sort'],
            ['id', 'snippet']
        );

        return $results;
    }

    /**
     * @param $videoId
     * @return \StdClass
     */
    public function getVideoInfo($videoId)
    {
        return $this->youtube->getVideoInfo($videoId);
    }

    /**
     * @param $videoId
     * @param int $maxResults
     * @return array
     */
    public function getRelatedVideos($videoId, $maxResults = 10)
    {
        return $this->youtube->getRelatedVideos($videoId, $maxResults);
    }

    /**
     * Parse data from source
     *
     * @param $videos
     * @param null|string $videoContent
     * @throws NotFoundHttpException
     *
     * @return array
     */
    public function parseVideos($videos, $videoContent = null)
    {
        $index = 0;
        $results = array();

        if (empty($videos) && is_null($videos)) {
            throw new NotFoundHttpException('Video not found');
        }

        foreach ($videos as $video) {
            $originalId = is_object($video->id) ? $video->id->videoId : $video->id;
            $customId = substr($originalId, 0, 1) . 'y' . substr($originalId, 1);

            $duration = $views = 0;
            if (isset($video->statistics) && isset($video->statistics->viewCount)) {
                $views = $video->statistics->viewCount;
            }

            if (isset($video->contentDetails) && isset($video->contentDetails->duration)) {
                $seconds = $video->contentDetails->duration;
                $duration = ISO8601ToSeconds($seconds);
            }

            $videoObject = new Video;

            $videoObject->provider = 'Youtube';
            $videoObject->original_id = $originalId;
            $videoObject->custom_id = $customId;
            $videoObject->original_url = 'https://www.youtube.com/watch?v=' . $originalId;

            $videoObject->title = $video->snippet->title;
            $videoObject->description = $video->snippet->description;
            // $videoObject->author   = $video['author'][0]['name']['$t'];
            // $videoObject->category  = [];
            $videoObject->thumbnail = $video->snippet->thumbnails->medium->url;

            $videoObject->rating = isset($video->rating) ? $video->rating : 0;
            $videoObject->views = $views;
            $videoObject->duration = $duration;
            $videoObject->tags = isset($video->snippet->tags) ? $video->snippet->tags : [];

            $results[$index] = $videoObject;

            if ($videoContent !== null) {
                $results[$index]['content'] = $videoContent;
            }

            $index++;
        }

        return $results;
    }
}
