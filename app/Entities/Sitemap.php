<?php

namespace Videouri\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * @package Videouri\Entities
 */
class Sitemap extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sitemaps';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['path', 'filename', 'items_count'];
}
