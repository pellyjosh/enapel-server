<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackupRun extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_RUNNING = 'running';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';
    public const STATUS_MIRRORED = 'mirrored';
    public const STATUS_RESTORED = 'restored';

    protected $fillable = [
        'bundle_uuid',
        'type',
        'status',
        'storage_target',
        'bundle_name',
        'bundle_path',
        'cloud_bundle_url',
        'checksum',
        'size_bytes',
        'files_count',
        'database_bytes',
        'manifest',
        'included_paths',
        'deleted_paths',
        'meta',
        'error_message',
        'started_at',
        'completed_at',
        'mirrored_at',
        'restored_at',
    ];

    protected function casts(): array
    {
        return [
            'size_bytes' => 'integer',
            'files_count' => 'integer',
            'database_bytes' => 'integer',
            'manifest' => 'array',
            'included_paths' => 'array',
            'deleted_paths' => 'array',
            'meta' => 'array',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'mirrored_at' => 'datetime',
            'restored_at' => 'datetime',
        ];
    }
}
