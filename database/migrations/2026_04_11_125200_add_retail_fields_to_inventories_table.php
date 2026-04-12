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
        Schema::table('inventories', function (Blueprint $table) {
            $table->decimal('cost_price', 15, 2)->default(0)->after('price');
            $table->string('unit_name')->default('Piece')->after('name');
            $table->integer('units_per_pack')->default(1)->after('unit_name');
            $table->decimal('pack_price_override', 15, 2)->nullable()->after('units_per_pack');
            $table->foreignId('parent_id')->nullable()->constrained('inventories')->onDelete('cascade')->after('id');
            $table->string('variation_name')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn([
                'cost_price',
                'unit_name',
                'units_per_pack',
                'pack_price_override',
                'parent_id',
                'variation_name'
            ]);
        });
    }
};
