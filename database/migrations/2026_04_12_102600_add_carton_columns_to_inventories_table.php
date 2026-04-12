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
        Schema::table('inventories', function (Blueprint $col) {
            $col->integer('packs_per_carton')->default(1)->after('units_per_pack');
            $col->decimal('carton_price_override', 15, 2)->nullable()->after('pack_price_override');
        });

        Schema::table('sales', function (Blueprint $col) {
            $col->boolean('is_carton')->default(false)->after('is_pack');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $col) {
            $col->dropColumn(['packs_per_carton', 'carton_price_override']);
        });

        Schema::table('sales', function (Blueprint $col) {
            $col->dropColumn('is_carton');
        });
    }
};
