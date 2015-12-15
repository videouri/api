<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Entities\Video;
use App\Entities\User;

class SaveVideo extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $data;

    private $provider;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $provider)
    {
        $this->data = $data;
        $this->provider = $provider;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $video = Video::where('original_id', '=', $this->data['originalId'])->first();

        if ($video) {
            return true;
        }

        $video = new Video;

        $video->provider = $this->provider;
        $video->original_id = $this->data['originalId'];
        $video->custom_id = $this->data['customId'];
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

        $video->save();

        return $video;
    }
}
