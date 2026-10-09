<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'title',
        'icon',
        'short_description',
        'description',
        'starting_price',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'starting_price' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
