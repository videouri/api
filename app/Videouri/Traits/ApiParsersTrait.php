<?php

namespace Videouri\Traits;

trait ApiParsersTrait
{
    public function parseApiResult($api, $videos, $specificContent = null)
    {
        $apiParser = "{$api}Parser";

        if (!is_null($specificContent)) {
            $this->contentForParser = $specificContent;
        }

        return $this->$apiParser($videos);
    }

    private function YoutubeParser($videos)
    {
        $i = 0;
        $results = array();

        if (empty($videos)) {
            return $results;
        }

        foreach ($videos as $video) {
            $videoId = is_object($video->id) ? $video->id->videoId : $video->id;
            $id = substr($videoId, 0, 1) . 'y' . substr($videoId, 1);

            $duration = $views = 0;
            if (isset($video->statistics) && isset($video->statistics->viewCount)) {
                $views = $video->statistics->viewCount;
            }

            if (isset($video->contentDetails) && isset($video->contentDetails->duration)) {
                $seconds = $video->contentDetails->duration;
                $duration = ISO8601ToSeconds($seconds);
            }

            $results[$i] = array(
                'url' => url('video/' . $id),
                'title' => $video->snippet->title,
                'description' => self::parseDescription($video->snippet->description),
                // 'author'   => $video['author'][0]['name']['$t'],
                // 'category'    => [],
                'thumbnail' => $video->snippet->thumbnails->medium->url,

                'rating' => isset($video->rating) ? $video->rating : 0,
                'views' => $views,
                'duration' => $duration,

                'source' => 'Youtube',
            );

            $i++;
        }

        if ($content = $this->contentForParser) {
            // $results[$content]['Youtube'] = $results['Youtube'];
            // unset($results['Youtube']);

            return array($content => $results);
        }

        return $results;
    }

    private function DailymotionParser($data)
    {
        $i = 0;
        $results = array();

        foreach ($data['list'] as $video) {
            preg_match('@video/([^_]+)_([^/]+)@', $video['url'], $match);
            $url = $match[1] . '/' . $match[2];
            $url = url('video/' . substr($url, 0, 1) . 'd' . substr($url, 1));

            $thumbnailUrl = preg_replace("/^http:/i", "https:", $video['thumbnail_360_url']);

            $results[$i] = array(
                'url' => $url,
                'title' => $video['title'],
                'description' => self::parseDescription($video['description']),
                // 'author'      => '',
                // 'category'      => '',
                'thumbnail' => $thumbnailUrl,

                'rating' => $video['rating'],
                'duration' => $video['duration'],
                'views' => $video['views_total'],

                'source' => 'Dailymotion',
            );

            $i++;
        }

        if ($content = $this->contentForParser) {
            // $results[$content]['Dailymotion'] = $results['Dailymotion'];
            // unset($results['Dailymotion']);

            return array($content => $results);
        }

        return $results;
    }

    private function MetacafeParser($data)
    {
        $i = 1;
        $results = array();

        if (!$data) {
            return false;
        }

        foreach ($data->channel->item as $video) {
            $video = (array) $video;
            preg_match('/http:\/\/[w\.]*metacafe\.com\/watch\/([^?&#"\']*)/is', $video['link'], $match);
            $id = substr($match[1], 0, -1);
            $url = url('video/' . substr($id, 0, 1) . 'M' . substr($id, 1));

            $results['Metacafe'][$i] = array(
                'url' => $url,
                'title' => $video['title'],
                'description' => self::parseDescription($video['title']),
                // 'author'      => $video['author'],
                // 'category'    => $video['category'],
                'thumbnail' => "http://www.metacafe.com/thumb/{$video['id']}.jpg",

                'rating' => isset($video['rank']) ? $video['rank'] : 0,
                'views' => 0,

                'source' => 'Metacafe',
            );

            if ($i === $this->maxResults) {
                break;
            }

            $i++;

        }

        if (isset($results['Metacafe']) && $content = $this->contentForParser) {
            $results[$content]['Metacafe'] = $results['Metacafe'];
            unset($results['Metacafe']);
        }

        return $results;
    }

