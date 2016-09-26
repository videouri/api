<?php

namespace Videouri\Http\Controllers\Api;

use Auth;
use Illuminate\Http\Request;
use Videouri\Entities\Source;
use Videouri\Jobs\RegisterSearch;

/**
 * @package Videouri\Http\Controllers\Api
 */
class SearchController extends ApiController
{
    /**
     * @param Request $request
     *
     * @return string
     */
    public function genericSearch(Request $request)
    {
        $query = $request->get('query');
        $page = (int) $request->get('page', 1);
        $maxResults = $request->get('maxResults');

        if (!is_numeric($maxResults) || $maxResults > 20) {
            $maxResults = 12;
        }

        $this->dispatch(new RegisterSearch($query, Auth::user()));

        $sources = [Source::DAILYMOTION, Source::VIMEO, Source::YOUTUBE];
        $videos = $this->scout->search($query, $sources, $page, $maxResults);

        return response($videos);
    }
}
