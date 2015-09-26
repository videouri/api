<?php

namespace Videouri\Services;

use Cache;
use Log;
use Videouri\Services\DailymotionAgent;
use Videouri\Services\VimeoAgent;
use Videouri\Services\YoutubeAgent;

/**
 * ApiProcessing Class
 *
 * Class containing commons and usefull function to
 * interact with the API used in Videouri APP
 *
 * @category    Libraries
 * @author      Alexandru Budurovici
 * @version     1.0
 */

class ApiProcessing
{
    /**
     * List of available apis to process
     *
     * @var array
     */
    public $apis = array(
        'Youtube',
        // 'Metacafe',
        'Dailymotion',
        'Vimeo'
    );


    /**
     * Available contents
     * 
     * @var array
     */
    private $mixedContents =  array(
                'most_viewed',
                'newest',
                'top_rated',
                'search',
            ),
            $individualContents = array(
                'getVideoEntry',
                'getRelatedVideos',
                'tag'
            );


    /**
     * Variable to be used, to mention what action will be executed
     *
     * @var boolean
     */
    public $content = null;


    /**
     * Array containing available time periods.
     *
     * @var array
     */
    private $validPeriods = array('ever', 'today', 'week', 'month');


    /**
     * Default variable referring to video sorting period
     *
     * @var string
     */
    public $period = 'ever';


    /**
     * Variables to be populated by specific uses
     *
     * @var string
     */
    public $videoId = null,
           $searchQuery = null,
           $page = '',
           $sort = null;


    /**
     * Maximum results from response
     *
     * @var integer
     */
    public $maxResults = 5;


    /**
     * Can be used to force a content to be re-cached
     * by using the function date.
     *
     *  For example:
     *      date('Y-m')   -> Expires in a month
     *      date('Y-m-d') -> Expires in a day
     *
     *  Used in HomeController, to ensure the retrieval of
     *  daily most_viewed videos
     *
     * @var integer
     */
    public $timestamp = null;


    /**
     * Variable to hold the sort parameter, for apiParser,
     * if set.
     * @var boolean
     */
    private $contentForParser = null;


    /**
     * Caching time span and timeout
     * @var array
     */
    private $_periodCachingTime = [
                'ever'  => 86400,  // 1 Day
                'today' => 86400,  // 1 Day
                'week'  => 172800, // 2 Days
                'month' => 259200  // 3 Days
            ],
            $_cacheTimeout = 10800;


    private $dailymotion, $vimeo, $youtube;


    public function __construct(DailymotionAgent $dailymotion, VimeoAgent $vimeo, YoutubeAgent $youtube)
    {
        $this->dailymotion = new DailymotionAgent;
        $this->vimeo       = new VimeoAgent;
        $this->youtube     = new YoutubeAgent;


        if ( ! in_array($this->period, $this->validPeriods)) {
            $this->period = 'today';
        }
    }

    public function mixedCalls()
    {
        $apiParameters = [
            'apis'        => $this->apis,
            'content'     => $this->content,
            'period'      => $this->period,
            'searchQuery' => $this->searchQuery,
            'page'        => $this->page,
            'sort'        => $this->sort
        ];
        // Debugbar::info($apiParameters);
        // Log::info('apiParameters', $apiParameters);
        
        if ($this->timestamp)
            $apiParameters['timestamp'] = $this->timestamp;

        $apiParametersHash = md5(serialize($apiParameters));
        
        // Log::info('apiParametersHash: ' . $apiParametersHash);
        // Debugbar::info($apiParametersHash);

        if (!$apiResponse = Cache::get($apiParametersHash)) {
            // if (!is_array($this->content) && !in_array($this->content, $this->availableContents)) {
            if (!in_array_r($this->content, $this->mixedContents)) {
                throw new Exception("Error Processing Request.", 1);
            }

            $this->maxResults = (int) $this->maxResults;

            $apiResponse = array();
            foreach ($this->apis as $api) {
                try {
                    if (is_array($this->content)) {
                        foreach ($this->content as $content) {
                           $apiResponse[$content][$api] = self::getContent($content, $api);
                        }
                    } else {
                        $apiResponse[$this->content][$api] = self::getContent($this->content, $api);
                        // $apiResponse = self::getContent($this->content, $api);
                    }
                }

                catch (ParameterException $e) {
                    // var_dump($e);
                    # echo "Encountered an API error -- code {$e->getCode()} - {$e->getMessage()}";
                }

                catch (Exception $e) {
                    // var_dump($e);
                    #echo "Some other Exception was thrown -- code {$e->getCode()} - {$e->getMessage()}";
                }
            }

            // dd($apiResponse);

            // Caching results
            // Cache::put($apiParametersHash, $apiResponse, $this->_periodCachingTime[$this->period]);
            
            // Set cache to expire in 24 hours
            Cache::put($apiParametersHash, $apiResponse, 1440); 
        }

        return $apiResponse;
    }


