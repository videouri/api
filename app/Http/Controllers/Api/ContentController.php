<?php

namespace Videouri\Http\Controllers\Api;

use Videouri\Maps\Source;

/**
 * @package Videouri\Http\Controllers\Api
 */
class ContentController extends ApiController
{
    /**
     * @return array
     */
    public function home()
    {
        /**
         * Default parameters for homepage
         */
        // $this->apiFetcher->apis = ['Youtube'];
        // $this->apiFetcher->period = 'today';
        // $this->apiFetcher->maxResults = 8;
        //
        // $content = ['most_viewed'];

        $apis = [Source::DAILYMOTION, Source::VIMEO, Source::YOUTUBE];
        $videos = $this->scout->getContent($apis, ['most_viewed']);



        // try {
        //     $apiResults = $this->apiFetcher->mixedCalls($content);
        //
        //     foreach ($apiResults as $content => $contentData) {
        //         foreach ($contentData as $api => $apiData) {
        //             $results = $this->apiFetcher->parseResults($api, $apiData, $content);
        //
        //             // Append results from different sources
        //             $videos = array_merge($videos, $results);
        //
        //             $viewsCount = [];
        //             // $ratings = [];
        //
        //             foreach ($videos as $k => $v) {
        //                 $viewsCount[$k] = $v['views'];
        //                 // $ratings[$k] = $v['rating'];
        //             }
        //
        //             array_multisort($viewsCount, SORT_DESC, $videos);
        //         }
        //         // $sortData as $api => $apiData
        //         // array_multisort($viewData['data'][$content], SORT_DESC, $viewData['data'][$content]);
        //         // $test = array_filter($viewData['data'][$content], function($v, $k) {
        //         //     echo('<pre>');
        //         //     var_dump($k['viewsCount']);
        //         //     // return strpos($k, 'theme');
        //         // });
        //
        //     } // $api_response as $sortName => $sortData
        //
        //     return response()->success($videos);
        // } catch (\Exception $e) {
        //     dump('getHome');
        //     dump($e);
        // }
    }
}
