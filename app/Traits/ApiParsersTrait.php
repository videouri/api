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

    // /**
    //  * [parseIndividualResult description]
    //  * @param  [type] $data [description]
    //  * @return [type]       [description]
    //  */
    // public function parseIndividualResult($api, $data)
    // {
    //     if (!$data) {
    //         throw new \Exception("Error parsing result from $api. Video might've been deleted");
    //     }

    //     if ($api === 'Vimeo' && isset($data['body']['error'])) {
    //         throw new \Exception("$api: " . $data['body']['error']);
    //     }

    //     $video = [];

    //     if ($api === "dailymotion") {
    //         $httpsUrl = preg_replace("/^http:/i", "https:", $data['url']);
    //         $thumbnailUrl = preg_replace("/^http:/i", "https:", $data['thumbnail_360_url']);

    //         // $video['original_id'] = $data['id'];
    //         $video['url'] = $httpsUrl;
    //         $video['title'] = $data['title'];
    //         $video['description'] = $data['description'];
    //         $video['thumbnail'] = $thumbnailUrl;

    //         // $video['ratings']  = $data['ratings'];
    //         $video['views'] = $data['views_total'];
    //         $video['duration'] = $data['duration'];

    //         $video['tags'] = $data['tags'];
    //     }

    //     // elseif ($api === "Metacafe") {
    //     //     // if (preg_match('/http:\/\/[w\.]*metacafe\.com\/fplayer\/(.*).swf/is', $data['embed'], $match)) {
    //     //     //     $video['swf']['url'] = $data['embed'];
    //     //     //     $video['swf']['api'] = 'mcapiplayer';
    //     //     // }

    //     //     // else {
    //     //     //     $video['embed_html'] = $data['embed'];
    //     //     // }

    //     //     $video['title'] = $data->title;
    //     //     $video['thumbnail'] = 'http://www.metacafe.com/thumb/'.$originalId.'.jpg';

    //     //     $dom = new DOMDocument();
    //     //     $dom->loadHTML($data->description);

    //     //     $xml = simplexml_import_dom($dom);
    //     //     $p   = (string)$xml->body->p;

    //     //     $video['description'] = strstr($p, 'Ranked', true);

    //     //     $tags  = array();
    //     //     $count = count((object)$xml->body->p[1]->a) - 2;
    //     //     for ($index = 2; $index <= $count; $index++) {
    //     //         $tag = (object)$xml->body->p[1]->a[$index];
    //     //         $tag = str_replace(array('News & Events'), '', $tag);
    //     //         $tags[] = $tag;
    //     //     }

    //     //     $video['tags']        = $tags;
    //     //     // $video['related']     = $this->relatedVideos(array('api'=>$api,'id'=>$originalId));
    //     // }

    //     if ($api == "vimeo") {
    //         $data = $data['body'];

    //         $originalId = explode('/', $data['uri'])[2];

    //         $video['url'] = "https://vimeo.com/" . $originalId;
    //         $video['title'] = $data['name'];
    //         $video['description'] = $data['description'];
    //         $video['thumbnail'] = $data['pictures']['sizes'][2]['link'];

    //         // $video['ratings']  = $data['ratings'];
    //         $video['views'] = $data['stats']['plays'];
    //         $video['duration'] = $data['duration'];

    //         $tags = array();
    //         if (!empty($data['tags'])) {
    //             foreach ($data['tags'] as $tag) {
    //                 $tags[] = $tag['name'];
    //             }
    //         }

    //         $video['tags'] = $tags;
    //     }

    //     if ($api == "youtube") {
    //         $seconds = $data->contentDetails->duration;
    //         $totalSeconds = ISO8601ToSeconds($seconds);

    //         $video['url'] = "https://www.youtube.com/watch?v=" . $data->id;
    //         $video['title'] = $data->snippet->title;
    //         $video['description'] = $data->snippet->description;
    //         $video['thumbnail'] = $data->snippet->thumbnails->medium->url;

    //         // $video['ratings']     = $data['gd$rating']['average'];
    //         $video['views'] = $data->statistics->viewCount;
    //         $video['duration'] = $totalSeconds;

    //         $video['tags'] = isset($data->snippet->tags) ? $data->snippet->tags : [];
    //     }

    //     $videoObject = new Video;
    //     foreach ($video as $field => $value) {
    //         $videoObject->$field = $value;
    //     }

    //     dump($video);
    //     dump($videoObject);
    //     die;

    //     return $video;
    // }

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
