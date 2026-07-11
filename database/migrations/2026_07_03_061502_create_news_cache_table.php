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
        Schema::create('news_cache', function (Blueprint $table) {
            $table->id();

            $table->foreignId('country_id')
                ->nullable()
                ->constrained('countries')
                ->nullOnDelete();

            $table->string('title');
            $table->text('description')->nullable();
            $table->string('source')->nullable();
            $table->text('url')->nullable();

            $table->enum('category', [
                'logistics',
                'trade',
                'shipping',
                'economy',
                'geopolitics'
            ])->default('economy');

            $table->integer('positive_score')->default(0);
            $table->integer('negative_score')->default(0);

            $table->enum('sentiment', [
                'positive',
                'neutral',
                'negative'
            ])->default('neutral');

            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news_cache');
    }
};
