<?php

namespace Videouri\Services\Scout\Actions;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Videouri\Maps\Source;
use Videouri\Services\Scout\Agents\AgentInterface;
use Videouri\Services\Scout\Agents\DailymotionAgent;
use Videouri\Services\Scout\Agents\VimeoAgent;
use Videouri\Services\Scout\Agents\YoutubeAgent;

/**
 * @package Videouri\Services\Scout
 */
abstract class AbstractAction
{
    const CACHE_FOR_A_FULL_DAY = 1440;
    const CACHE_FOR_A_HALF_DAY = 720;

    /**
     * @var array
     */
    private $instances = [];

    /**
     * @var array
     */
    protected $sources = [];

    /**
     * @return mixed
     */
    abstract public function process();

    /**
     * @return array
     */
    public function getSources()
    {
        return $this->sources;
    }

    /**
     * @param array $sources
     *
     * @return $this
     * @throws \Exception
     */
    public function setSources(array $sources)
    {
        $this->sources = $sources;
        return $this;
    }

    /**
     * Return required api agent
     *
     * @param  string $api
     *
     * @throws \Exception
     * @return AgentInterface
     */
    public function getAgent($api)
    {
        $api = strtolower($api);

        if (!isset($this->instances[$api])) {
            switch ($api) {
                case Source::YOUTUBE:
                    $agent = new YoutubeAgent();
                    break;
                case Source::DAILYMOTION:
                    $agent = new DailymotionAgent();
                    break;
                case Source::VIMEO:
                    $agent = new VimeoAgent();
                    break;
                default:
                    throw new BadRequestHttpException($api . ' source is not available.');
                    break;
            }

            $this->instances[$api] = $agent;
        }

        return $this->instances[$api];
    }
}
