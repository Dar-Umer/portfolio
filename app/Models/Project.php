<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'client_profile_id',
        'project_number',
        'name',
        'slug',
        'category',
        'description',
        'start_date',
        'expected_delivery_date',
        'actual_completion_date',
        'status',
        'priority',
        'progress_percentage',
        'budget',
        'technologies',
        'current_deployed_version',
        'internal_notes',
    ];

    protected $casts = [
        'technologies' => 'array',
        'start_date' => 'date',
        'expected_delivery_date' => 'date',
        'actual_completion_date' => 'date',
        'budget' => 'decimal:2',
        'progress_percentage' => 'integer',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(ClientProfile::class, 'client_profile_id');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(ProjectMilestone::class)->orderBy('sort_order');
    }

    public function timelineEvents(): HasMany
    {
        return $this->hasMany(ProjectTimelineEvent::class)->orderBy('created_at', 'desc');
    }

    public function members(): HasMany
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function repositories(): HasMany
    {
        return $this->hasMany(ProjectRepository::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function recalculateProgress(): int
    {
        $milestones = $this->milestones()->get();
        if ($milestones->isEmpty()) {
            return $this->progress_percentage;
        }

        $total = $milestones->count();
        $completed = $milestones->where('status', 'completed')->count();
        $inProgress = $milestones->where('status', 'in_progress')->count();

        $percentage = (int) round(($completed * 100 + $inProgress * 50) / $total);
        $percentage = min(100, max(0, $percentage));

        $this->update(['progress_percentage' => $percentage]);

        return $percentage;
    }
}
