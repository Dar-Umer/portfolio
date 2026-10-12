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
        'acceptance_criteria',
        'demo_url',
        'deliverable_type',
        'due_date',
        'completion_date',
        'client_approved_at',
        'client_review_note',
        'status',
        'progress',
        'is_client_visible',
        'sort_order',
        'requested_by_user_id',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completion_date' => 'date',
        'client_approved_at' => 'datetime',
        'progress' => 'integer',
        'is_client_visible' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }
}
