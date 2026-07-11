<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {

        Schema::create('trade_recommendations', function (Blueprint $table) {

            $table->id();


            /*
            |--------------------------------------------------------------------------
            | Country Reference
            |--------------------------------------------------------------------------
            */

            $table->foreignId('country_id')
                  ->constrained()
                  ->cascadeOnDelete();



            /*
            |--------------------------------------------------------------------------
            | Recommendation Engine Output
            |--------------------------------------------------------------------------
            */


            $table->string('trade_action');


            $table->enum('priority',[

                'low',
                'medium',
                'high',
                'critical'

            ])
            ->default('medium');



            $table->text('recommendation');


            $table->text('business_reason');



            /*
            |--------------------------------------------------------------------------
            | Generated Information
            |--------------------------------------------------------------------------
            */


            $table->timestamp('generated_at')
                  ->nullable();


            $table->timestamps();

        });

    }


    public function down(): void
    {

        Schema::dropIfExists('trade_recommendations');

    }

};