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
        if (Video::where('original_id', '=', $this->videoData['origId'])->first())
            return;

        $video = new Video([
            'provider'    => $this->provider,
            'original_id' => $this->videoData['origId'],
            'title'       => $this->videoData['title'],
            'description' => $this->videoData['description'],
            'thumbnail'   => $this->videoData['thumbnail'],
            'views'       => $this->videoData['views'],
            'categories'  => null,
            'tags'        => json_encode($this->videoData['tags']),
        ]);

        $video->save();
    }
}
