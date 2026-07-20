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
        Schema::table('articles', function (Blueprint $table) {
            $table->string('author')->nullable()->after('content');
            $table->string('status')->default('Draft')->after('author');
            
            if (Schema::hasColumn('articles', 'is_published')) {
                $table->dropColumn('is_published');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['author', 'status']);
            $table->boolean('is_published')->default(false);
        });
    }
};
