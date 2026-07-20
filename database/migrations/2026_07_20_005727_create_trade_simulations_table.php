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
        Schema::create('trade_simulations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('origin_country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->foreignId('destination_country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->foreignId('origin_port_id')->nullable()->constrained('ports')->nullOnDelete();
            $table->foreignId('destination_port_id')->nullable()->constrained('ports')->nullOnDelete();
            $table->string('cargo_type')->nullable();
            $table->string('container_size')->nullable();
            $table->date('departure_date')->nullable();
            $table->double('estimated_distance')->nullable();
            $table->integer('estimated_duration')->nullable(); // in days
            $table->string('weather_impact')->nullable();
            $table->string('currency_impact')->nullable();
            $table->integer('risk_score')->nullable();
            $table->string('risk_level')->nullable();
            $table->text('ai_recommendation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trade_simulations');
    }
};
