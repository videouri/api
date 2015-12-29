<?php

namespace App\Transformers;

use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;
use App\Entities\Video;
use Auth;

class VideoTransformer extends TransformerAbstract
{
    public function transform(Video $video)
    {
        $response = [
            'id'           => (int) $video->id,
            'provider'     => $video->provider,

            'original_id'  => $video->original_id,
            'custom_id'    => $video->custom_id,

            'original_url' => $video->original_url,
            'custom_url'   => $video->custom_url,

            'title'        => $video->title,
            'description'  => $video->description,
            'thumbnail'    => $video->thumbnail,
            'views'        => (int) $video->views,
            'duration'     => (int) $video->duration,
            'tags'         => $video->tags,
        ];

        $isFavorited = false;
        $savedForLater = false;

        if ($user = Auth::user()) {
            if ($video->favorited()->whereUserId($user->id)->first()) {
                $isFavorited = true;
            }

            if ($video->watchLater()->whereUserId($user->id)->first()) {
                $savedForLater = true;
            }
        }

        $response['favorited'] = $isFavorited;
        $response['saved_for_later'] = $savedForLater;

        return $response;
    }
}
