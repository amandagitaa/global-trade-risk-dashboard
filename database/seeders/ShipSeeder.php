<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ship;
use App\Models\Port;

class ShipSeeder extends Seeder
{
    public function run(): void
    {
        $ports = Port::pluck('id')->toArray();

        if (count($ports) < 2) {
            $this->command->error('Ports not found.');
            return;
        }

        $ships = [

            "Ever Given",
            "MSC Oscar",
            "Emma Maersk",
            "OOCL Hong Kong",
            "CMA CGM Jacques Saadé",
            "MSC Gülsün",
            "HMM Algeciras",
            "Madrid Maersk",
            "MSC Mina",
            "CMA CGM Marco Polo",
            "COSCO Shipping Universe",
            "MOL Triumph",
            "MSC Diana",
            "Ever Ace",
            "Ever Glory",
            "Ever Gentle",
            "Ever Loading",
            "Ever Smart",
            "Ever Lunar",
            "Ever Strong"

        ];

        $types = [

            "Container Ship",
            "Cargo Ship",
            "Bulk Carrier",
            "Oil Tanker"

        ];

        for ($i = 1; $i <= 200; $i++) {

            $route = \App\Models\ShippingRoute::inRandomOrder()->first();

            if (!$route) {
                $this->command->error('Shipping routes not found.');
                return;
            }

            Ship::updateOrCreate(
                ['imo_number' => 'IMO'.str_pad($i,7,'0',STR_PAD_LEFT)],
                [
                'current_port_id' => $route->origin_port_id,

                'destination_port_id' => $route->destination_port_id,

                'shipping_route_id' => $route->id,

                'ship_name' => $ships[array_rand($ships)].' '.$i,

                'ship_type' => $types[array_rand($types)],

                'capacity_teu' => rand(2000,24000),

                'cargo_percentage' => rand(40,100),

                'speed_knots' => rand(12,26),

                'eta_days' => rand(2,25),

                'status' => collect([
                    'Docked',
                    'Loading',
                    'Sailing',
                    'Delayed'
                ])->random(),

                'latitude' => $route->originPort ? ($route->originPort->latitude + (rand(-10,10)/1000)) : null,

                'longitude' => $route->originPort ? ($route->originPort->longitude + (rand(-10,10)/1000)) : null,

            ]);

        }

        $this->command->info('200 Ships Imported');
    }
}