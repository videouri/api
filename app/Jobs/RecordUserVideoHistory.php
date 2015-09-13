<?php

namespace App\Jobs;

use App\Jobs\Job;
use Videouri\Entities\UserVideoHistory;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecordUserVideoHistory extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $user, $video, $searchTerm;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        User $user = null,
        Video $video = null,
        SearchHistory $searchTerm = null
    ) {
        $this->user = $user;
        $this->video = $video;
        $this->searchTerm = $searchTerm;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $userVideoHistory = new UserVideoHistory;

        die;
    }
}
