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
        'thumbnail', 'views', 'duration',
        'categories', 'tags'
    ];

    // protected $casts = [
    // ];

    //////////////
    // Mutators //
    //////////////

    public function getTagsAttribute()
    {
        $tags = $this->attributes['tags'];

        if (!is_array($this->attributes['tags'])) {
            $tags = json_decode($this->attributes['tags']);
        }

        return $tags;
    }

    public function getCustomUrlAttribute()
    {
        return url('/video/' . $this->custom_id);
    }

    /////////////
    // Helpers //
    /////////////

    public function savedForLater($userId)
    {
        if (is_numeric($userId) && $this->watchLater()->whereUserId($userId)->first()) {
            return true;
        }

        return false;
    }

    public function isFavorited($userId)
    {
        if (is_numeric($userId) && $this->favorited()->whereUserId($userId)->first()) {
            return true;
        }

        return false;
    }

    ///////////////
    // Relations //
    ///////////////

    public function watchers()
    {
        return $this->belongsToMany(User::class, 'views');
    }

    public function favorited()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    public function watchLater()
    {
        return $this->belongsToMany(User::class, 'watch_later');
    }
}
