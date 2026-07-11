<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TradeRecommendation extends Model
{
    protected $fillable = [

        'country_id',

        'trade_action',

        'priority',

        'recommendation',

        'business_reason',

        'generated_at'

    ];

    protected $casts=[

        'generated_at'=>'datetime'

    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    
}