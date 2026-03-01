<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class News extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'content',
        'thumbnail',
        'published_at',
        'deleted_at'
    ];
}
