<?php

namespace Videouri\Entities;

use Illuminate\Database\Eloquent\Model;
use Videouri\Entities\User;

class Favorite extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_favorite_videos';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('User', 'favorites');
    }
}
