<?php

namespace App\Services;

use Cache;
use Exception;
use App\Services\DailymotionAgent;
use App\Services\VimeoAgent;
use App\Services\YoutubeAgent;
use App\Traits\ApiParsersTrait;

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
    use ApiParsersTrait;

    /**
     * List of available apis to process
     *
     * @var array
     */
    public $apis = [
        'youtube',
        // 'metacafe',
        'dailymotion',
        'vimeo'
    ];

    /**
     * Available contents
     *
     * @var array
     */
    private $availableContents = array(
        'most_viewed',
        'newest',
        'top_rated',
    );

    private $individualContents = array(
        'getVideoEntry',
        'getRelatedVideos',
        'tag'
    );

    /**
     * Array containing available time periods.
     *
     * @var array
     */
    private $validPeriods = ['ever', 'today', 'week', 'month'];

    //////////////////////////////
    // Parameters for Api calls //
    //////////////////////////////

    /**
     * @var string
     */
    public $sort = null;

    /**
     * Sort period
     *
     * @var string
     */
    public $period = 'ever';

    /**
     * @uses $availableContents
     * @uses $individualContents
     * @var boolean
     */
    public $content = null;

    /**
     * @var int
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
     * @var int
     */
    public $timestamp = null;

    //////////////////////////
    // Behavioural settings //
    //////////////////////////
    /**
     * @uses $avialableContents
     * @var string
     */
    private $videoContent = null;

    /**
     * Don't execute a real call to the APIs if set to true
     * @var boolean
     */
    public $fakeContent = false;

    /**
     * To cache or not to cache
     * @var boolean
     */
    public $cacheDeactivated = true;

    /**
     * Caching time span and timeout
     * @var array
     */
    // private $_periodCachingTime = [
    //     'ever' => 86400, // 1 Day
    //     'today' => 86400, // 1 Day
    //     'week' => 172800, // 2 Days
    //     'month' => 259200, // 3 Days
    // ],
    // $_cacheTimeout = 10800;

    /////////////////
    // API Globals //
    /////////////////
    private $dailymotion;
    private $vimeo;
    private $youtube;

    public function __construct(DailymotionAgent $dailymotion, VimeoAgent $vimeo, YoutubeAgent $youtube)
    {
        $this->dailymotion = $dailymotion;
        $this->vimeo = $vimeo;
        $this->youtube = $youtube;

        if (!in_array($this->period, $this->validPeriods)) {
            $this->period = 'today';
        }
    }

    public function mixedCalls($content)
    {
        $apiParameters = [
            'apis'        => $this->apis,
            'content'     => $content,
            'period'      => $this->period,
            // 'searchQuery' => $this->searchQuery,
            // 'page'        => $this->page,
            // 'sort'        => $this->sort
        ];

        if ($this->timestamp) {
            $apiParameters['timestamp'] = $this->timestamp;
        }

        $apiParametersHash = md5(serialize($apiParameters));

        if (!$apiResponse = Cache::get($apiParametersHash)) {
            if (!in_array_r($content, $this->availableContents)) {
                throw new Exception("Error Processing Request.", 1);
            }

            $this->maxResults = (int) $this->maxResults;

            $apiResponse = [];
            foreach ($this->apis as $api) {
                if (is_array($content)) {
                    foreach ($content as $c) {
                        $apiResponse[$c][$api] = $this->getContent($c, $api);
                    }
                } else {
                    $apiResponse[$content][$api] = $this->getContent($content, $api);
                }

                // Caching results
                // Cache::put($apiParametersHash, $apiResponse, $this->_periodCachingTime[$this->period]);

                if (!$this->cacheDeactivated) {
                    // Set cache to expire in 24 hours
                    Cache::put($apiParametersHash, $apiResponse, 1440);
                }
            }
        }

        return $apiResponse;
    }

    /**
     * [searchVideos description]
     * @param  [type] $query [description]
     * @param  [type] $page  [description]
     * @param  [type] $sort  [description]
     * @return [type]        [description]
     */
    public function searchVideos($query, $page, $sort)
    {

    }

    /**
     * [getVideoInfo description]
     *
     * @param  string  $api
     * @param  string  $videoId
     * @param  boolean $parseResult
     * @return array|object
     */
    public function getVideoInfo($api, $videoId, $parseResult = true)
    {
        $apiToRun = $this->{$api};

        $videoData = $apiToRun->getVideoInfo($videoId);

        if ($parseResult) {
            $videoData = $this->parseApiResult($api, $videoData);
            // $videoData = $this->parseIndividualResult($api, $videoData);
        }

        return $videoData;
    }

    /**
     * [getContent description]
     * @param  [type] $content [description]
     * @param  [type] $api     [description]
     * @return [type]          [description]
     */
    private function getContent($content, $api)
    {
        $parameters = [
            'period'     => $this->period,
            'maxResults' => $this->maxResults
        ];

        $apiToRun = $this->{$api};

        try {
            return $apiToRun->getContent($content, $parameters);
        } catch (Exception $e) {
            // TODO: Mail the error
            dump('getContent');
            dump($e);
        }
    }
}
