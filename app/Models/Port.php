<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Port extends Model
{
    protected $fillable = [

        'country_iso2',
        'country_name',

        'name',
        'code',
        'city',
        'region',

        'latitude',
        'longitude',

        'port_type',
        'status',

        'annual_capacity',
        'teu_capacity',
        'trade_volume',

        'importance_score',

        'risk_score',
        'risk_level',

        'weather_risk',
        'political_risk',
        'logistic_risk',

        'main_industries',
        'description',

        'traffic_level',
        'shipping_routes',
        'ai_recommendation'

    ];

    public function country()
    {
        return $this->belongsTo(
            Country::class,
            'country_iso2',
            'iso2'
        );
    }

    public function ships()
    {
        return $this->hasMany(
            Ship::class,
            'current_port_id'
        );
    }

    public function routes()
    {
        return $this->hasMany(
            ShippingRoute::class,
            'origin_port_id'
        );
    }
}