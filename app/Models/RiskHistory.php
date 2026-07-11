<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiskHistory extends Model
{
    protected $fillable = [
        'country_id',
        'risk_score',
        'record_date'
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}