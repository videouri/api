<?php

namespace App\Services;

use Cache;
use Session;

use App\Entities\Video;
use App\Services\Agents\YoutubeAgent;
use App\Services\Agents\DailymotionAgent;
use App\Services\Agents\VimeoAgent;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use DailymotionApiException;

/**
 * Class ApiFetcher
 * @package App\Services
 */
final class ApiFetcher extends ApiManager
{
    /**
     * List of available apis to process
     * @var array
     */
    public $apis = [
        'Youtube',
        'Vimeo',
        'Dailymotion',
        // 'Metacafe',
    ];

    /**
     * Available contents
     * @var array
     */
    protected static $availableContents = ['most_viewed', 'newest', 'top_rated'];

    /**
     * Array containing available time periods.
     * @var array
     */
    protected static $validPeriods = ['ever', 'today', 'week', 'month'];

    //////////////////////////////
    // Parameters for Api calls //
    //////////////////////////////

    /**
     * @var string
     */
    public $sort;

    /**
     * @var int
     */
    public $page = 1;

    /**
     * Sort period
     *
     * @var string
     */
    public $period = 'ever';

    /**
     * @uses $availableContents
     * @var boolean
     */
    public $content;

    /**
     * @var int
     */
    public $maxResults = 5;

    /**
     * @var string
     */
    public $country;

    /**
     * Variable that alters the parameters hash,
     * to ensure the retrieval of data for a specific period.
     *
     * For example, it can be used to ensure the retrieval of
     * daily most_viewed videos
     *
     *  Usage
     *      date('Y-m')   -> Expires in a month
     *      date('Y-m-d') -> Expires in a day
     *
     *  Default value = Y-m-d
     *
     * @var string
     */
    public $timestamp;

    //////////////////////////
    // Behavioural settings //
    //////////////////////////

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

    ////////////////
    // API Agents //
    ////////////////

    /**
     * @var DailymotionAgent
     */
    private $dailymotion;

    /**
     * @var VimeoAgent
     */
    private $vimeo;

    /**
     * @var YoutubeAgent
     */
    private $youtube;

    /**
     * ApiManager constructor.
     */
    public function __construct()
    {
        $this->dailymotion = new DailymotionAgent();
        $this->vimeo = new VimeoAgent();
        $this->youtube = new YoutubeAgent();

        if (!in_array($this->period, self::$validPeriods)) {
            $this->period = 'today';
        }

        $this->country = Session::get('country');
        $this->timestamp = date('Y-m-d');
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
            if (!in_array_r($content, self::$availableContents)) {
                throw new Exception('What is this? Exception');
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

                // Set cache to expire in 24 hours
                Cache::put($apiParametersHash, $apiResponse, env('API_CACHE_EXPIRY_TIME'));
            }
        }

        return $apiResponse;
    }

    /**
     * Meta search for video content
     *
     * @param  string $searchQuery
     * @return array
     */
    public function searchVideos($searchQuery)
    {
        $parameters = [
            'searchQuery' => $searchQuery,
        ];

        $parameters['page'] = $this->page;

        if ($this->sort !== null) {
            $parameters['sort'] = $this->sort;
        }

        if ($this->period !== null) {
            $parameters['period'] = $this->period;
        }

        $parameters['maxResults'] = $this->maxResults;

        $results = [];

        foreach ($this->apis as $api) {
            $apiAgent = $this->getAgent($api);

            $videos = $apiAgent->searchVideos($parameters);
            $videos = $this->parseResults($api, $videos);

            $results[$api] = $videos;
        }

        return $results;
    }

    /**
     * [getVideoInfo description]
     *
     * @param  string $api
     * @param  string $videoId
     * @param  bool $parseResult
     * @return mixed
     */
    public function getVideoInfo($api, $videoId, $parseResult = true)
    {
        // If it exists in the database, get the info from there and transform it
        // via VideoTransformer
        if ($video = Video::where('original_id', '=', $videoId)->first()) {
            if ($video->dmca_claim) {
                return abort(404);
            }

            $video = $this->transformVideos($video);
        } else {
            try {
                $apiAgent = $this->getAgent($api);
                $video = $apiAgent->getVideoInfo($videoId);

                if ($parseResult) {
                    $video = $this->parseResults($api, $video);
                }
            } catch (NotFoundHttpException $e) {
                abort(404);
            } catch (DailymotionApiException $e) {
                abort(404);

                // @TODO Log $e->getCode() > 404
            } catch (\Exception $e) {
                dump('parseResults');
                dump($e);
            }
        }

        return $video;
    }

    /**
     * Retrieve related videos for a given video
     *
     * @param  string $api
     * @param  string $videoId
     * @return array
     */
    public function getRelatedVideos($api, $videoId)
    {
        $apiAgent = $this->getAgent($api);
        $videos = $apiAgent->getRelatedVideos($videoId, $maxResults = 6);

        if (empty($videos)) {
            return [];
        }

        $videos = $apiAgent->parseVideos($videos, $content = 'related');

        return $videos;
    }

    /**
     * @param $api
     * @param $videos
     * @param null $content
     * @return array
     */
    public function parseResults($api, $videos, $content = null)
    {
        if (empty($videos)) {
            Log::alert('parseResults: ' . $api . ' with empty $videos');
            return [];
        }

        if (!is_null($content)) {
            $this->videoContent = $content;
        }

        if (!is_array($videos)) {
            $videos = [
                $videos,
            ];
        }

        $apiAgent = $this->getAgent($api);
        $videos = $apiAgent->parseVideos($videos);

        if (empty($videos)) {
            return [];
        }

        if (count($videos) === 1) {
            $videos = $videos[0];
        }

        $videos = $this->transformVideos($videos);

        return $videos;
    }

    /**
     * Get content for a specific keyword, like, most_viewed
     *
     * @param  string $content
     * @param  string $api
     * @return array
     */
    private function getContent($content, $api)
    {
        $parameters = [
            'period' => $this->period,
            'maxResults' => $this->maxResults,
        ];

        if ($this->country !== null) {
            $parameters['country'] = $this->country;
        }

        try {
            $apiAgent = $this->getAgent($api);
            return $apiAgent->getContent($content, $parameters);
        } catch (\Exception $e) {
            // TODO: Mail the error
            dump('getContent');
            dump($e);
        }
    }

    /**
     * Return required api agent
     *
     * @param  string $api
     * @return object
     */
    private function getAgent($api)
    {
        $api = strtolower($api);
        return $this->{$api};
    }
}