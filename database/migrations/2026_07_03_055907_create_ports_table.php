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
        Schema::create('ports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('country_id')
                ->constrained('countries')
                ->onDelete('cascade');

            $table->string('port_name');
            $table->string('port_code')->nullable();
            $table->string('city')->nullable();
            $table->string('country_name_cache')->nullable();

            $table->decimal('latitude', 10, 6);
            $table->decimal('longitude', 10, 6);

            $table->integer('congestion_score')->default(0);
            $table->enum('status', ['normal', 'busy', 'congested'])->default('normal');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ports');
    }
};
