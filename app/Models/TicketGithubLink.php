<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketGithubLink extends Model
{
    protected $fillable = [
        'ticket_id',
        'link_type',
        'ref_identifier',
        'ref_url',
        'title',
        'status',
        'created_by_user_id',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
