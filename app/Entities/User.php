<?php

namespace Videouri\Entities;

use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @package Videouri\Entities
 */
class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'avatar',
        'provider',
        'provider_id',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function favorites()
    {
        return $this->belongsToMany(Video::class, 'favorites');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function watchLater()
    {
        return $this->belongsToMany(Video::class, 'watch_later');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function videosWatched()
    {
        return $this->belongsToMany(Video::class, 'views');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function searches()
    {
        return $this->hasMany(Search::class);
    }
}
