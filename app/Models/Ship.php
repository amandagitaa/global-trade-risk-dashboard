<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ship extends Model
{
    protected $fillable = [

        'current_port_id',

        'destination_port_id',

        'shipping_route_id',

        'imo_number',

        'ship_name',

        'ship_type',

        'capacity_teu',

        'cargo_percentage',

        'speed_knots',

        'eta_days',

        'status',

        'latitude',

        'longitude'

    ];

    public function currentPort()
    {
        return $this->belongsTo(
            Port::class,
            'current_port_id'
        );
    }

    public function destinationPort()
    {
        return $this->belongsTo(
            Port::class,
            'destination_port_id'
        );
    }

    public function shippingRoute()
    {
        return $this->belongsTo(
            ShippingRoute::class,
            'shipping_route_id'
        );
    }
}