<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Entities\Video;
use App\Entities\User;

/**
 * Class SaveVideo
 * @package App\Jobs
 */
class SaveVideo extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * @var array
     */
    private $data;

    /**
     * @var string
     */
    private $provider;

    /**
     * @param array $data
     * @param string $provider
     */
    public function __construct($data, $provider)
    {
        $this->data = $data;
        $this->provider = $provider;
    }

    /**
     * @return bool
     */
    public function handle()
    {
        $video = Video::where('original_id', '=', $this->data['original_id'])->first();

        if ($video) {
            return true;
        }

        $video = new Video;

        $video->provider = $this->provider;
        $video->original_id = $this->data['original_id'];
        $video->custom_id = $this->data['custom_id'];

        $video->original_url = $this->data['original_url'];
        // $video->slug = null;

        $video->title = $this->data['title'];

        if (!empty($this->data['description'])) {
            $video->description = $this->data['description'];
        }

        $video->thumbnail = $this->data['thumbnail'];

        if ($this->data['views'] > 0) {
            $video->views = $this->data['views'];
        }

        if ($this->data['duration'] > 0) {
            $video->duration = $this->data['duration'];
        }

        $video->categories = null;
        $video->tags = json_encode($this->data['tags']);

        return $video->save();
    }
}
