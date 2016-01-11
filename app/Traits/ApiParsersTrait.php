<?php

namespace App\Traits;

use App\Entities\Video;
use Exception;
use Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Dailymotion\DailymotionException;

trait ApiParsersTrait
{
    /**
     * [parseApiResult description]
     *
     * @param  string $api
     * @param  array  $videos
     * @param  string $content
     * @return array
     */
    public function parseApiResult($api, $videos, $content = null)
    {
        if (empty($videos)) {
            Log::alert('parseApiResult: ' . $api . ' with empty $videos');
            return [];
        }

        $api = strtolower($api);

        $apiParser = "{$api}Parser";

        if (!is_null($content)) {
            $this->videoContent = $content;
        }

        if (!is_array($videos)) {
            $videos = [
                $videos,
            ];
        }

        $videos = $this->$apiParser($videos);

        if (empty($videos)) {
            return [];
        }

        if (count($videos) === 1) {
            $videos = $videos[0];
        }

        $videos = $this->transformVideos($videos);

        return $videos;
    }

    private function youtubeParser($videos)
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

            if ($this->videoContent) {
                $results[$index]['content'] = $this->videoContent;
            }

            $index++;
        }

        return $results;
    }

    private function dailymotionParser($data)
    {
        $index = 0;
        $results = array();

        // This is because of getVideoInfor
        if (!isset($data['list'])) {
            $data = [
                'list' => [
                    $data
                ]
            ];
        }

        foreach ($data['list'] as $video) {
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


            if ($this->videoContent) {
                $results[$index]['content'] = $this->videoContent;
            }

            $index++;
        }

        return $results;
    }

    private function metacafeParser($data)
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

    private function vimeoParser($videos)
    {
        $index = 0;
        $results = [];

        if (empty($videos) || isset($videos['body']['error'])) {
            throw new NotFoundHttpException('Video not found');
        }

        // // No results returned
        // if (isset($videos['body']['data']) && empty($videos['body']['data'])) {
        //     return [];
        // }

        // $videos = $videos['body'];

        // if (isset($videos['data'])) {
        //     $videos = $videos['data'];
        // }

        if (empty($videos['body'])) {
            return [];
        }

        // This is for getVideoInfo function
        if (!empty($videos['body']) && !isset($videos['body']['data'])) {
            $videos['body'] = [
                'data' => [
                    $videos['body']
                ]
            ];
        }


        foreach ($videos['body']['data'] as $video) {
            $originalId = explode('/', $video['uri'])[2];
            $customId = substr($originalId, 0, 1) . 'v' . substr($originalId, 1);

            $videoObject = new Video;

            $videoObject->provider = 'Vimeo';
            $videoObject->original_id = $originalId;
            $videoObject->custom_id = $customId;
            $videoObject->original_url = 'https://vimeo.com/' . $originalId;

            $videoObject->title = $video['name'];
            $videoObject->description = $video['description'];
            // $videoObject->author     = $video['user']['name'];
            // $videoObject->category   = '';
            $videoObject->thumbnail = $video['pictures']['sizes'][2]['link'];

            $videoObject->rating = $video['metadata']['connections']['likes']['total'];
            $videoObject->duration = $video['duration'];
            $videoObject->views = $video['stats']['plays'];
            $videoObject->tags = $video['stats']['plays'];

            $tags = array();
            if (!empty($video['tags'])) {
                foreach ($video['tags'] as $tag) {
                    $tags[] = $tag['name'];
                }
            }

            $videoObject->tags = $tags;

            $results[$index] = $videoObject;

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

    /**
     * [parseDescription description]
     * @param  [type] $text [description]
     * @return [type]       [description]
     */
    private function parseDescription($text)
    {
        if ($this->content !== 'getVideoEntry') {
            $text = str_limit($text, 90);
        }

        return $text;
    }
}
