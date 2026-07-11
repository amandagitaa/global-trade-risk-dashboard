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
        Schema::create('risk_scores', function (Blueprint $table) {
            $table->id();

            $table->foreignId('country_id')
                ->constrained('countries')
                ->onDelete('cascade');

            $table->decimal('weather_score', 8, 2)->default(0);
            $table->decimal('port_score', 8, 2)->default(0);
            $table->decimal('currency_score', 8, 2)->default(0);
            $table->decimal('economic_score', 8, 2)->default(0);
            $table->decimal('news_score', 8, 2)->default(0);

            $table->decimal('final_score', 8, 2)->default(0);

            $table->enum('risk_level', [
                'safe',
                'stable',
                'alert',
                'dangerous',
                'critical'
            ])->default('safe');

            $table->text('reason')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_scores');
    }
};
