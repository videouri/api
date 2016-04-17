<?php

namespace App\Services;

/**
 * Credit: https://github.com/alaouy/Youtube
 */
class YoutubeClient
{
    /**
     * @var string
     */
    protected $youtubeKey; // from the config file

    /**
     * @var array
     */
    public $APIs = [
        'videos.list'        => 'https://www.googleapis.com/youtube/v3/videos',
        'search.list'        => 'https://www.googleapis.com/youtube/v3/search',
        'channels.list'      => 'https://www.googleapis.com/youtube/v3/channels',
        'playlists.list'     => 'https://www.googleapis.com/youtube/v3/playlists',
        'playlistItems.list' => 'https://www.googleapis.com/youtube/v3/playlistItems',
        'activities'         => 'https://www.googleapis.com/youtube/v3/activities',
    ];

    /**
     * @var array
     */
    public $pageInfo = [];

    /**
     * Constructor
     * $youtube = new Youtube(['key' => 'KEY HERE'])
     *
     * @param array $params
     * @throws \Exception
     */
    public function __construct($key)
    {
        if (empty($key)) {
            throw new \Exception('Google API key is Required, please visit https://console.developers.google.com/');
        }

        $this->youtubeKey = $key;
    }

    /**
     * @param $single
     * @return \StdClass
     * @throws \Exception
     */
    public function getVideoInfo($vId, $part = ['id', 'snippet', 'contentDetails', 'player', 'statistics', 'status'])
    {
        $apiUrl = $this->getApi('videos.list');
        $params = [
            'id'   => is_array($vId) ? implode(',', $vId) : $vId,
            'key'  => $this->youtubeKey,
            'part' => implode(', ', $part),
        ];

        $apiData = $this->apiGet($apiUrl, $params);

        if (is_array($vId)) {
            return $this->decodeMultiple($apiData);
        }

        return $this->decodeSingle($apiData);
    }

    /**
     * Gets popular videos for a specific region (ISO 3166-1 alpha-2)
     *
     * @param $regionCode
     * @param int $maxResults
     * @return array
     */
    public function getPopularVideos($regionCode, $maxResults = 10, $part = ['id', 'snippet', 'contentDetails', 'player', 'statistics', 'status'])
    {
        $apiUrl = $this->getApi('videos.list');
        $params = [
            'chart'      => 'mostPopular',
            'part'       => implode(', ', $part),
            'regionCode' => $regionCode,
            'maxResults' => $maxResults,
        ];

        $apiData = $this->apiGet($apiUrl, $params);

        return $this->decodeList($apiData);
    }

    /**
     * Simple search interface, this search all stuffs
     * and order by relevance
     *
     * @param $q
     * @param int $maxResults
     * @return array
     */
    public function search($q, $maxResults = 10, $part = ['id', 'snippet'])
    {
        $params = [
            'q'          => $q,
            'part'       => implode(', ', $part),
            'maxResults' => $maxResults,
        ];

        return $this->searchAdvanced($params);
    }

    /**
     * Search only videos
     *
     * @param  string $q Query
     * @param  integer $maxResults number of results to return
     * @param  string $order Order by
     * @return \StdClass  API results
     */
    public function searchVideos($q, $maxResults = 10, $order = null, $part = ['id'])
    {
        $params = [
            'q'          => $q,
            'type'       => 'video',
            'part'       => implode(', ', $part),
            'maxResults' => $maxResults,
        ];

        if (!empty($order)) {
            $params['order'] = $order;
        }

        return $this->searchAdvanced($params);
    }

    /**
     * Search only videos in the channel
     *
     * @param  string $q
     * @param  string $channelId
     * @param  integer $maxResults
     * @param  string $order
     * @return object
     */
    public function searchChannelVideos($q, $channelId, $maxResults = 10, $order = null, $part = ['id', 'snippet'])
    {
        $params = [
            'q'          => $q,
            'type'       => 'video',
            'channelId'  => $channelId,
            'part'       => implode(', ', $part),
            'maxResults' => $maxResults,
        ];

        if (!empty($order)) {
            $params['order'] = $order;
        }

        return $this->searchAdvanced($params);
    }

