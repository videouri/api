<?php

namespace App\Entities;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

use App\Traits\PresentableTrait;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword, PresentableTrait;

    /**
     * [$presenter description]
     * @var string
     */
    protected $presenter = 'App\Presenters\User';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password',
        'avatar', 'provider', 'provider_id'
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
