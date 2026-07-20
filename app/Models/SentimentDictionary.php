<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SentimentDictionary extends Model
{
    protected $fillable = [
        'word',
        'type'
    ];
}
