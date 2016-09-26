<?php

namespace Videouri\Entities;

use Cocur\Slugify\Slugify;
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

        'author',
        'duration',
        'views',
        'likes',
        'dislikes',

        'data',
        'dmca_claim'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'custom_url'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'dmca_claim'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
        'dmca_claim' => 'boolean'
    ];

    //////////////
    // Mutators //
    //////////////

    /**
     * @return string
     */
    public function getCustomUrlAttribute()
    {
        $slugify = new Slugify();
        $slug = $slugify->slugify($this->getAttribute('data')['title']);

        return videouri_url('/video/' . $slug . '/' . $this->getAttribute('custom_id'));
    }

    /////////////
    // Helpers //
    /////////////

    /**
     * @param integer $userId
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
    public function isFavorite($userId)
    {
        if ($this->favorite()->where('user_id', '=', $userId)->first()) {
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
    public function favorite()
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
