<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

            Schema::table('ports', function (Blueprint $table) {

            $table->string('traffic_level')
                ->default('Medium');

            $table->text('shipping_routes')
                ->nullable();

            $table->text('ai_recommendation')
                ->nullable();

        });
    }


    public function down(): void
    {
        Schema::table('ports', function (Blueprint $table) {

            $table->dropColumn([
                'traffic_level',
                'shipping_routes',
                'ai_recommendation'
            ]);

        });
    }
};