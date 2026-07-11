<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = [
        'country_code',
        'country_name',
        'capital',
        'region',
        'subregion',
        'currency_name',
        'currency_code',
        'language',
        'flag',
        'population',
        'latitude',
        'longitude'
    ];

    // ======================
    // Relations
    // ======================

    public function ports(): HasMany
    {
        return $this->hasMany(Port::class);
    }

    public function riskScores(): HasMany
    {
        return $this->hasMany(RiskScore::class);
    }

    public function latestRisk()
    {
        return $this->hasOne(RiskScore::class)->latestOfMany();
    }

    public function latestWeather()
    {
        return $this->hasOne(WeatherData::class)
                    ->latestOfMany();
    }

    public function latestCurrency()
    {
        return $this->hasOne(CurrencyRate::class)
                    ->latestOfMany();
    }

    public function latestNews()
    {
        return $this->hasOne(NewsCache::class)->latestOfMany();
    }
    
    public function recommendation()
    {
        return $this->hasOne(TradeRecommendation::class)
                    ->latestOfMany();
    }
}