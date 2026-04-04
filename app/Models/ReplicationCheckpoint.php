<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReplicationCheckpoint extends Model
{
    protected $fillable = [
        'checkpoint_uuid',
        'replication_node_id',
        'backup_run_id',
        'type',
        'status',
        'source_bundle_uuid',
        'source_bundle_path',
        'sync_age_seconds',
        'pulled_at',
        'applied_at',
        'manifest',
        'meta',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'sync_age_seconds' => 'integer',
            'pulled_at' => 'datetime',
            'applied_at' => 'datetime',
            'manifest' => 'array',
            'meta' => 'array',
        ];
    }

    public function replicationNode(): BelongsTo
    {
        return $this->belongsTo(ReplicationNode::class);
    }

    public function backupRun(): BelongsTo
    {
        return $this->belongsTo(BackupRun::class);
    }
}