    public function individualCall($api)
    {
        if (!in_array_r($this->content, $this->individualContents)) {
            throw new Exception("Error Processing Request.", 1);
        }

        if (isset($this->videoId)) {
            if ($api == 'Metacafe') {
                $videoId = explode('/', $this->videoId);
                $this->videoId = $videoId[0];
            }

            $dynamicVariable = "video_{$this->videoId}";
            if ($this->content == 'getRelatedVideos') {
                $dynamicVariable = "relatedTo_{$this->videoId}";
            }

            $cacheVariable = "{$api}_{$dynamicVariable}";
        }

        else {
            if (isset($this->searchQuery)) {
                $characters  = array("-", "@");
                $searchQuery = str_replace($characters, '', $this->searchQuery);

                $dynamicVariable = "searchQuery_{$searchQuery}";
                if (isset($parameters['page'])) {
                    $dynamicVariable .= "_page{$this->page}";
                }
            }

            // elseif (isset($parameters['maxResults'])) {
            //     $dynamicVariable = "{$parameters['maxResults']}_results";
            // }

            else {
                $dynamicVariable = "{$this->content}";
            }
            
            $cacheVariable = "{$api}_{$dynamicVariable}_{$this->period}";
        }

        $apiResponse = Cache::get($cacheVariable);

        if (!$apiResponse) {
            $apiResponse = self::getContent($this->content, $api);
            // Cache::put($cacheVariable, $apiResponse, $this->_cacheTimeout);
            
            // Set cache to 24 hours
            Cache::put($cacheVariable, $apiResponse, 1440);
        }

        return $apiResponse;
    }


    private function getContent($content, $api)
    {
        $parameters = array(
                            'content'    => $content,
                            'period'     => $this->period,
                            'maxResults' => $this->maxResults,
                        );

        if (isset($this->videoId)) {
            $parameters['videoId'] = $this->videoId;
        }
        elseif (isset($this->searchQuery)) {
            $parameters['page']        = $this->page;
            $parameters['sort']        = $this->sort;
            $parameters['searchQuery'] = $this->searchQuery;
        }

        $api = strtolower($api);
        $apiToRun = $this->{$api};
        // $youtube = new YoutubeAgent;
        return $apiToRun->data($parameters);
    }



    ////     ////
    // PARSERS //
    ////     ////
    public function parseApiResult($api = null, $data, $specificContent = null)
    {
        $apiParser = "{$api}Parser";

        if (!is_null($specificContent)) {
            $this->contentForParser = $specificContent;
        }

        return $this->$apiParser($data);
    }