    /**
     * Generic Search interface, use any parameters specified in
     * the API reference
     *
     * @param $params
     * @param $pageInfo
     * @return array
     * @throws \Exception
     */
    public function searchAdvanced($params, $pageInfo = false)
    {
        $apiUrl = $this->getApi('search.list');

        if (empty($params) || !isset($params['q'])) {
            throw new \InvalidArgumentException('at least the Search query must be supplied');
        }

        $apiData = $this->apiGet($apiUrl, $params);
        if ($pageInfo) {
            return [
                'results' => $this->decodeList($apiData),
                'info'    => $this->pageInfo,
            ];
        }

        return $this->decodeList($apiData);
    }

    /**
     * Generic Search Paginator, use any parameters specified in
     * the API reference and pass through nextPageToken as $token if set.
     *
     * @param $params
     * @param $token
     * @return array
     */
    public function paginateResults($params, $token = null)
    {
        if (!is_null($token)) {
            $params['pageToken'] = $token;
        }

        if (!empty($params)) {
            return $this->searchAdvanced($params, true);
        }
    }

    /**
     * @param $username
     * @return \StdClass
     * @throws \Exception
     */
    public function getChannelByName($username, $optionalParams = false, $part = ['id', 'snippet', 'contentDetails', 'statistics', 'invideoPromotion'])
    {
        $apiUrl = $this->getApi('channels.list');
        $params = [
            'forUsername' => $username,
            'part'        => implode(', ', $part),
        ];

        if ($optionalParams) {
            $params = array_merge($params, $optionalParams);
        }

        $apiData = $this->apiGet($apiUrl, $params);

        return $this->decodeSingle($apiData);
    }

    /**
     * @param $id
     * @return \StdClass
     * @throws \Exception
     */
    public function getChannelById($id, $optionalParams = false, $part = ['id', 'snippet', 'contentDetails', 'statistics', 'invideoPromotion'])
    {
        $apiUrl = $this->getApi('channels.list');
        $params = [
            'id'   => $id,
            'part' => implode(', ', $part),
        ];

        if ($optionalParams) {
            $params = array_merge($params, $optionalParams);
        }

        $apiData = $this->apiGet($apiUrl, $params);

        return $this->decodeSingle($apiData);
    }

    /**
     * @param $channelId
     * @param array $optionalParams
     * @return array
     * @throws \Exception
     */
    public function getPlaylistsByChannelId($channelId, $optionalParams = [], $part = ['id', 'snippet', 'status'])
    {
        $apiUrl = $this->getApi('playlists.list');
        $params = [
            'channelId' => $channelId,
            'part'      => implode(', ', $part),
        ];

        if ($optionalParams) {
            $params = array_merge($params, $optionalParams);
        }

        $apiData = $this->apiGet($apiUrl, $params);

        return $this->decodeList($apiData);
    }

    /**
     * @param $id
     * @return \StdClass
     * @throws \Exception
     */
    public function getPlaylistById($id, $part = ['id', 'snippet', 'status'])
    {
        $apiUrl = $this->getApi('playlists.list');
        $params = [
            'id'   => $id,
            'part' => implode(', ', $part),
        ];
        $apiData = $this->apiGet($apiUrl, $params);

        return $this->decodeSingle($apiData);
    }

    /**
     * @param $playlistId
     * @return array
     * @throws \Exception
     */
    public function getPlaylistItemsByPlaylistId($playlistId, $pageToken = false, $maxResults = 50, $part = ['id', 'snippet', 'contentDetails', 'status'])
    {
        $apiUrl = $this->getApi('playlistItems.list');
        $params = [
            'playlistId' => $playlistId,
            'part'       => implode(', ', $part),
            'maxResults' => $maxResults,
        ];

        // Pass page token if it is given, an empty string won't change the api response
        if (is_string($pageToken)) {
            $params['pageToken'] = $pageToken;
        }

        $apiData = $this->apiGet($apiUrl, $params);
        $result = ['results' => $this->decodeList($apiData)];

        if (is_string($pageToken) || $pageToken) {
            $result['info']['nextPageToken'] = (isset($this->pageInfo['nextPageToken']) ? $this->pageInfo['nextPageToken'] : false);
            $result['info']['prevPageToken'] = (isset($this->pageInfo['prevPageToken']) ? $this->pageInfo['prevPageToken'] : false);
        }

        return $result;
    }

