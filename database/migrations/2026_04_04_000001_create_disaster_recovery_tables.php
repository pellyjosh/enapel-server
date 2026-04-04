<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('disaster_recovery_settings', function (Blueprint $table) {
            $table->id();
            $table->string('node_name')->nullable();
            $table->string('node_role')->default('primary');
            $table->string('service_hostname')->nullable();
            $table->text('nas_path')->nullable();
            $table->boolean('cloud_mirror_enabled')->default(false);
            $table->string('cloud_mirror_url')->nullable();
            $table->text('cloud_mirror_token')->nullable();
            $table->unsignedInteger('snapshot_interval_minutes')->default(15);
            $table->unsignedTinyInteger('full_backup_hour')->default(2);
            $table->unsignedTinyInteger('monthly_backup_hour')->default(3);
            $table->unsignedInteger('retention_snapshot_days')->default(7);
            $table->unsignedInteger('retention_daily_backups')->default(30);
            $table->unsignedInteger('retention_monthly_backups')->default(12);
            $table->boolean('standby_enabled')->default(false);
            $table->string('standby_primary_url')->nullable();
            $table->text('standby_pairing_token')->nullable();
            $table->timestamp('last_successful_snapshot_at')->nullable();
            $table->timestamp('last_successful_full_backup_at')->nullable();
            $table->timestamp('last_cloud_mirror_at')->nullable();
            $table->timestamp('last_standby_seen_at')->nullable();
            $table->text('encrypted_passphrase')->nullable();
            $table->string('passphrase_hint')->nullable();
            $table->string('dr_passphrase_hash')->nullable();
            $table->json('health_warnings')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('backup_runs', function (Blueprint $table) {
            $table->id();
            $table->uuid('bundle_uuid')->unique();
            $table->string('type');
            $table->string('status')->default('pending');
            $table->string('storage_target')->default('nas');
            $table->string('bundle_name');
            $table->text('bundle_path')->nullable();
            $table->text('cloud_bundle_url')->nullable();
            $table->string('checksum')->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->unsignedInteger('files_count')->default(0);
            $table->unsignedBigInteger('database_bytes')->nullable();
            $table->json('manifest')->nullable();
            $table->json('included_paths')->nullable();
            $table->json('deleted_paths')->nullable();
            $table->json('meta')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('mirrored_at')->nullable();
            $table->timestamp('restored_at')->nullable();
            $table->timestamps();
        });

        Schema::create('replication_nodes', function (Blueprint $table) {
            $table->id();
            $table->uuid('node_uuid')->unique();
            $table->string('name');
            $table->string('role')->default('standby');
            $table->string('hostname')->nullable();
            $table->string('base_url')->nullable();
            $table->text('shared_secret')->nullable();
            $table->string('pair_token_hash')->nullable();
            $table->string('status')->default('unpaired');
            $table->unsignedInteger('sync_lag_seconds')->nullable();
            $table->foreignId('last_backup_run_id')->nullable()->constrained('backup_runs')->nullOnDelete();
            $table->timestamp('paired_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('last_pull_at')->nullable();
            $table->timestamp('last_backup_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('replication_checkpoints', function (Blueprint $table) {
            $table->id();
            $table->uuid('checkpoint_uuid')->unique();
            $table->foreignId('replication_node_id')->nullable()->constrained('replication_nodes')->nullOnDelete();
            $table->foreignId('backup_run_id')->nullable()->constrained('backup_runs')->nullOnDelete();
            $table->string('type');
            $table->string('status')->default('pending');
            $table->string('source_bundle_uuid')->nullable();
            $table->text('source_bundle_path')->nullable();
            $table->unsignedInteger('sync_age_seconds')->nullable();
            $table->timestamp('pulled_at')->nullable();
            $table->timestamp('applied_at')->nullable();
            $table->json('manifest')->nullable();
            $table->json('meta')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('replication_checkpoints');
        Schema::dropIfExists('replication_nodes');
        Schema::dropIfExists('backup_runs');
        Schema::dropIfExists('disaster_recovery_settings');
    }
};
