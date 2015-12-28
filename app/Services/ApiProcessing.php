<?php

namespace App\Services;

use Exception;

use Cache;
use Auth;

use App\Services\DailymotionAgent;
use App\Services\VimeoAgent;
use App\Services\YoutubeAgent;
use App\Traits\ApiParsersTrait;
use App\Entities\Video;
use App\Transformers\VideoTransformer;

use League\Fractal\Manager as FractalManager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\ArraySerializer;

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
        'vimeo',
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
        'getRelatedVideos',
        'tag',
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
     * @var string
     */
    public $country = null;

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

        $this->cacheDeactivated = env('DEACTIVATE_CACHING', false);
    }

    public function mixedCalls($content)
    {
        $apiParameters = [
            'apis'    => $this->apis,
            'content' => $content,
            'period'  => $this->period,
            // 'searchQuery' => $this->searchQuery,
            // 'page'        => $this->page,
            // 'sort'        => $this->sort
        ];

        if ($this->country) {
            $apiParameters['country'] = $this->country;
        }

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
    public function searchVideos($searchQuery, $page, $sort, $period, $maxResults = 10)
    {
        $parameters = [
            'searchQuery' => $searchQuery,
            'page'        => $page,
            'sort'        => $sort,
            'period'      => $period,
            'maxResults'  => $maxResults,
        ];

        $results = [];

        $apis = $this->apis;
        foreach ($apis as $api) {
            $apiToRun = $this->{$api};
            $videos = $apiToRun->searchVideos($parameters);
            $videos = $this->parseApiResult($api, $videos);
            $results[$api] = $videos;
        }

        return $results;
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

        // If it exists in the database, get the info from there and transform it
        // via VideoTransformer
        if ($video = Video::where('original_id', '=', $videoId)->first()) {
            if ($video->dmca_claim) {
                return abort(404);
            }

            $video = $this->transformVideos($video);
        } else {
            $video = $apiToRun->getVideoInfo($videoId);

            if ($parseResult) {
                $video = $this->parseApiResult($api, $video);
            }
        }

        return $video;
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
            'maxResults' => $this->maxResults,
        ];

        if ($this->country) {
            $parameters['country'] = $this->country;
        }

        $apiToRun = $this->{$api};

        try {
            return $apiToRun->getContent($content, $parameters);
        } catch (Exception $e) {
            // TODO: Mail the error
            dump('getContent');
            dump($e);
        }
    }

    /**
     * [transformVideos description]
     * @return [type] [description]
     */
    public function transformVideos($videos)
    {
        if (is_array($videos) && count($videos) > 0) {
            $resource = new Collection($videos, new VideoTransformer());
        } else {
            $resource = new Item($videos, new VideoTransformer());
        }

        // Create a top level instance somewhere
        $fractalManager = new FractalManager();
        // $fractalManager->setSerializer(new ArraySerializer());

        $videos = $fractalManager->createData($resource)->toArray();

        return $videos['data'];
    }
}
