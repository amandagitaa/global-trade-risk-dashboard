<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news_cache', function (Blueprint $table) {


            $table->integer('risk_score')
                ->default(0)
                ->after('negative_score');


            $table->string('risk_level')
                ->nullable()
                ->after('risk_score');


            $table->integer('quality_score')
                ->default(0)
                ->after('risk_level');


            $table->string('reliability_level')
                ->nullable()
                ->after('quality_score');


            $table->string('verification_status')
                ->nullable()
                ->after('reliability_level');

        });
    }


    public function down(): void
    {
        Schema::table('news_cache', function (Blueprint $table) {

    $table->dropColumn([
        'risk_score',
        'risk_level',
        'quality_score',
        'reliability_level',
        'verification_status',
    ]);

});
    }
};