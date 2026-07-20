<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('watchlists', function (Blueprint $table) {
            $table->string('watch_type')->default('country')->after('user_id');
            $table->foreignId('port_id')->nullable()->constrained('ports')->onDelete('cascade')->after('country_id');
            $table->foreignId('route_id')->nullable()->constrained('shipping_routes')->onDelete('cascade')->after('port_id');
            $table->string('status')->default('monitoring')->after('route_id');
            
            // Note: country_id is already in the watchlists table, let's make sure it's nullable if we add port/route
            $table->unsignedBigInteger('country_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('watchlists', function (Blueprint $table) {
            $table->dropForeign(['port_id']);
            $table->dropForeign(['route_id']);
            $table->dropColumn(['watch_type', 'port_id', 'route_id', 'status']);
            $table->unsignedBigInteger('country_id')->nullable(false)->change();
        });
    }
};
