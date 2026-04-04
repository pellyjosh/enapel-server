<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReplicationNode extends Model
{
    protected $fillable = [
        'node_uuid',
        'name',
        'role',
        'hostname',
        'base_url',
        'shared_secret',
        'pair_token_hash',
        'status',
        'sync_lag_seconds',
        'last_backup_run_id',
        'paired_at',
        'last_seen_at',
        'last_pull_at',
        'last_backup_at',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'shared_secret' => 'encrypted',
            'sync_lag_seconds' => 'integer',
            'paired_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'last_pull_at' => 'datetime',
            'last_backup_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    public function lastBackupRun(): BelongsTo
    {
        return $this->belongsTo(BackupRun::class, 'last_backup_run_id');
    }

    public function checkpoints(): HasMany
    {
        return $this->hasMany(ReplicationCheckpoint::class);
    }
}
