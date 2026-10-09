<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketStatus extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'color',
        'is_initial',
        'is_resolved',
        'is_closed',
        'allowed_transitions',
        'sort_order',
    ];

    protected $casts = [
        'is_initial' => 'boolean',
        'is_resolved' => 'boolean',
        'is_closed' => 'boolean',
        'allowed_transitions' => 'array',
        'sort_order' => 'integer',
    ];

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
