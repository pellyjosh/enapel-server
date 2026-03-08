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
        Schema::create('applications', function (Blueprint $table) {
            $table->id(); 
            $table->string('company_name');
            $table->string('email');
            $table->string('phone');
            $table->string('name');
            $table->enum('gender', ['male', 'female']);
            $table->string('logo');
            $table->string('establishment');
            $table->text('module')->nullable();
            $table->text('description');
            $table->decimal('amount', 10, 2); 
            $table->integer('duration'); 
            $table->string('license_key')->unique(); 
            $table->enum('status', ['pending', 'active', 'inactive'])->default('pending');
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
