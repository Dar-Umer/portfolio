<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ticket_number',
        'client_profile_id',
        'project_id',
        'ticket_category_id',
        'ticket_status_id',
        'assigned_to_user_id',
        'subject',
        'description',
        'priority',
        'issue_type',
        'environment',
        'app_version',
        'browser_info',
        'steps_to_reproduce',
        'expected_result',
        'actual_result',
        'first_response_due_at',
        'first_responded_at',
        'resolution_due_at',
        'resolved_at',
        'closed_at',
        'is_sla_breached',
        'root_cause',
        'fix_description',
        'fixed_version',
        'customer_instructions',
    ];

    protected $casts = [
        'first_response_due_at' => 'datetime',
        'first_responded_at' => 'datetime',
        'resolution_due_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'is_sla_breached' => 'boolean',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(ClientProfile::class, 'client_profile_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'ticket_category_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(TicketStatus::class, 'ticket_status_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class)->orderBy('created_at', 'asc');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(TicketStatusHistory::class)->orderBy('created_at', 'desc');
    }

    public function githubLinks(): HasMany
    {
        return $this->hasMany(TicketGithubLink::class)->orderBy('created_at', 'desc');
    }
}
