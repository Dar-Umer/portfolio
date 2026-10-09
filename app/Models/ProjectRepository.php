<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectRepository extends Model
{
    protected $fillable = [
        'project_id',
        'repo_owner',
        'repo_name',
        'default_branch',
        'repo_url',
        'access_token',
        'webhook_secret',
        'last_synced_at',
        'last_sync_status',
        'latest_release_tag',
        'latest_release_published_at',
        'is_active',
    ];

    protected $casts = [
        'last_synced_at' => 'datetime',
        'latest_release_published_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'access_token',
        'webhook_secret',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function syncLogs(): HasMany
    {
        return $this->hasMany(GithubSyncLog::class);
    }
}
