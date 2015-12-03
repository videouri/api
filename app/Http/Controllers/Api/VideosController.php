<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SaveSearchTerm;
use Auth;
use Illuminate\Http\Request;
use Videouri\Services\ApiProcessing;
use Videouri\Services\FakeContentGenerator;

class VideosController extends Controller
{
    /**
     * @var ApiProcessing
     */
    protected $apiprocessing;

    /**
     * Don't execute a real call to the APIs
     * @var boolean
     */
    private $fakeContent = true;

    public function __construct(ApiProcessing $apiprocessing)
    {
        $this->apiprocessing = $apiprocessing;
        $this->apiprocessing->timestamp = date('Y-m-d');
    }

    public function getHome()
    {
        /**
         * Default parameters for homepage
         */
        // $this->apiprocessing->apis       = ['Youtube', 'Dailymotion'];
        $this->apiprocessing->apis = ['Dailymotion', 'Youtube'];
        $this->apiprocessing->content = ['most_viewed'];
        $this->apiprocessing->period = 'today';
        $this->apiprocessing->maxResults = 8;

        $videos = [];
        if ($this->fakeContent) {
            $faker = new FakeContentGenerator;
            return $faker->videos();
        }

        $apiResults = $this->apiprocessing->mixedCalls();

        foreach ($apiResults as $content => $contentData) {
            foreach ($contentData as $api => $apiData) {
                $results = $this->apiprocessing->parseApiResult($api, $apiData, $content);
                if (!empty($results)) {
                    if (!isset($videos[$content])) {
                        $videos[$content] = $results[$content];
                    } elseif (is_array($videos[$content])) {
                        $videos[$content] = array_merge($videos[$content], $results[$content]);
                    }
                }

                $viewsCount = [];
                // $ratings = [];
                foreach ($videos[$content] as $k => $v) {
                    $viewsCount[$k] = $v['views'];
                    // $ratings[$k] = $v['rating'];
                }
                array_multisort($viewsCount, SORT_DESC, $videos[$content]);
            }
            // $sortData as $api => $apiData
            // array_multisort($viewData['data'][$content], SORT_DESC, $viewData['data'][$content]);
            // $test = array_filter($viewData['data'][$content], function($v, $k) {
            //     echo('<pre>');
            //     var_dump($k['viewsCount']);
            //     // return strpos($k, 'theme');
            // });

        } // $api_response as $sortName => $sortData

        return $videos;
    }

    public function getSearch(Request $request)
    {
        /**
         * Check that search query is not empty
         * nor contains just empty spaces
         */
        $searchQuery = $request->get('search_query');

        if (empty($searchQuery) || ctype_space($searchQuery)) {
            return response()->error('invalid_search_query', 400);
        }

        $this->apiprocessing->searchQuery = $searchQuery;

        /**
         * Avoid flooding with max results input
         */
        $maxResults = $request->get('maxResults');
        if (!is_numeric($maxResults) || $maxResults > 20) {
            $maxResults = 12;
        }

        $this->apiprocessing->maxResults = $maxResults;

        /**
         * Validate apis input, if set
         */
        $apis = $request->get('apis');
        $predefinedApis = $this->apiprocessing->apis;
        if (in_array_r($apis, $predefinedApis)) {
            $this->apiprocessing->apis = $apis;
        }

        $this->apiprocessing->page = $request->get('page', 1);
        $this->apiprocessing->sort = $request->get('sort', 'relevance');
        $this->apiprocessing->content = 'search';

        // Queue to save search term
        $this->dispatch(new SaveSearchTerm($searchQuery, Auth::user()));

        $videos = array();
        $videosRaw = $this->apiprocessing->mixedCalls()['search'];

        foreach ($videosRaw as $api => $apiData) {
            $videos = array_merge($videos, $this->apiprocessing->parseApiResult($api, $apiData));
        }

        return response()->success($videos);
    }
}
