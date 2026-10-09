<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageSection extends Model
{
    protected $fillable = [
        'section_key',
        'title',
        'subtitle',
        'content',
        'is_enabled',
        'sort_order',
    ];

    protected $casts = [
        'content' => 'array',
        'is_enabled' => 'boolean',
        'sort_order' => 'integer',
    ];
}
