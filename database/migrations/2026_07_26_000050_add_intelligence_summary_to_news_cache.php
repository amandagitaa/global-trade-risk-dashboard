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
            $table->text('intelligence_summary')->nullable()->after('operational_impact');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news_cache', function (Blueprint $table) {
            $table->dropColumn('intelligence_summary');
        });
    }
};
