<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisasterRecoverySetting extends Model
{
    protected $fillable = [
        'node_name',
        'node_role',
        'service_hostname',
        'nas_path',
        'cloud_mirror_enabled',
        'cloud_mirror_url',
        'cloud_mirror_token',
        'snapshot_interval_minutes',
        'full_backup_hour',
        'monthly_backup_hour',
        'retention_snapshot_days',
        'retention_daily_backups',
        'retention_monthly_backups',
        'standby_enabled',
        'standby_primary_url',
        'standby_pairing_token',
        'last_successful_snapshot_at',
        'last_successful_full_backup_at',
        'last_cloud_mirror_at',
        'last_standby_seen_at',
        'encrypted_passphrase',
        'passphrase_hint',
        'dr_passphrase_hash',
        'health_warnings',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'cloud_mirror_enabled' => 'boolean',
            'snapshot_interval_minutes' => 'integer',
            'full_backup_hour' => 'integer',
            'monthly_backup_hour' => 'integer',
            'retention_snapshot_days' => 'integer',
            'retention_daily_backups' => 'integer',
            'retention_monthly_backups' => 'integer',
            'standby_enabled' => 'boolean',
            'last_successful_snapshot_at' => 'datetime',
            'last_successful_full_backup_at' => 'datetime',
            'last_cloud_mirror_at' => 'datetime',
            'last_standby_seen_at' => 'datetime',
            'cloud_mirror_token' => 'encrypted',
            'standby_pairing_token' => 'encrypted',
            'encrypted_passphrase' => 'encrypted',
            'health_warnings' => 'array',
            'meta' => 'array',
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate(
            ['id' => 1],
            [
                'node_name' => gethostname() ?: 'enapel-node',
                'node_role' => 'primary',
                'snapshot_interval_minutes' => config('disaster-recovery.snapshot_interval_minutes', 15),
                'full_backup_hour' => config('disaster-recovery.default_full_backup_hour', 2),
                'monthly_backup_hour' => config('disaster-recovery.default_monthly_backup_hour', 3),
                'retention_snapshot_days' => 7,
                'retention_daily_backups' => 30,
                'retention_monthly_backups' => 12,
                'cloud_mirror_enabled' => false,
                'standby_enabled' => false,
                'health_warnings' => [],
                'meta' => [],
            ]
        );
    }
}
