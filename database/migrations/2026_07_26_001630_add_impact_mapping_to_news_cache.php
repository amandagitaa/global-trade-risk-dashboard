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
            $table->json('mapped_countries')->nullable()->after('intelligence_summary');
            $table->json('mapped_ports')->nullable()->after('mapped_countries');
            $table->json('regional_entities')->nullable()->after('mapped_ports');
            $table->string('port_impact_type', 50)->nullable()->after('regional_entities');
            $table->string('trade_exposure_type', 100)->nullable()->after('port_impact_type');
            $table->decimal('mapping_confidence', 3, 2)->nullable()->after('trade_exposure_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news_cache', function (Blueprint $table) {
            $table->dropColumn([
                'mapped_countries',
                'mapped_ports',
                'regional_entities',
                'port_impact_type',
                'trade_exposure_type',
                'mapping_confidence'
            ]);
        });
    }
};
