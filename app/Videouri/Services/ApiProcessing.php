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
    private $mixedContents = array(
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
    public $videoId = null;
    public $searchQuery = null;
    public $page = '';
    public $sort = null;

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
     * Don't execute a real call to the APIs if set to true
     * @var boolean
     */
    public $fakeContent = false;

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
        // Debugbar::info($apiParameters);
        // Log::info('apiParameters', $apiParameters);

        if ($this->timestamp) {
            $apiParameters['timestamp'] = $this->timestamp;
        }

        $apiParametersHash = md5(serialize($apiParameters));

        // Log::info('apiParametersHash: ' . $apiParametersHash);
        // Debugbar::info($apiParametersHash);

        if (!$apiResponse = Cache::get($apiParametersHash)) {
            if (!in_array_r($this->content, $this->mixedContents)) {
                throw new Exception("Error Processing Request.", 1);
            }

            $this->maxResults = (int) $this->maxResults;

            $apiResponse = array();
            foreach ($this->apis as $api) {
                try {
                    if (is_array($this->content)) {
                        foreach ($this->content as $content) {
                            $apiResponse[$content][$api] = $this->getContent($content, $api);
                        }
                    } else {
                        $apiResponse[$this->content][$api] = $this->getContent($this->content, $api);
                        // $apiResponse = $this->getContent($this->content, $api);
                    }
                } catch (Exception $e) {
                }
            }

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
            $apiResponse = $this->getContent($this->content, $api);
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
            'maxResults' => $this->maxResults
        );

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
