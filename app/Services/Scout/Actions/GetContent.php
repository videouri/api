<?php

namespace Videouri\Services\Scout\Actions;

use Videouri\Services\Scout\Actions\Traits\Filtered;
use Videouri\Services\Scout\Actions\Traits\Paginated;
use Cache;

/**
 * @package Videouri\Services\Scout
 */
class GetContent extends AbstractAction
{
    use Paginated, Filtered;

    /**
     * @var string
     */
    private $content;

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
     * @return array
     */
    public function process()
    {
        $parameters = [
            'period' => $this->getPage(),
            'maxResults' => $this->getMaxResults(),
            'country' => $this->getCountry()
        ];

        $parametersHash = md5(serialize($parameters));
        $cachedContent = Cache::get($parametersHash);

        if (!$cachedContent) {
            $apiResponse = [];
            foreach ($this->getSources() as $api) {
                try {
                    $apiAgent = $this->getAgent($api);
                    $videos = $apiAgent->getContent($this->content, $parameters);
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
