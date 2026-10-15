<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainee_results', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->decimal('score', 5, 1)->default(0.0);
            $table->integer('hazards_found')->default(0);
            $table->integer('hazards_missed')->default(0);
            $table->integer('wrong_clicks')->default(0);
            $table->float('completion_time')->default(0.0); // stored in seconds
            $table->string('performance_rating')->default('Needs Review'); // "Locked In", "Great", "Valid Effort", "Needs Review"
            $table->boolean('is_certified')->default(false);
            $table->json('found_hazards')->nullable();
            $table->json('missed_hazards')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainee_results');
    }
};
