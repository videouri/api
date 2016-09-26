<?php

namespace Videouri\Services\Scout\Actions;

use Videouri\Services\Scout\Actions\Traits\Paginated;
use Cache;

/**
 * @package Videouri\Services\Scout
 */
final class Search extends AbstractAction
{
    use Paginated;

    /**
     * @var string
     */
    private $query;

    /**
     * @param string $query
     *
     * @throws \Exception
     * @return $this
     */
    public function setQuery($query)
    {
        if (empty($query) || ctype_space($query)) {
            throw new \Exception('Search query cannot be empty.');
        }

        $this->query = $query;

        return $this;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return array
     */
    public function process()
    {
        $parameters = [
            'query' => $this->getQuery(),
            'page' => $this->getPage(),
            'maxResults' => $this->getMaxResults(),
        ];

        $results = [];
        foreach ($this->getSources() as $api) {
            $parameters['api'] = $api;
            $parametersHash = md5(serialize($parameters));

            $apiCachedContent = Cache::get($parametersHash);
            $videos = $apiCachedContent;

            if (!$apiCachedContent) {
                $agent = $this->getAgent($api);

                $videos = $agent->search($parameters);
                $videos = $agent->parseVideos($videos);

                Cache::put($parametersHash, $videos, self::CACHE_FOR_A_HALF_DAY);
            }

            $results = array_merge($results, $videos);
        }

        return $results;
    }
}
