<?php

namespace App\Transformers;

use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;
use Videouri\Entities\Video;

class VideoTransformer extends TransformerAbstract
{
    public function transform(Video $video)
    {
        return [
            'id'          => (int) $video->id,
            'provider'    => $video->provider,
            'original_id' => $video->original_id,
            'custom_id'   => $video->custom_id,
            'custom_url'  => $video->custom_url,
            'title'       => $video->title,
            'description' => $video->description,
            'thumbnail'   => $video->thumbnail,
            'views'       => (int) $video->views,
            'duration'    => (int) $video->duration,
            'tags'        => $video->tags
        ];
    }
}
