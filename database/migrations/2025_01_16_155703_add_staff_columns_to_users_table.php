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
        Schema::table('users', function (Blueprint $table) {
            $table->string('staffid')->unique()->nullable()->after('id'); 
            $table->string('phone')->nullable()->after('password'); 
            $table->string('designation')->nullable()->after('phone');
            $table->string('role')->default('user')->after('designation');
            $table->date('dob')->nullable()->after('role'); 
            $table->decimal('salary', 10, 2)->nullable()->after('dob');
            $table->boolean('is_admin')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['staffid', 'phone', 'designation', 'role', 'dob', 'salary']);
        });
    }
};