    /**
     * @param $channelId
     * @return array
     * @throws \Exception
     */
    public function getActivitiesByChannelId($channelId, $part = ['id', 'snippet', 'contentDetails'], $maxResults = 5)
    {
        if (empty($channelId)) {
            throw new \InvalidArgumentException('ChannelId must be supplied');
        }

        $apiUrl = $this->getApi('activities');
        $params = [
            'channelId'  => $channelId,
            'part'       => implode(', ', $part),
            'maxResults' => $maxResults,
        ];
        $apiData = $this->apiGet($apiUrl, $params);

        return $this->decodeList($apiData);
    }

    /**
     * @param  string $videoId
     * @return array
     * @throws \Exception
     */
    public function getRelatedVideos($videoId, $maxResults = 5, $part = ['id', 'snippet'])
    {
        if (empty($videoId)) {
            throw new \InvalidArgumentException('A video id must be supplied');
        }

        $apiUrl = $this->getApi('search.list');
        $params = [
            'type'             => 'video',
            'relatedToVideoId' => $videoId,
            'part'             => implode(', ', $part),
            'maxResults'       => $maxResults,
        ];
        $apiData = $this->apiGet($apiUrl, $params);

        return $this->decodeList($apiData);
    }

    /**
     * Parse a youtube URL to get the youtube Vid.
     * Support both full URL (www.youtube.com) and short URL (youtu.be)
     *
     * @param  string $youtubeUrl
     * @throws \Exception
     * @return string Video Id
     */
    public static function parseVIdFromURL($youtubeUrl)
    {
        if (strpos($youtubeUrl, 'youtube.com')) {
            if (strpos($youtubeUrl, 'embed')) {
                $path = static::parseUrlPath($youtubeUrl);
                $vid = substr($path, 7);
                return $vid;
            } else {
                $params = static::parseUrlQuery($youtubeUrl);
                return $params['v'];
            }
        } else if (strpos($youtubeUrl, 'youtu.be')) {
            $path = static::parseUrlPath($youtubeUrl);
            $vid = substr($path, 1);
            return $vid;
        } else {
            throw new \Exception('The supplied URL does not look like a Youtube URL');
        }
    }

    /**
     * Get the channel object by supplying the URL of the channel page
     *
     * @param  string $youtubeUrl
     * @throws \Exception
     * @return object Channel object
     */
    public function getChannelFromURL($youtubeUrl)
    {
        if (strpos($youtubeUrl, 'youtube.com') === false) {
            throw new \Exception('The supplied URL does not look like a Youtube URL');
        }

        $path = static::parseUrlPath($youtubeUrl);
        if (strpos($path, '/channel') === 0) {
            $segments = explode('/', $path);
            $channelId = $segments[count($segments) - 1];
            $channel = $this->getChannelById($channelId);
        } else if (strpos($path, '/user') === 0) {
            $segments = explode('/', $path);
            $username = $segments[count($segments) - 1];
            $channel = $this->getChannelByName($username);
        } else {
            throw new \Exception('The supplied URL does not look like a Youtube Channel URL');
        }

        return $channel;
    }

    /*
     *  Internally used Methods, set visibility to public to enable more flexibility
     */

    /**
     * @param $name
     * @return mixed
     */
    public function getApi($name)
    {
        return $this->APIs[$name];
    }

