<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingRoute extends Model
{
    protected $fillable=[

        'origin_port_id',

        'destination_port_id',

        'distance_km',

        'estimated_days',

        'shipping_cost',

        'risk_level',

        'weather_risk',

        'piracy_risk',

        'status',

        'ai_recommendation'

    ];

    public function originPort()
    {
        return $this->belongsTo(
            Port::class,
            'origin_port_id'
        );
    }

    public function destinationPort()
    {
        return $this->belongsTo(
            Port::class,
            'destination_port_id'
        );
    }

}