    private function VimeoParser($data)
    {
        $i = 0;
        $results = array();

        if (empty($data) || isset($data['body']['error'])) {
            return $results;
        }

        foreach ($data['body']['data'] as $video) {
            $origId = explode('/', $video['uri'])[2];
            $id = substr($origId, 0, 1) . 'v' . substr($origId, 1);

            $results[$i] = array(
                'url' => url('video/' . $id),
                'title' => $video['name'],
                'description' => self::parseDescription($video['description']),
                // 'author'      => $video['user']['name'],
                // 'category'    => '',
                'thumbnail' => $video['pictures']['sizes'][2]['link'],

                'rating' => $video['metadata']['connections']['likes']['total'],
                'duration' => $video['duration'],
                'views' => $video['stats']['plays'],

                'source' => 'Vimeo',
            );

            if ($i === $this->maxResults) {
                break;
            }

            $i++;

        }

        if (isset($results['Vimeo']) && $content = $this->contentForParser) {
            $results[$content]['Vimeo'] = $results['Vimeo'];
            unset($results['Vimeo']);
        }

        return $results;
    }

    /**
     * [parseIndividualResult description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function parseIndividualResult($api, $data)
    {
        if (!$data) {
            throw new \Exception("Error parsing result from $api. Video might've been deleted");
        }

        if ($api === 'Vimeo' && isset($data['body']['error'])) {
            throw new \Exception("$api: " . $data['body']['error']);
        }

        $video = [];

        if ($api === "Dailymotion") {
            $httpsUrl = preg_replace("/^http:/i", "https:", $data['url']);
            $thumbnailUrl = preg_replace("/^http:/i", "https:", $data['thumbnail_360_url']);

            $video['url'] = $httpsUrl;
            $video['title'] = $data['title'];
            $video['description'] = $data['description'];
            $video['thumbnail'] = $thumbnailUrl;

            // $video['ratings']  = $data['ratings'];
            $video['views'] = $data['views_total'];
            $video['duration'] = $data['duration'];

            $video['tags'] = $data['tags'];
        }

        // elseif ($api === "Metacafe") {
        //     // if (preg_match('/http:\/\/[w\.]*metacafe\.com\/fplayer\/(.*).swf/is', $data['embed'], $match)) {
        //     //     $video['swf']['url'] = $data['embed'];
        //     //     $video['swf']['api'] = 'mcapiplayer';
        //     // }

        //     // else {
        //     //     $video['embed_html'] = $data['embed'];
        //     // }

        //     $video['title'] = $data->title;
        //     $video['thumbnail'] = 'http://www.metacafe.com/thumb/'.$origId.'.jpg';

        //     $dom = new DOMDocument();
        //     $dom->loadHTML($data->description);

        //     $xml = simplexml_import_dom($dom);
        //     $p   = (string)$xml->body->p;

        //     $video['description'] = strstr($p, 'Ranked', true);

        //     $tags  = array();
        //     $count = count((object)$xml->body->p[1]->a) - 2;
        //     for ($i = 2; $i <= $count; $i++) {
        //         $tag = (object)$xml->body->p[1]->a[$i];
        //         $tag = str_replace(array('News & Events'), '', $tag);
        //         $tags[] = $tag;
        //     }

        //     $video['tags']        = $tags;
        //     // $video['related']     = $this->_relatedVideos(array('api'=>$api,'id'=>$origId));
        // }

        elseif ($api == "Vimeo") {
            $data = $data['body'];

            $origId = explode('/', $data['uri'])[2];

            $video['url'] = "https://vimeo.com/" . $origId;
            $video['title'] = $data['name'];
            $video['description'] = $data['description'];
            $video['thumbnail'] = $data['pictures']['sizes'][2]['link'];

            // $video['ratings']  = $data['ratings'];
            $video['views'] = $data['stats']['plays'];
            $video['duration'] = $data['duration'];

            $tags = array();
            if (!empty($data['tags'])) {
                foreach ($data['tags'] as $tag) {
                    $tags[] = $tag['name'];
                }
            }

            $video['tags'] = $tags;
        } elseif ($api == "Youtube") {
            $seconds = $data->contentDetails->duration;
            $totalSeconds = ISO8601ToSeconds($seconds);

            $video['url'] = "https://www.youtube.com/watch?v=" . $data->id;
            $video['title'] = $data->snippet->title;
            $video['description'] = $data->snippet->description;
            $video['thumbnail'] = $data->snippet->thumbnails->medium->url;

            // $video['ratings']     = $data['gd$rating']['average'];
            $video['views'] = $data->statistics->viewCount;
            $video['duration'] = $totalSeconds;

            $video['tags'] = isset($data->snippet->tags) ? $data->snippet->tags : [];
        }

        return $video;
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
