<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CurrencyRate extends Model
{
    protected $table = 'currency_rates';

    protected $fillable = [
        'country_id',
        'base_currency',
        'target_currency',
        'exchange_rate',
        'change_percentage',
        'recorded_at',
    ];

    protected $casts = [
        'exchange_rate'     => 'float',
        'change_percentage' => 'float',
        'recorded_at'       => 'datetime',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}