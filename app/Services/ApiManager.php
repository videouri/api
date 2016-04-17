<?php

namespace App\Services;

use App\Transformers\VideoTransformer;

use League\Fractal\Manager as FractalManager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\ArraySerializer;

abstract class ApiManager
{
    /**
     * @uses $avialableContents
     * @var string
     */
    protected $videoContent = null;

    /**
     * Custom wrapper to manage fractal transforms
     *
     * @param  array $videos
     * @return array
     */
    public function transformVideos($videos)
    {
        if (is_array($videos) && count($videos) > 0) {
            $resource = new Collection($videos, new VideoTransformer());
        } else {
            $resource = new Item($videos, new VideoTransformer());
        }

        // Create a top level instance somewhere
        $fractalManager = new FractalManager();
        // $fractalManager->setSerializer(new ArraySerializer());

        $videos = $fractalManager->createData($resource)->toArray();

        return $videos['data'];
    }

    /**
     * @param $text
     * @return string
     */
    protected function parseDescription($text)
    {
        if ($this->content !== 'getVideoEntry') {
            $text = str_limit($text, 90);
        }

        return $text;
    }
}