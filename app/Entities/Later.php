<?php

namespace Videouri\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * @package Videouri\Entities
 */
class Later extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'watch_later';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('Videouri\Entities\User');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function video()
    {
        return $this->belongsTo('Videouri\Entities\Video');
    }
}
