<?php

namespace App\Jobs;

use App\Entities\Search;
use App\Entities\User;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RegisterSearch extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * @var string
     */
    private $searchTerm;

    /**
     * @var User
     */
    private $user;

    /**
     * @param string $searchTerm
     * @param User $user
     */
    public function __construct($searchTerm, User $user = null)
    {
        $this->searchTerm = $searchTerm;
        $this->user = $user;
    }

    /**
     * @return bool
     */
    public function handle()
    {
        $searchHistory = new Search;

        $searchHistory->term = $this->searchTerm;
        $searchHistory->user_id = isset($this->user->id) ? $this->user->id : null;

        return $searchHistory->save();
    }
}
