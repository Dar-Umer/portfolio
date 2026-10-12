<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectRequirement extends Model
{
    protected $fillable = [
        'project_id',
        'user_id',
        'title',
        'details',
        'priority',
        'status',
        'converted_milestone_id',
        'staff_feedback',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function convertedMilestone(): BelongsTo
    {
        return $this->belongsTo(ProjectMilestone::class, 'converted_milestone_id');
    }
}
