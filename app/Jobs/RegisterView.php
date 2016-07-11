<?php

namespace Videouri\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Videouri\Entities\User;
use Videouri\Entities\Video;

/**
 * @package Videouri\Jobs
 */
class RegisterView extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * @var Video
     */
    private $video;

    /**
     * @var User
     */
    private $user;

    /**
     * @param $originalId
     * @param User $user
     */
    public function __construct($originalId, User $user)
    {
        $this->video = Video::where('original_id', '=', $originalId)->first();
        $this->user = $user;
    }

    /**
     * Execute the job.
     * @return bool
     */
    public function handle()
    {
        // @TODO Well there's an issue when this job is dispatched,
        //       without video having been cached first, so $this->video is null
        return $this->video->watchers()->attach($this->user->id);
    }
}
