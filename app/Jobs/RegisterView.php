<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Entities\Video;
use App\Entities\User;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class RegisterView
 * @package App\Jobs
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
     * RegisterView constructor.
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
        // @TODO Well there's an issue when this job is dispatched, without video having been cached first, so $this->video is null
        return $this->video->watchers()->attach($this->user->id);
    }
}
