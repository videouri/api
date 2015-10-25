<?php

namespace Videouri\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 */
class UserVideoHistory extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_video_history';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}