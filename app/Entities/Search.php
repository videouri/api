<?php

namespace Videouri\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * @package Videouri\Entities
 */
class Search extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'searches';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['term', 'user_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('Videouri\Entities\User');
    }
}
