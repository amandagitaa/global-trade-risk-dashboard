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
        Schema::create('ships', function (Blueprint $table) {

            $table->id();

            $table->foreignId('current_port_id')
                ->constrained('ports')
                ->cascadeOnDelete();

            $table->foreignId('destination_port_id')
                ->constrained('ports')
                ->cascadeOnDelete();

            $table->string('imo_number')->unique();

            $table->string('ship_name');

            $table->string('ship_type');

            $table->integer('capacity_teu');

            $table->integer('cargo_percentage');

            $table->decimal('speed_knots',5,2);

            $table->integer('eta_days');

            $table->enum('status',[
                'Docked',
                'Loading',
                'Sailing',
                'Delayed'
            ]);

            $table->decimal('latitude',10,6);

            $table->decimal('longitude',10,6);

            $table->timestamps();

        });
    }
};
