<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectTimelineEvent extends Model
{
    protected $fillable = [
        'project_id',
        'user_id',
        'event_type',
        'title',
        'description',
        'is_client_visible',
    ];

    protected $casts = [
        'is_client_visible' => 'boolean',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
