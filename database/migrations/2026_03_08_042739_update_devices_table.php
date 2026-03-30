<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->string('name')->after('id')->nullable();
            $table->string('identifier')->unique()->after('name')->nullable();
            $table->enum('status', ['active', 'disabled', 'locked'])->default('active')->after('identifier');
            $table->timestamp('last_active_at')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn(['name', 'identifier', 'status', 'last_active_at']);
        });
    }
};
