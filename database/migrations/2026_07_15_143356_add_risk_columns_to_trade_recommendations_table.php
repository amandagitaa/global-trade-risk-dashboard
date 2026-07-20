<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trade_recommendations', function (Blueprint $table) {

            $table->string('risk_level')
                ->nullable()
                ->after('business_reason');

            $table->decimal('risk_score', 5, 2)
                ->default(0)
                ->after('risk_level');

        });
    }

    public function down(): void
    {
        Schema::table('trade_recommendations', function (Blueprint $table) {

            $table->dropColumn([
                'risk_level',
                'risk_score'
            ]);

        });
    }
};