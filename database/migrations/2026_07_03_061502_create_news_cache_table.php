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

            /*
            |--------------------------------------------------------------------------
            | Country
            |--------------------------------------------------------------------------
            */

            $table->foreignId('country_id')
                ->nullable()
                ->constrained('countries')
                ->nullOnDelete();

            $table->string('country_name')->nullable();

            /*
            |--------------------------------------------------------------------------
            | News Information
            |--------------------------------------------------------------------------
            */

            $table->string('title');

            $table->text('description')->nullable();

            $table->longText('content')->nullable();

            $table->text('url');

            $table->text('image_url')->nullable();

            $table->string('author')->nullable();

            $table->string('source')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Category
            |--------------------------------------------------------------------------
            */

            $table->enum('category',[
                'trade',
                'shipping',
                'logistics',
                'economy',
                'geopolitics',
                'technology',
                'manufacturing',
                'energy'
            ])->default('trade');

            /*
            |--------------------------------------------------------------------------
            | AI Sentiment
            |--------------------------------------------------------------------------
            */

            $table->integer('positive_score')->default(0);

            $table->integer('negative_score')->default(0);

            $table->enum('sentiment',[
                'positive',
                'neutral',
                'negative'
            ])->default('neutral');

            /*
            |--------------------------------------------------------------------------
            | News API
            |--------------------------------------------------------------------------
            */

            $table->timestamp('published_at')->nullable();

            /*
            |--------------------------------------------------------------------------
            | System
            |--------------------------------------------------------------------------
            */

            $table->timestamps();

            $table->index('country_id');
            $table->index('published_at');
            $table->index('sentiment');
            $table->index('category');
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