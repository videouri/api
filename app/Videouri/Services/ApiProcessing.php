<?php

namespace Videouri\Services;

use Cache;
use Exception;
use Videouri\Services\DailymotionAgent;
use Videouri\Services\VimeoAgent;
use Videouri\Services\YoutubeAgent;
use Videouri\Traits\ApiParsersTrait;

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
        'Youtube',
        // 'Metacafe',
        'Dailymotion',
        'Vimeo'
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
        'search'
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
     * @var int
     */
    public $videoId = null;

    /**
     * @var string
     */
    public $searchQuery = null;

    /**
     * @var int
     */
    public $page = 1;

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
    public $cacheDeactivated = false;

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

        if ($this->timestamp) {
            $apiParameters['timestamp'] = $this->timestamp;
        }

        $apiParametersHash = md5(serialize($apiParameters));

        if (!$apiResponse = Cache::get($apiParametersHash)) {
            if (!in_array_r($this->content, $this->availableContents)) {
                throw new Exception("Error Processing Request.", 1);
            }

            $this->maxResults = (int) $this->maxResults;

            $apiResponse = [];
            foreach ($this->apis as $api) {
                try {
                    if (is_array($this->content)) {
                        foreach ($this->content as $content) {
                            $apiResponse[$content][$api] = $this->getVideos($content, $api);
                        }
                    } else {
                        $apiResponse[$this->content][$api] = $this->getVideos($this->content, $api);
                        // $apiResponse = $this->getVideos($this->content, $api);
                    }
                } catch (Exception $e) {
                }
            }

            // Caching results
            // Cache::put($apiParametersHash, $apiResponse, $this->_periodCachingTime[$this->period]);

            if (!$this->cacheDeactivated) {
                // Set cache to expire in 24 hours
                Cache::put($apiParametersHash, $apiResponse, 1440);
            }
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
        } else {
            if (isset($this->searchQuery)) {
                $characters = array("-", "@");
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
            $apiResponse = $this->getVideos($this->content, $api);
            // Cache::put($cacheVariable, $apiResponse, $this->_cacheTimeout);

            // Set cache to 24 hours
            Cache::put($cacheVariable, $apiResponse, 1440);
        }

        return $apiResponse;
    }

    private function getVideos($content, $api)
    {
        $parameters = [
            'content'    => $content,
            'period'     => $this->period,
            'maxResults' => $this->maxResults
        ];

        if (isset($this->videoId)) {
            $parameters['videoId'] = $this->videoId;
        } elseif (isset($this->searchQuery)) {
            $parameters['page'] = $this->page;
            $parameters['sort'] = $this->sort;
            $parameters['searchQuery'] = $this->searchQuery;
        }

        $api = strtolower($api);
        $apiToRun = $this->{$api};

        return $apiToRun->data($parameters);
    }
}
