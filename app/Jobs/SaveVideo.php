<?php

namespace Videouri\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use Videouri\Entities\Video;

/**
 * @package Videouri\Jobs
 */
class SaveVideo extends Job implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * @var Video
     */
    private $video;

    /**
     * @param Video $video
     */
    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $originalId = $this->video->getAttribute('original_id');
        $video = Video::where('original_id', '=', $originalId)->first();

        if (!$video) {
            if ($this->video->save()) {
                Log::info('SaveVideo: Successfully saved new video.', [
                    'provider' => $this->video->getAttribute('provider'),
                    'original_id' => $this->video->getAttribute('original_id')
                ]);
            } else {
                Log::error('SaveVideo: Error whilst trying to save new video.', [
                    'provider' => $this->video->getAttribute('provider'),
                    'original_id' => $this->video->getAttribute('original_id')
                ]);
            }
        }
    }
}
