<?php

namespace Videouri\Services\Scout;

use Auth;
use Cache;
use DailymotionApiException;
use Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Videouri\Entities\Video;
use Videouri\Jobs\RegisterView;
use Videouri\Jobs\SaveVideo;
use Videouri\Services\Scout\Agents\AgentInterface;

/**
 * @package Videouri\Services
 */
class ApiFetcher
{


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
     * @var AgentInterface
     */
    private $agent;
    /**
     * @uses $availableContents
     * @var string
     */
    public $content;

    /**
     * @uses $availableContents
     * @var string
     */
    protected $videoContent;

    /**
     * Array containing available time periods.
     * @var array
     */
    protected static $validPeriods = ['ever', 'today', 'week', 'month'];

    /**
     * Available contents
     * @var array
     */
    protected static $availableContents = ['most_viewed', 'newest', 'top_rated'];

    /**
     * ApiManager constructor.
     */
    public function __construct()
    {
        if (!in_array($this->period, self::$validPeriods)) {
            $this->period = 'today';
        }

        $this->country = Session::get('country');
        $this->timestamp = date('Y-m-d');
    }

    /**
     * @param $text
     *
     * @return string
     */
    protected function parseDescription($text)
    {
        if ($this->content !== 'getVideoEntry') {
            $text = str_limit($text, 90);
        }

        return $text;
    }

    /**
     * @param AgentInterface $agent
     *
     * @return $this
     */
    public function setAgent(AgentInterface $agent)
    {
        $this->agent = $agent;
        return $this;
    }

    /**
     * @param $content
     *
     * @return array
     * @throws \Exception
     */
    public function mixedCalls($content)
    {
        $apiParameters = [
            'apis' => $this->apis,
            'content' => $content,
            'period' => $this->period,
            // 'query' => $this->query,
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
                throw new \Exception('What is this? Exception');
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
     * @param  string $query
     *
     * @return array
     */
    public function search($query)
    {
        $parameters = [
            'query' => $query,
            'page' => $this->page,
            'maxResults' => $this->maxResults,
        ];

        if ($this->sort !== null) {
            $parameters['sort'] = $this->sort;
        }

        if ($this->period !== null) {
            $parameters['period'] = $this->period;
        }

        $results = [];
        foreach ($this->apis as $api) {
            try {
                $apiAgent = $this->getAgent($api);

                $videos = $apiAgent->search($parameters);
                $videos = $this->parseResults($api, $videos);

                $results[$api] = $videos;
            } catch (\Exception $e) {
                // @TODO dynamically activate or de-activate calls to an api source based on the fact
                //       that if my calls are being denied because of rate-limiting
            }
        }

        return $results;
    }

    /**
     * @param  string $api
     * @param  string $videoId
     *
     * @return mixed
     */
    public function getVideo($api, $videoId)
    {
        /**
         * Return cached video and if it's not,
         * get it from the source and transform it
         */
        if ($video = Video::where('original_id', '=', $videoId)->first()) {
            if ($video->dmca_claim) {
                return abort(404);
            }

            $video = $this->transformVideos($video);
        } else {
            try {
                $apiAgent = $this->getAgent($api);

                # Fetch the video from source
                $video = $apiAgent->getVideo($videoId);

                # Parse it
                $video = $this->parseResults($api, $video);

                # Run a job to cache it in the DB
                $job = (new SaveVideo($video, $api))->onQueue('pre_video_saved');
                $this->dispatch($job);
            } catch (NotFoundHttpException $e) {
                abort(404);
            } catch (DailymotionApiException $e) {
                abort(404);
            } catch (\Exception $e) {
                dump('parseResults');
                dump($e);
            }
        }

        /**
         * If there's a user logged, register the video view
         */
        if ($user = Auth::user()) {
            $originalId = $video['original_id'];

            $job = (new RegisterView($originalId, $user))->onQueue('post_video_saved');
            $this->dispatch($job);
        }

        return $video;
    }

    /**
     * Retrieve related videos for a given video
     *
     * @param  string $api
     * @param  string $videoId
     *
     * @return array
     */
    public function getRelated($api, $videoId)
    {
        $apiAgent = $this->getAgent($api);
        $videos = $apiAgent->getRelated($videoId, $maxResults = 6);

        if (empty($videos)) {
            return [];
        }

        $videos = $apiAgent->parseVideos($videos, $content = 'related');

        return $videos;
    }

    /**
     * Get content for a specific keyword, like, most_viewed
     *
     * @param  string $content
     * @param  string $api
     *
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
}
