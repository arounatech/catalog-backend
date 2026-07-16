<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
        use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'price',
        'image',
        'status',
        'sort_order',
    ];
}
