<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortfolioProject extends Model
{
    protected $fillable = [
        'portfolio_category_id',
        'title',
        'slug',
        'short_description',
        'description',
        'featured_image',
        'gallery_images',
        'technologies',
        'client_name',
        'project_type',
        'completion_date',
        'project_url',
        'github_url',
        'status',
        'is_featured',
        'is_published',
        'seo_title',
        'seo_description',
        'sort_order',
    ];

    protected $casts = [
        'gallery_images' => 'array',
        'technologies' => 'array',
        'completion_date' => 'date',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(PortfolioCategory::class, 'portfolio_category_id');
    }
}
