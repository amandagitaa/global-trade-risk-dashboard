<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('country_comparisons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('country_a_id')->constrained('countries')->onDelete('cascade');
            $table->foreignId('country_b_id')->constrained('countries')->onDelete('cascade');
            $table->decimal('risk_score_a', 5, 2)->nullable();
            $table->decimal('risk_score_b', 5, 2)->nullable();
            $table->string('recommended_country')->nullable();
            $table->text('recommendation')->nullable();
            $table->json('comparison_result')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('country_comparisons');
    }
};
