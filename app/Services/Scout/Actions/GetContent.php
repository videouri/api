<?php

namespace Videouri\Services\Scout\Actions;

use Videouri\Services\Scout\Actions\Traits\Paginated;
use Cache;

/**
 * @package Videouri\Services\Scout
 */
class GetContent extends AbstractAction
{
    use Paginated;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $country;

    /**
     * @param string $content
     *
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @param string $country
     *
     * @return $this
     */
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return array
     */
    public function process()
    {
        $parameters = [
            'period' => $this->period,
            'maxResults' => $this->maxResults,
        ];

        if ($this->country !== null) {
            $parameters['country'] = $this->country;
        }

        $parametersHash = md5(serialize($parameters));
        $cachedContent = Cache::get($parametersHash);

        if (!$cachedContent) {
            $apiResponse = [];
            foreach ($this->getSources() as $api) {
                try {
                    $apiAgent = $this->getAgent($api);
                    $videos = $apiAgent->getContent($this->content, $api);
                    $videos = $apiAgent->parseVideos($videos);

                    $apiResponse[$this->content][$api] = $videos;
                } catch (\Exception $e) {

                }

                Cache::put($parametersHash, $apiResponse, self::CACHE_FOR_A_FULL_DAY);
            }

            return $apiResponse;
        }

        return $cachedContent;
    }
}
