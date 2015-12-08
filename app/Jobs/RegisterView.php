<?php

namespace App\Jobs;

use App\Jobs\Job;
use Videouri\Entities\Video;
use Videouri\Entities\User;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

class RegisterView extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $video;

    private $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($origId, User $user)
    {
        $this->video = Video::where('original_id', '=', $origId)->first();
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        return $this->video->watchers()->attach($this->user->id);
    }
}
