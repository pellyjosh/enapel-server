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
        Schema::table('purchases', function (Blueprint $table) {
            if (!Schema::hasColumn('purchases', 'inventory_id')) {
                $table->unsignedBigInteger('inventory_id')->nullable()->after('supplier_id');
                $table->foreign('inventory_id')->references('id')->on('inventories')->onDelete('set null');
            }
            if (!Schema::hasColumn('purchases', 'expiry_date')) {
                $table->date('expiry_date')->nullable()->after('amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropForeign(['inventory_id']);
            $table->dropColumn(['inventory_id', 'expiry_date']);
        });
    }
};
