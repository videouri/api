<?php

namespace Videouri\Services;

/**
 * @package Videouri\Services
 */
class FakeContentGenerator
{
    /**
     * @var array
     */
    private $apis = ['Youtube', 'Dailymotion', 'Vimeo'];

    /**
     * @var array
     */
    private $thumbnails = ['image1.jpg', 'image1.jpg', 'image3.jpg', 'image4.jpg'];

    /**
     * @return array
     */
    public function videos($ammount = 10)
    {
        $videos = [];

        for ($i = 0; $i < $ammount; $i++) {
            $id = str_random(5);
            $videos[] = [
                'id' => $id,
                'title' => str_random(10),
                'description' => str_random(30),
                'source' => $this->apis[array_rand($this->apis)],
                'url' => videouri_url('/video/fake' . $id),
                'thumbnail' => asset('image/fake/' . $this->thumbnails[array_rand($this->thumbnails)]),
            ];
        }

        return $videos;
    }
}
