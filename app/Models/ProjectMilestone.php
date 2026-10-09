<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectMilestone extends Model
{
    protected $fillable = [
        'project_id',
        'title',
        'description',
        'due_date',
        'completion_date',
        'status',
        'progress',
        'is_client_visible',
        'sort_order',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completion_date' => 'date',
        'progress' => 'integer',
        'is_client_visible' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
