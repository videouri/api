<?php

namespace App\Entities;

use App\Traits\PresentableTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use PresentableTrait;

    /**
     * @var \App\Presenters\User
     */
    protected $presenter = 'App\Presenters\User';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password',
        'avatar', 'provider', 'provider_id',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    ////////////////
    // FUNCTIONAL //
    ////////////////

    public function favorites()
    {
        return $this->belongsToMany(Video::class, 'favorites');
    }

    public function watchLater()
    {
        return $this->belongsToMany(Video::class, 'watch_later');
    }

    /////////////
    // HISTORY //
    /////////////

    public function videosWatched()
    {
        return $this->belongsToMany(Video::class, 'views')->orderBy('registered_at', 'desc');
    }

    public function searches()
    {
        return $this->hasMany(Search::class)->orderBy('registered_at', 'desc');
    }
}
