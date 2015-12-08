<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Auth;

use App\Http\Controllers\Controller;
use App\Jobs\RegisterSearch;

use Videouri\Services\ApiProcessing;
use Videouri\Services\FakeContentGenerator;

class SearchController extends Controller
{
    /**
     * @var ApiProcessing
     */
    protected $apiprocessing;

    /**
     * Don't execute a real call to the APIs
     * @var boolean
     */
    private $fakeContent = false;

    public function __construct(ApiProcessing $apiprocessing)
    {
        $this->apiprocessing = $apiprocessing;
        $this->apiprocessing->timestamp = date('Y-m-d');
    }

    public function getVideos(Request $request)
    {
        /**
         * Check that search query is not empty
         * nor contains just empty spaces
         */
        $searchQuery = $request->get('query');

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
        $this->dispatch(new RegisterSearch($searchQuery, Auth::user()));

        $videos = array();

        try {
            $videosRaw = $this->apiprocessing->mixedCalls()['search'];

            foreach ($videosRaw as $api => $apiData) {
                $videos = array_merge($videos, $this->apiprocessing->parseApiResult($api, $apiData));
            }

            return response()->success($videos);
        } catch (Exception $e) {
            dd($e);
        }
    }
}
