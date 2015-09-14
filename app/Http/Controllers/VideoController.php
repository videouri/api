<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Jobs\SaveVideoData;
use Videouri\Services\ApiProcessing;
use Videouri\Entities\Video;

class VideoController extends Controller
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($customId, $videoSlug = null)
    {
        $api    = substr($customId, 1, 1);
        $origId = substr_replace($customId, '', 1, 1);

        switch ($api) {
            case 'd':
                $api = 'Dailymotion';
                break;

            case 'v':
                $api = 'Vimeo';
                break;

            case 'y':
                $api = 'Youtube';
                break;

            case 'M':
                $api = 'Metacafe';
                $long_id  = $origId . '/' . $videoSlug;
                break;

            default:
                show_error(lang('video_id',$customId));
                break;
        }


        // $this->apiprocessing->videoId = ($api === "Metacafe") ? $long_id : $origId;
        $this->apiprocessing->videoId = $origId;
        $this->apiprocessing->content = 'getVideoEntry';

        // @TODO
        //   - Try to get data from the database, if not do an API call
        #if ($response = Video::where('original_id', '=', $origId)->first()) {
        #    $response = $response->toArray();
        #} else {
            $response = $this->apiprocessing->individualCall($api);
        #}

        if (!$response) {
            abort(404);
        }

        $video = [];

        if ($api === "Dailymotion") {
            $httpsUrl     = preg_replace("/^http:/i", "https:", $response['url']);
            $thumbnailUrl = preg_replace("/^http:/i", "https:", $response['thumbnail_360_url']);

            $video['url']         = $httpsUrl;
            $video['title']       = $response['title'];
            $video['description'] = $response['description'];
            $video['thumbnail']   = $thumbnailUrl;
            
            // $video['ratings']  = $response['ratings'];
            $video['views']       = humanizeNumber($response['views_total']);
            $video['duration']    = humanizeSeconds($response['duration']);
            
            $video['tags']        = $response['tags'];
            $video['related']     = $this->_relatedVideos($api, $origId);
        }

        // elseif ($api === "Metacafe") {
        //     // if (preg_match('/http:\/\/[w\.]*metacafe\.com\/fplayer\/(.*).swf/is', $response['embed'], $match)) {
        //     //     $video['swf']['url'] = $response['embed'];
        //     //     $video['swf']['api'] = 'mcapiplayer';
        //     // }

        //     // else {
        //     //     $video['embed_html'] = $response['embed'];
        //     // }

        //     $video['title'] = $response->title;
        //     $video['thumbnail'] = 'http://www.metacafe.com/thumb/'.$origId.'.jpg';

        //     $dom = new DOMDocument();
        //     $dom->loadHTML($response->description);

        //     $xml = simplexml_import_dom($dom);
        //     $p   = (string)$xml->body->p;
            
        //     $video['description'] = strstr($p, 'Ranked', true);

        //     $tags  = array();
        //     $count = count((object)$xml->body->p[1]->a) - 2;
        //     for ($i = 2; $i <= $count; $i++) {
        //         $tag = (object)$xml->body->p[1]->a[$i];
        //         $tag = str_replace(array('News & Events'), '', $tag);
        //         $tags[] = $tag;
        //     }

        //     $video['tags']        = $tags;
        //     // $video['related']     = $this->_relatedVideos(array('api'=>$api,'id'=>$origId));
        // }

        elseif ($api == "Vimeo") {
            $video = $response['body'];

            $video['url']         = "https://vimeo.com/".$origId;
            $video['title']       = $video['name'];
            $video['description'] = $video['description'];
            $video['thumbnail']   = $video['pictures']['sizes'][2]['link'];
            
            // $video['ratings']  = $response['ratings'];
            $video['views']       = humanizeNumber($video['stats']['plays']);
            $video['duration']    = humanizeSeconds($video['duration']);

            $tags = array();
            if (!empty($video['tags'])) {
                foreach($video['tags'] as $tag) {
                    $tags[] = $tag['name'];
                }
            }
            
            $video['tags']       = $tags;
            $video['related']    = $this->_relatedVideos($api, $origId);
        }

        elseif ($api == "Youtube") {
            $totalSeconds = ISO8601ToSeconds($response->contentDetails->duration);

            $video['url']         = "https://www.youtube.com/watch?v=".$origId;
            $video['title']       = $response->snippet->title;
            $video['description'] = $response->snippet->description;
            $video['thumbnail']   = $response->snippet->thumbnails->medium->url;
            
            // $video['ratings']     = $response['gd$rating']['average'];
            $video['views']       = humanizeNumber($response->statistics->viewCount);
            $video['duration']    = humanizeSeconds($totalSeconds);
            
            $video['tags']        = isset($response->snippet->tags) ? $response->snippet->tags : [];
            $video['related']     = $this->_relatedVideos($api, $origId);
        }

        // $video['customId'] = $customId;
        $video['origId']      = $origId;


        // Queue to save video data
        $this->dispatch(new SaveVideoData($video, $api));

        $data['video']       = $video;
        $data['thumbnail']   = $video['thumbnail'];
        $data['source']      = $api;
        
        // Metadata
        $data['title']       = $video['title'] . ' - Videouri';
        $data['description'] = str_limit($video['description'], 100);
        $data['canonical']   = "video/$customId";
        
        $data['bodyId']      = 'videoPage';

        return view('videouri.pages.video', $data);
    }

    /**
    * This function will retrieve related videos according to its id or some of its tags
    *
    * @param string $origId The id for which to look for data
    * @return the php response from parsing the data.
    */
    private function _relatedVideos($api, $origId = null)
    {
        $this->apiprocessing->content    = 'getRelatedVideos';
        $this->apiprocessing->maxResults = 8;
        // $this->apiprocessing->api = $api;
        // $this->apiprocessing->videoId = $origId;
         
        $response = $this->apiprocessing->individualCall($api);
        $response = $this->apiprocessing->parseApiResult($api, $response);

        $related = $response;

        // switch ($api) {
        //     case 'Dailymotion':
        //         $i = 0;
        //         // dd($response);
        //         foreach ($response['list'] as $video) {
        //             preg_match('@video/([^_]+)_([^/]+)@', $video['url'], $match);
        //             $url = $match[1].'/'.$match[2];
        //             $url = url('video/'.substr($url,0,1).'d'.substr($url,1));

        //             $httpsUrl                 = preg_replace("/^http:/i", "https:", $url);
        //             $related[$i]['url']       = $url;
                    
        //             $thumbnailUrl             = preg_replace("/^http:/i", "https:", $video['thumbnail_240_url']);
        //             $related[$i]['thumbnail'] = $thumbnailUrl;
                    
        //             $related[$i]['title']     = $video['title'];
        //             $related[$i]['source']    = 'Dailymotion';
        //             $i++;
        //         }
        //         break;

        //     case "Metacafe":
        //         $i = 0;
        //         foreach ($response->channel->item as $video) {
        //             preg_match('/http:\/\/[w\.]*metacafe\.com\/watch\/([^?&#"\']*)/is', $video->link, $match);
        //             $id  = substr($match[1],0,-1);
        //             $url = url('video/' . substr($id, 0, 1) . 'M' . substr($id,1));

        //             $related[$i]['url']       = $url;
        //             $related[$i]['title']     = trim_text($video->title, 83);
        //             $related[$i]['thumbnail'] = "http://www.metacafe.com/thumb/$video->id.jpg";
        //             $related[$i]['source']    = 'Metacafe';
        //             $i++;
        //         }
        //         break;

        //     case "Vimeo":
        //         $i = 0;
        //         foreach ($response['body']['data'] as $video) {
        //             $origid = explode('/', $video['uri'])[2];
        //             $id     = substr($origid,0,1).'v'.substr($origid,1);
        //             $url    = url('video/'.$id);

        //             $related[$i]['url']       = $url;
        //             $related[$i]['title']     = trim_text($video['name'], 83);
        //             $related[$i]['thumbnail'] = $video['pictures']['sizes'][2]['link'];
        //             $related[$i]['source']    = 'Metacafe';
        //             $i++;
        //         }
        //         break;

        //     case 'Youtube':
        //         $i = 0;
        //         foreach ($response['feed']['entry'] as $video) {
        //             $origid = substr( $video['id']['$t'], strrpos( $video['id']['$t'], '/' )+1 );
        //             $id     = substr($origid,0,1).'y'.substr($origid,1);
        //             $url    = url('video/'.$id);

        //             $related[$i]['url']       = $url;
        //             $related[$i]['title']     = trim_text($video['title']['$t'], 83);
        //             $thumbnailUrl             = preg_replace("/^http:/i", "https:", $video['media$group']['media$thumbnail'][0]['url']);
        //             $related[$i]['thumbnail'] = $thumbnailUrl;
        //             $related[$i]['source']    = 'YouTube';
        //             $i++;
        //         }
        //         break;
        // }

        return $related;
    }
}
