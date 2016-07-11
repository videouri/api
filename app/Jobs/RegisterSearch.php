<?php

namespace Videouri\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Videouri\Entities\Search;
use Videouri\Entities\User;

/**
 * @package Videouri\Jobs
 */
class RegisterSearch extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * @var string
     */
    private $term;

    /**
     * @var User
     */
    private $user;

    /**
     * @param string $term
     * @param User $user
     */
    public function __construct($term, User $user = null)
    {
        $this->term = $term;
        $this->user = $user;
    }

    /**
     * @return bool
     */
    public function handle()
    {
        $search = new Search;

        $search->term = $this->term;
        $search->user_id = isset($this->user->id) ? $this->user->id : null;

        return $search->save();
    }
}
