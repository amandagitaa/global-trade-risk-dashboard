<?php

namespace App\Http\Controllers;

use App\Models\Port;

class PortController extends Controller
{
    public function index()
    {
        $ports = Port::with('country')
            ->orderByDesc('importance_score')
            ->get();

        return view('ports.index', compact('ports'));
    }

    public function show(Port $port)
    {

        $port->load([

            'country',

            'ships.destinationPort',

            'routes.destinationPort'

        ]);

        $nearbyPorts = Port::with('country')
    ->where('country_iso2', $port->country_iso2)
    ->where('id', '!=', $port->id)
    ->take(5)
    ->get();

        return view(
            'ports.show',
            compact(
                'port',
                'nearbyPorts'
            )
        );

    }
}