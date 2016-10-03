<?php

namespace Videouri\Http\Controllers\Api;

use Videouri\Maps\Source;

/**
 * @package Videouri\Http\Controllers\Api
 */
class RecommendationController extends ApiController
{
    /**
     * @param string $customId
     *
     * @return array
     */
    public function forVideo($customId)
    {
        $api = substr($customId, 1, 1);
        $originalId = substr_replace($customId, '', 1, 1);

        switch ($api) {
            case 'd':
                $api = Source::DAILYMOTION;
                break;

            case 'v':
                $api = Source::VIMEO;
                break;

            case 'y':
                $api = Source::YOUTUBE;
                break;

            default:
                return [];
                break;
        }

        $videos = $this->scout->getRelated($api, $originalId);

        return response($videos);
    }
}
