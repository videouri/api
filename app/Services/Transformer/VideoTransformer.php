<?php

namespace Videouri\Services\Transformer;

use League\Fractal\TransformerAbstract;
use Videouri\Entities\Video;
use Auth;

/**
 * @package Videouri\Services\Transformer
 */
class VideoTransformer extends TransformerAbstract
{
    /**
     * @param Video $video
     *
     * @return array
     */
    public function transform(Video $video)
    {

        $data = $video->getAttribute('data');

        $response = [
            'provider' => $video->getAttribute('provider'),
            'original_id' => $video->getAttribute('original_id'),
            'custom_id' => $video->getAttribute('custom_id'),
            'custom_url' => $video->getAttribute('custom_url'),

            'author' => $video->getAttribute('author'),
            'duration' => (int) $video->getAttribute('duration'),
            'views' => humanizeNumber($video->getAttribute('views')),
            'ratings' => (int) $video->getAttribute('ratings'),

            'data' => $data
        ];

        $isFavorite = false;
        $savedForLater = false;

        if ($user = Auth::user()) {
            if ($video->favorite()->where('user_id', '=', $user->id)->first()) {
                $isFavorite = true;
            }

            if ($video->watchLater()->where('user_id', '=', $user->id)->first()) {
                $savedForLater = true;
            }
        }

        $response['favorite'] = $isFavorite;
        $response['saved_for_later'] = $savedForLater;

        return $response;
    }
}
