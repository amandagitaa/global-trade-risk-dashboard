<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeatherData extends Model
{
    protected $table = 'weather_data';

    protected $fillable = [
        'country_id',
        'temperature',
        'rainfall',
        'wind_speed',
        'storm_risk',
        'weather_status',
        'recorded_at'
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function getWeatherIconAttribute()
    {
        $weather = strtolower($this->weather ?? '');

        if (str_contains($weather, 'clear')) {
            return '☀️';
        }

        if (str_contains($weather, 'sun')) {
            return '☀️';
        }

        if (str_contains($weather, 'cloud')) {
            return '☁️';
        }

        if (str_contains($weather, 'rain')) {
            return '🌧';
        }

        if (str_contains($weather, 'storm')) {
            return '⛈';
        }

        if (str_contains($weather, 'snow')) {
            return '❄️';
        }

        if (str_contains($weather, 'fog')) {
            return '🌫';
        }

        return '🌤';
    }
}