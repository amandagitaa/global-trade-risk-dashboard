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
        // Safe conversion from ENUM to VARCHAR
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE articles MODIFY category VARCHAR(150) NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot reliably downgrade back to a specific ENUM without losing data, 
        // so we leave it as VARCHAR in the down method, or do nothing.
    }
};
