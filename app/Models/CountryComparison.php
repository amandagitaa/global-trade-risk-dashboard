<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountryComparison extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'country_a_id',
        'country_b_id',
        'risk_score_a',
        'risk_score_b',
        'recommended_country',
        'recommendation',
        'comparison_result',
    ];

    protected $casts = [
        'comparison_result' => 'array',
        'risk_score_a' => 'float',
        'risk_score_b' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function countryA()
    {
        return $this->belongsTo(Country::class, 'country_a_id');
    }

    public function countryB()
    {
        return $this->belongsTo(Country::class, 'country_b_id');
    }
}
