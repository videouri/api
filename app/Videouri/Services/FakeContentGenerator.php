<?php

namespace Videouri\Services;

class FakeContentGenerator
{
    /**
     * [$apis description]
     * @var array
     */
    private $apis = ['Youtube', 'Dailymotion', 'Metacafe', 'Vimeo'];

    /**
     * [$apis description]
     * @var array
     */
    private $thumbnails = ['image1.jpg', 'image1.jpg', 'image3.jpg', 'image4.jpg'];

    public function videos()
    {
        $videos = [];
        for ($i= 0; $i < 15; $i++) {
            $id = str_random(5);
            $videos[] = [
                'id' => $id,
                'title' => str_random(10),
                'description' => str_random(30),
                'source' => $this->apis[array_rand($this->apis)],
                'url' => url('/video/fake' . $id),
                'thumbnail' => asset('image/fake/' . $this->thumbnails[array_rand($this->thumbnails)])
            ];
        }

        return $videos;
    }
}
