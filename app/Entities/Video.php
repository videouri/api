<?php

namespace Videouri\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * @package Videouri\Entities
 */
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
        'provider',
        'original_id',
        'custom_id',
        'original_url',
        'slug', // @TODO add slug for video
        'author',
        'title',
        'description',
        'thumbnail',
        'views',
        'duration',
        'categories',
        'tags',
        'dmca_claim'
    ];

    //////////////
    // Mutators //
    //////////////

    /**
     * @return array
     */
    public function getTagsAttribute()
    {
        $tags = $this->attributes['tags'];

        if (!is_array($this->attributes['tags'])) {
            $tags = json_decode($this->attributes['tags']);
        }

        return $tags;
    }

    /**
     * @return string
     */
    public function getCustomUrlAttribute()
    {
        return videouri_url('/video/' . $this->custom_id);
    }

    /////////////
    // Helpers //
    /////////////

    /**
     * @param $userId
     *
     * @return bool
     */
    public function savedForLater($userId)
    {
        if ($this->watchLater()->where('user_id', '=', $userId)->first()) {
            return true;
        }

        return false;
    }

    /**
     * @param $userId
     *
     * @return bool
     */
    public function isFavorited($userId)
    {
        if ($this->favorited()->where('user_id', '=', $userId)->first()) {
            return true;
        }

        return false;
    }

    ///////////////
    // Relations //
    ///////////////

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function watchers()
    {
        return $this->belongsToMany(User::class, 'views');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function favorited()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function watchLater()
    {
        return $this->belongsToMany(User::class, 'watch_later');
    }
}