    private function YoutubeParser($videos)
    {
        $i = 0;
        $results = array();
        
        if (empty($videos)) {
            return $results;
        }

        foreach ($videos as $video) {
            // echo "<pre>";
            // var_dump($video);
            // echo "<hr/>";

            $videoId = is_object($video->id) ? $video->id->videoId : $video->id;
            $id = substr($videoId, 0, 1) . 'y' . substr($videoId, 1);

            $duration = $views = 0;
            if (isset($video->statistics) && isset($video->statistics->viewCount))
                $views = $video->statistics->viewCount;

            if (isset($video->contentDetails) && isset($video->contentDetails->duration)) {
                $seconds = $video->contentDetails->duration;
                $duration = ISO8601ToSeconds($seconds);
            }

            $results[$i] = array(
                'url'         => url('video/'.$id),
                'title'       => $video->snippet->title,
                'description' => self::parseDescription($video->snippet->description),
                // 'author'   => $video['author'][0]['name']['$t'],
                // 'category'    => [],
                'thumbnail'   => $video->snippet->thumbnails->medium->url,
                
                'rating'      => isset($video->rating) ? $video->rating : 0,
                'views'       => $views,
                'duration'    => $duration,
                
                'source'      => 'Youtube',
            );

            $i++;
            // dd($results);
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
            $url = $match[1].'/'.$match[2];
            $url = url('video/'.substr($url,0,1).'d'.substr($url,1));

            $thumbnailUrl = preg_replace("/^http:/i", "https:", $video['thumbnail_360_url']);

            $results[$i] = array(
                'url'         => $url,
                'title'       => $video['title'],
                'description' => self::parseDescription($video['description']),
                // 'author'      => '',
                // 'category'      => '',
                'thumbnail'   => $thumbnailUrl,

                'rating'      => $video['rating'],
                'duration'    => $video['duration'],
                'views'       => $video['views_total'],

                'source'      => 'Dailymotion',
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

        if (!$data) return false;

        foreach ($data->channel->item as $video) {
            $video = (array) $video;
            preg_match('/http:\/\/[w\.]*metacafe\.com\/watch\/([^?&#"\']*)/is', $video['link'], $match);
            $id  = substr($match[1],0,-1);
            $url = url('video/'.substr($id,0,1).'M'.substr($id,1));
            
            $results['Metacafe'][$i] = array(
                'url'         => $url,
                'title'       => $video['title'],
                'description' => self::parseDescription($video['title']),
                // 'author'      => $video['author'],
                // 'category'    => $video['category'],
                'thumbnail'   => "http://www.metacafe.com/thumb/{$video['id']}.jpg",

                'rating'      => isset($video['rank']) ? $video['rank'] : 0,
                'views'       => 0,

                'source'      => 'Metacafe',
            );

            if ($i === $this->maxResults) break;
            
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
            $id     = substr($origId, 0, 1) . 'v' . substr($origId, 1);

            $results[$i] = array(
                'url'         => url('video/'.$id),
                'title'       => $video['name'],
                'description' => self::parseDescription($video['description']),
                // 'author'      => $video['user']['name'],
                // 'category'    => '',
                'thumbnail'   => $video['pictures']['sizes'][2]['link'],

                'rating'      => $video['metadata']['connections']['likes']['total'],
                'duration'    => $video['duration'],
                'views'       => $video['stats']['plays'],

                'source'      => 'Vimeo',
            );

            if ($i === $this->maxResults) break;
            
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
        $video = [];

        if ($api === "Dailymotion") {
            $httpsUrl     = preg_replace("/^http:/i", "https:", $data['url']);
            $thumbnailUrl = preg_replace("/^http:/i", "https:", $data['thumbnail_360_url']);

            $video['url']         = $httpsUrl;
            $video['title']       = $data['title'];
            $video['description'] = $data['description'];
            $video['thumbnail']   = $thumbnailUrl;
            
            // $video['ratings']  = $data['ratings'];
            $video['views']       = $data['views_total'];
            $video['duration']    = $data['duration'];
            
            $video['tags']        = $data['tags'];
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

            $video['url']         = "https://vimeo.com/".$origId;
            $video['title']       = $data['name'];
            $video['description'] = $data['description'];
            $video['thumbnail']   = $data['pictures']['sizes'][2]['link'];
            
            // $video['ratings']  = $data['ratings'];
            $video['views']       = $data['stats']['plays'];
            $video['duration']    = $data['duration'];

            $tags = array();
            if (!empty($data['tags'])) {
                foreach($data['tags'] as $tag) {
                    $tags[] = $tag['name'];
                }
            }
            
            $video['tags']       = $tags;
        }

        elseif ($api == "Youtube") {
            $seconds = $data->contentDetails->duration;
            $totalSeconds = ISO8601ToSeconds($seconds);

            $video['url']         = "https://www.youtube.com/watch?v=" . $data->id;
            $video['title']       = $data->snippet->title;
            $video['description'] = $data->snippet->description;
            $video['thumbnail']   = $data->snippet->thumbnails->medium->url;
            
            // $video['ratings']     = $data['gd$rating']['average'];
            $video['views']       = $data->statistics->viewCount;
            $video['duration']    = $totalSeconds;
            
            $video['tags']        = isset($data->snippet->tags) ? $data->snippet->tags : [];
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
        if ($this->content === 'getVideoEntry') {
            return $text;
        } else {
            return str_limit($text, 90);
        }
    }
}