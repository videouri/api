<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class View extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'views';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('App\Entities\User');
    }

    public function video()
    {
        return $this->belongsTo('App\Entities\Video');
    }
}
