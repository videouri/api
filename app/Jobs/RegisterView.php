<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Entities\Video;
use App\Entities\User;

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
    public function __construct($originalId, User $user)
    {
        $this->video = Video::where('original_id', '=', $originalId)->first();
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
