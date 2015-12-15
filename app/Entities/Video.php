<?php

namespace App\Entities;

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
        'provider', 'original_id', 'custom_id',
        'original_url', 'slug',
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
        return $this->belongsToMany('App\Entities\User', 'views');
    }

    public function favorited()
    {
        return $this->belongsToMany('App\Entities\Favorite', 'favorites');
    }

    public function latered()
    {
        return $this->belongsToMany('App\Entities\Later', 'watch_later');
    }
}
