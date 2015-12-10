<?php

namespace Videouri\Entities;

use Illuminate\Database\Eloquent\Model;

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
        'provider', 'original_id', 'custom_id', 'slug',
        'author', 'title', 'description',
        'thumbnail', 'views',
        'categories', 'tags'
    ];

    public function getCustomUrlAttribute()
    {
        return url('/video/' . $this->custom_id);
    }

    public function watchers()
    {
        return $this->belongsToMany('Videouri\Entities\User', 'views');
    }

    public function favorited()
    {
        return $this->belongsToMany('Videouri\Entities\Favorite', 'favorites');
    }

    public function latered()
    {
        return $this->belongsToMany('Videouri\Entities\Later', 'watch_later');
    }
}
