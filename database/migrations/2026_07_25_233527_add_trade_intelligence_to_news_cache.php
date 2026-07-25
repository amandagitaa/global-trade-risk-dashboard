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
        Schema::table('news_cache', function (Blueprint $table) {
            $table->integer('impact_score')->nullable();
            $table->string('impact_level')->nullable();
            $table->string('risk_direction')->nullable();
            $table->decimal('impact_confidence', 8, 2)->nullable();
            $table->json('affected_countries')->nullable();
            $table->json('affected_sectors')->nullable();
            $table->json('impact_factors')->nullable();
            $table->text('operational_impact')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news_cache', function (Blueprint $table) {
            $table->dropColumn([
                'impact_score',
                'impact_level',
                'risk_direction',
                'impact_confidence',
                'affected_countries',
                'affected_sectors',
                'impact_factors',
                'operational_impact'
            ]);
        });
    }
};
