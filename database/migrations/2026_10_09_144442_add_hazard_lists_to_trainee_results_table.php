<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trainee_results', function (Blueprint $table) {
            $table->json('found_hazards')->nullable()->after('hazards_missed');
            $table->json('missed_hazards')->nullable()->after('found_hazards');
        });
    }

    public function down(): void
    {
        Schema::table('trainee_results', function (Blueprint $table) {
            $table->dropColumn(['found_hazards', 'missed_hazards']);
        });
    }
};
