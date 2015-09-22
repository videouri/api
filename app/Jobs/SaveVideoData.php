<?php

namespace App\Jobs;

use App\Jobs\Job;
use Videouri\Entities\Video;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

class SaveVideoData extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $videoData, $provider;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($videoData, $provider)
    {
        $this->videoData = $videoData;
        $this->provider  = $provider;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $video = Video::where('original_id', '=', $this->videoData['origId'])
                            ->where('duration', '=', 0)
                            ->first();
        
        if (!$video) {
            $video = new Video();
        }

        $video->provider     = $this->provider;
        $video->original_id  = $this->videoData['origId'];
        $video->videouri_url = url('/video/' . $this->videoData['customId']);
        $video->title        = $this->videoData['title'];

        if (!empty($this->videoData['description']))
            $video->description = $this->videoData['description'];

        $video->thumbnail = $this->videoData['thumbnail'];

        if ($this->videoData['views'] > 0)
            $video->views = $this->videoData['views'];

        if ($this->videoData['duration'] > 0)
            $video->duration = $this->videoData['duration'];

        $video->categories = null;
        $video->tags = json_encode($this->videoData['tags']);

        $video->save();

        return $video;
    }
}
