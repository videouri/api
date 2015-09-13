<?php

namespace Videouri\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * 
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
        'provider', 'original_id', 'title',
        'description', 'thumbnail', 'views',
        'categories', 'tags'
    ];
}