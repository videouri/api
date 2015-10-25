<?php

namespace App\Http\Controllers;

use Request;
use Auth;

use App\Jobs\SaveSearchTerm;
use Videouri\Services\ApiProcessing;

class SearchController extends Controller
{
    /**
     * ApiProcessing
     */
    protected $apiprocessing;

    // public function __construct(ApiProcessing $apiprocessing)
    public function __construct(ApiProcessing $apiprocessing)
    {
        $this->apiprocessing = $apiprocessing;
    }

    /**
    * This function is ment for getting search or tag results for the query specified
    *
    * @return the php response from parsing the data.
    */
    public function getVdeos()
    {
        $this->apiprocessing->apis = ['Dailymotion', 'Vimeo', 'Youtube'];
        // $this->apiprocessing->apis = ['Youtube'];

        $this->apiprocessing->page = Request::get('page', 1);
        $this->apiprocessing->sort = Request::get('sort', 'relevance');
        $this->apiprocessing->maxResults = 12;

        $searchQuery = Request::get('search_query');

        // Not empty nor just white spaces
        if (empty($searchQuery) || ctype_space($searchQuery)) {
            return response()->view('errors.404', [], 404);
        }

        $this->apiprocessing->searchQuery = $searchQuery;
        $this->apiprocessing->content     = 'search';

        // Queue to save search term
        $this->dispatch(new SaveSearchTerm($searchQuery, Auth::user()));

        try {
            $searchResultsRaw = $this->apiprocessing->mixedCalls()['search'];
            $searchResults    = array();

            foreach ($searchResultsRaw as $api => $apiData) {
                $searchResults = array_merge($searchResults, $this->apiprocessing->parseApiResult($api, $apiData));
            }

            // dd($searchResults);

            // uasort($searchResults, 'sortByViews');

        }
        catch(ParameterException $e) {
            dd($e);
            #echo "Encountered an API error -- code {$e->getCode()} - {$e->getMessage()}";
        }
        catch(Exception $e) {
            dd($e);
            #echo "Some other Exception was thrown -- code {$e->getCode()} - {$e->getMessage()}";
        }

        // dd($searchResults);


        // App data
        $data['searchQuery'] = $searchQuery;
        $data['data']        = $searchResults;
        $data['apis']        = $this->apiprocessing->apis;
        $data['page']        = $this->apiprocessing->page;

        // Metadata
        $data['title']       = $searchQuery . ' - Videouri';
        $data['description'] = 'Searching for ' . $searchQuery . ' on ' . implode(', ', $data['apis']);
        $data['canonical']   = '';

        return view('videouri.public.results', $data);
    }
}
