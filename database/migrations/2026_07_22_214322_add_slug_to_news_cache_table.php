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
        Schema::table('news_cache', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('title');
        });

        // Backfill existing records
        $news = \App\Models\NewsCache::all();
        foreach ($news as $item) {
            $urlStr = (string)$item->url;
            if (str_starts_with($urlStr, '/news/')) {
                $item->slug = substr($urlStr, 6);
            } else {
                $item->slug = \Illuminate\Support\Str::slug($item->title) . '-' . \Illuminate\Support\Str::random(8);
            }
            // Temporarily ignore events to avoid the original_url re-validation which we already did
            $item->saveQuietly();
        }

        // Make slug unique after backfilling
        Schema::table('news_cache', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('news_cache', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