    /**
     * Decode the response from youtube, extract the single resource object.
     * (Don't use this to decode the response containing list of objects)
     *
     * @param  string $apiData the api response from youtube
     * @throws \Exception
     * @return \StdClass  an Youtube resource object
     */
    public function decodeSingle(&$apiData)
    {
        $resObj = json_decode($apiData);
        if (isset($resObj->error)) {
            $msg = "Error " . $resObj->error->code . " " . $resObj->error->message;
            if (isset($resObj->error->errors[0])) {
                $msg .= " : " . $resObj->error->errors[0]->reason;
            }

            throw new \Exception($msg);
        }

        $itemsArray = $resObj->items;
        if (!is_array($itemsArray) || count($itemsArray) == 0) {
            return false;
        }

        return $itemsArray[0];
    }

    /**
     * Decode the response from youtube, extract the multiple resource object.
     *
     * @param  string $apiData the api response from youtube
     * @throws \Exception
     * @return \StdClass  an Youtube resource object
     */
    public function decodeMultiple(&$apiData)
    {
        $resObj = json_decode($apiData);
        if (isset($resObj->error)) {
            $msg = "Error " . $resObj->error->code . " " . $resObj->error->message;
            if (isset($resObj->error->errors[0])) {
                $msg .= " : " . $resObj->error->errors[0]->reason;
            }

            throw new \Exception($msg);
        }

        $itemsArray = $resObj->items;
        if (!is_array($itemsArray)) {
            return false;
        }

        return $itemsArray;
    }

    /**
     * Decode the response from youtube, extract the list of resource objects
     *
     * @param  string $apiData response string from youtube
     * @throws \Exception
     * @return array Array of StdClass objects
     */
    public function decodeList(&$apiData)
    {
        $resObj = json_decode($apiData);
        if (isset($resObj->error)) {
            $msg = "Error " . $resObj->error->code . " " . $resObj->error->message;
            if (isset($resObj->error->errors[0])) {
                $msg .= " : " . $resObj->error->errors[0]->reason;
            }

            throw new \Exception($msg);
        }

        $this->pageInfo = [
            'resultsPerPage' => $resObj->pageInfo->resultsPerPage,
            'totalResults'   => $resObj->pageInfo->totalResults,
            'kind'           => $resObj->kind,
            'etag'           => $resObj->etag,
            'prevPageToken'  => null,
            'nextPageToken'  => null,
        ];

        if (isset($resObj->prevPageToken)) {
            $this->pageInfo['prevPageToken'] = $resObj->prevPageToken;
        }

        if (isset($resObj->nextPageToken)) {
            $this->pageInfo['nextPageToken'] = $resObj->nextPageToken;
        }

        $itemsArray = $resObj->items;
        if (!is_array($itemsArray) || count($itemsArray) == 0) {
            return false;
        }

        return $itemsArray;
    }

    /**
     * Using CURL to issue a GET request
     *
     * @param $url
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function apiGet($url, $params)
    {
        // set the youtube key
        $params['key'] = $this->youtubeKey;

        $curlPort = 80;
        if (strpos($url, 'https') !== false) {
            $curlPort = 443;
        }

        // boilerplates for CURL
        $tuCurl = curl_init();

        curl_setopt($tuCurl, CURLOPT_URL, $url . (strpos($url, '?') === false ? '?' : '') . http_build_query($params));
        curl_setopt($tuCurl, CURLOPT_PORT, $curlPort);
        curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);

        $tuData = curl_exec($tuCurl);
        if (curl_errno($tuCurl)) {
            throw new \Exception('Curl Error : ' . curl_error($tuCurl));
        }

        return $tuData;
    }

    /**
     * Parse the input url string and return just the path part
     *
     * @param  string $url the URL
     * @return string      the path string
     */
    public static function parseUrlPath($url)
    {
        $array = parse_url($url);

        return $array['path'];
    }

    /**
     * Parse the input url string and return an array of query params
     *
     * @param  string $url the URL
     * @return array      array of query params
     */
    public static function parseUrlQuery($url)
    {
        $array = parse_url($url);
        $query = $array['query'];

        $queryParts = explode('&', $query);

        $params = [];
        foreach ($queryParts as $param) {
            $item = explode('=', $param);
            $params[$item[0]] = empty($item[1]) ? '' : $item[1];
        }

        return $params;
    }
}
