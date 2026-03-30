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
        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('device_id')->nullable()->after('id')->constrained('devices')->nullOnDelete();
        });
        Schema::table('receipts', function (Blueprint $table) {
            $table->foreignId('device_id')->nullable()->after('id')->constrained('devices')->nullOnDelete();
        });
        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('device_id')->nullable()->after('id')->constrained('devices')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['device_id']);
            $table->dropColumn('device_id');
        });
        Schema::table('receipts', function (Blueprint $table) {
            $table->dropForeign(['device_id']);
            $table->dropColumn('device_id');
        });
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['device_id']);
            $table->dropColumn('device_id');
        });
    }
};
