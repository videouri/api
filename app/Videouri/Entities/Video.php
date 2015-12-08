<?php

namespace Videouri\Entities;

use Illuminate\Database\Eloquent\Model;

use Videouri\Entities\User;
use Videouri\Entities\Favorite;

class Video extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'videos';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'provider', 'original_id', 'videouri_url',
        'author', 'title', 'description',
        'thumbnail', 'views',
        'categories', 'tags'
    ];

    public function watchers()
    {
        return $this->belongsToMany('Videouri\Entities\User', 'views');
    }
}
