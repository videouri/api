<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Entities\Search;
use App\Entities\User;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

class RegisterSearch extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    // Variables to insert
    private $searchTerm;

    private $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($searchTerm, User $user = null)
    {
        $this->searchTerm = $searchTerm;
        $this->user       = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Do not this on local... it's pointless
        // if (env('APP_ENV') === 'local')
        //     return;

        $searchHistory = new Search;

        $searchHistory->term    = $this->searchTerm;
        $searchHistory->user_id = isset($this->user->id) ? $this->user->id : null;

        $searchHistory->save();
    }
}
