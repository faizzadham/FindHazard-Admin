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
        Schema::table('trainee_results', function (Blueprint $table) {
            $table->decimal('score', 5, 1)->default(0)->change();
            $table->decimal('completion_time', 6, 1)->default(0)->change();

            if (!Schema::hasColumn('trainee_results', 'wrong_clicks')) {
                $table->integer('wrong_clicks')->default(0)->after('hazards_missed');
            }
            if (!Schema::hasColumn('trainee_results', 'is_certified')) {
                $table->boolean('is_certified')->default(false)->after('performance_rating');
            }
            if (!Schema::hasColumn('trainee_results', 'found_hazards')) {
                $table->json('found_hazards')->nullable()->after('wrong_clicks');
            }
            if (!Schema::hasColumn('trainee_results', 'missed_hazards')) {
                $table->json('missed_hazards')->nullable()->after('found_hazards');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trainee_results', function (Blueprint $table) {
            if (Schema::hasColumn('trainee_results', 'wrong_clicks')) {
                $table->dropColumn('wrong_clicks');
            }
            if (Schema::hasColumn('trainee_results', 'is_certified')) {
                $table->dropColumn('is_certified');
            }
        });
    }
};
