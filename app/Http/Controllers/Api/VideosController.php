<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Auth;

use App\Http\Controllers\Controller;

use App\Services\ApiProcessing;
use App\Services\FakeContentGenerator;

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
    private $fakeContent = false;

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
        // $this->apiprocessing->apis = ['dailymotion', 'youtube'];
        $this->apiprocessing->apis = ['youtube'];
        $this->apiprocessing->period = 'today';
        $this->apiprocessing->maxResults = 8;

        $content = ['most_viewed'];

        $videos = [];
        if ($this->fakeContent) {
            $faker = new FakeContentGenerator;
            return $faker->videos();
        }

        try {
            $apiResults = $this->apiprocessing->mixedCalls($content);
        } catch (Exception $e) {
            dump($e);
        }
        // dd($apiResults);

        foreach ($apiResults as $content => $contentData) {
            foreach ($contentData as $api => $apiData) {
                $results = $this->apiprocessing->parseApiResult($api, $apiData, $content);

                // Append results from different sources
                $videos = array_merge($videos, $results);

                $viewsCount = [];
                // $ratings = [];
                foreach ($videos['data'] as $k => $v) {
                    $viewsCount[$k] = $v['views'];
                    // $ratings[$k] = $v['rating'];
                }

                array_multisort($viewsCount, SORT_DESC, $videos['data']);
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
}
