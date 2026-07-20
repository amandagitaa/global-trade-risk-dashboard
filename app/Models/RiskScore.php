<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiskScore extends Model
{
    protected $fillable = [
        'country_id',
        'weather_score',
        'port_score',
        'currency_score',
        'economic_score',
        'news_score',
        'final_score',
        'risk_level',
        'reason'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}