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

    public function ports()
    {
        return $this->hasMany(
            Port::class,
            'country_iso2',
            'iso2'
        );
    }
    public function riskScores(): HasMany
    {
        return $this->hasMany(RiskScore::class);
    }

    public function latestRisk()
    {
        return $this->hasOne(RiskScore::class)->latestOfMany();
    }

    public function weatherData(): HasMany
    {
        return $this->hasMany(WeatherData::class);
    }

    public function currencyRates(): HasMany
    {
        return $this->hasMany(CurrencyRate::class);
    }

    public function newsCache(): HasMany
    {
        return $this->hasMany(NewsCache::class);
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

    public function economicData()
    {
        return $this->hasOne(
            EconomicData::class
        )->latestOfMany();
    }
}