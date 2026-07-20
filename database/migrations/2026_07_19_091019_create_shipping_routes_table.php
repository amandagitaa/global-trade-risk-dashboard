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
        Schema::create('shipping_routes', function (Blueprint $table) {

            $table->id();

            $table->foreignId('origin_port_id')
                ->constrained('ports')
                ->cascadeOnDelete();

            $table->foreignId('destination_port_id')
                ->constrained('ports')
                ->cascadeOnDelete();

            $table->integer('distance_km');

            $table->integer('estimated_days');

            $table->decimal('shipping_cost',12,2);

            $table->enum('risk_level',[
                'Low',
                'Medium',
                'High'
            ]);

            $table->integer('weather_risk');

            $table->integer('piracy_risk');

            $table->string('status');

            $table->text('ai_recommendation');

            $table->timestamps();

        });
    }
};
