<?php

namespace App\Http\Controllers\Api;

use App\Jobs\RegisterSearch;
use App\Services\FakeContentGenerator;
use Illuminate\Http\Request;
use Auth;

/**
 * Class VideosController
 * @package App\Http\Controllers\Api
 */
class VideosController extends ApiController
{
    /**
     * Don't execute a real call to the APIs
     * @var boolean
     */
    private $fakeContent = false;

    /**
     * VideosController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->middleware('auth', [
            'only' => [
                'getWatchLater',
                'getFavorites'
            ]
        ]);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getSearch(Request $request)
    {
        $this->apiFetcher->timestamp = date('Y-m-d');

        /**
         * Check that search query is not empty
         * nor contains just empty spaces
         */
        $searchQuery = $request->get('query');

        if (empty($searchQuery) || ctype_space($searchQuery)) {
            return response()->error('invalid_search_query', 400);
        }

        # Max results
        $maxResults = $request->get('maxResults');
        if (!is_numeric($maxResults) || $maxResults > 20) {
            $maxResults = 12;
        }
        $this->apiFetcher->maxResults = $maxResults;

        # Selective API
        $apis = $request->get('apis');
        $predefinedApis = $this->apiFetcher->apis;
        if (in_array_r($apis, $predefinedApis)) {
            $this->apiFetcher->apis = $apis;
        }

        # Page
        $page = $request->get('page', 1);
        if ($page > 1) {
            $this->apiFetcher->page = $page;
        }

        # Sorting
        $this->apiFetcher->sort = $request->get('sort', 'relevance');

        $period = '';

        // Queue to save search term

        $this->dispatch(new RegisterSearch($searchQuery, Auth::user()));

        $results = $this->apiFetcher->searchVideos($searchQuery);

        $videos = [];
        foreach ($results as $api => $apiData) {
            $videos = array_merge_recursive($videos, $apiData);
        }

        return response()->success($videos);
    }

    /**
     * @return array
     */
    public function getHome()
    {
        /**
         * Default parameters for homepage
         */
        $this->apiFetcher->apis = ['Youtube'];
        $this->apiFetcher->period = 'today';
        $this->apiFetcher->maxResults = 8;

        $content = ['most_viewed'];

        $videos = [];
        if ($this->fakeContent) {
            $faker = new FakeContentGenerator;
            return $faker->videos();
        }

        try {
            $apiResults = $this->apiFetcher->mixedCalls($content);

            foreach ($apiResults as $content => $contentData) {
                foreach ($contentData as $api => $apiData) {
                    $results = $this->apiFetcher->parseResults($api, $apiData, $content);

                    // Append results from different sources
                    $videos = array_merge($videos, $results);

                    $viewsCount = [];
                    // $ratings = [];

                    foreach ($videos as $k => $v) {
                        $viewsCount[$k] = $v['views'];
                        // $ratings[$k] = $v['rating'];
                    }

                    array_multisort($viewsCount, SORT_DESC, $videos);
                }
                // $sortData as $api => $apiData
                // array_multisort($viewData['data'][$content], SORT_DESC, $viewData['data'][$content]);
                // $test = array_filter($viewData['data'][$content], function($v, $k) {
                //     echo('<pre>');
                //     var_dump($k['viewsCount']);
                //     // return strpos($k, 'theme');
                // });

            } // $api_response as $sortName => $sortData

            return response()->success($videos);
        } catch (\Exception $e) {
            dump('getHome');
            dump($e);
        }
    }

    ////////////////
    // Video page //
    ////////////////

    /**
     * Recommended
     *
     * @param Request $request
     * @return array
     */
    public function getRecommended(Request $request)
    {
        $api = $request->get('api');
        $originalId = $request->get('original_id');

        $videos = $this->apiFetcher->getRelatedVideos($api, $originalId);

        return $videos;
    }

    /**
     * Favorites
     *
     * @return array
     */
    public function getFavorites()
    {
        $user = Auth::user();
        $records = $user->favorites()
                        ->distinct()
                        // ->select(['title', 'thumbnail', 'description', 'custom_id'])
                        ->limit(50)
                        ->get();

        return $this->parseResponse($records);
    }

    /**
     * Watch later
     *
     * @return array
     */
    public function getWatchLater()
    {
        $user = Auth::user();
        $records = $user->watchLater()
                        ->distinct()
                        // ->select(['title', 'thumbnail', 'description', 'custom_id'])
                        ->limit(50)
                        ->get();

        return $this->parseResponse($records);
    }

    /**
     * DRY for favorites and watch later
     *
     * @param  array $records
     * @return array
     */
    private function parseResponse($records)
    {
        $records = $records->all();

        if (count($records) > 0) {
            $records = $this->apiFetcher->transformVideos($records);
        }

        return response()->success($records);
    }
}
