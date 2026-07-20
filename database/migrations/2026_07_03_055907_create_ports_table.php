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
        Schema::create('ports', function (Blueprint $table) {

            $table->id();

            $table->string('country_iso2', 2);
            $table->string('country_name');

            $table->string('name');
            $table->string('code')->nullable();

            $table->string('city')->nullable();
            $table->string('region');

            $table->decimal('latitude',10,7);
            $table->decimal('longitude',10,7);

            /*
            |--------------------------------------------------------------------------
            | Port Information
            |--------------------------------------------------------------------------
            */

            $table->string('port_type')
                ->default('Container Port');

            $table->string('status')
                ->default('Operational');

            /*
            |--------------------------------------------------------------------------
            | Capacity
            |--------------------------------------------------------------------------
            */

            $table->string('annual_capacity')->nullable();

            $table->integer('teu_capacity')
                ->default(0);

            $table->decimal('trade_volume', 12, 2)
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Intelligence
            |--------------------------------------------------------------------------
            */

            $table->integer('importance_score')
                ->default(0);

            $table->integer('risk_score')
                ->default(0);

            $table->string('risk_level')
                ->default('Low');

            $table->integer('weather_risk')
                ->default(0);

            $table->integer('political_risk')
                ->default(0);

            $table->integer('logistic_risk')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Description
            |--------------------------------------------------------------------------
            */

            $table->text('main_industries')->nullable();

            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ports');
    }
};
