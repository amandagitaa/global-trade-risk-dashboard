<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsCache extends Model
{
    protected $table = 'news_cache';

    protected $fillable = [
        'country_id',
        'title',
        'description',
        'source',
        'url',
        'category',
        'positive_score',
        'negative_score',
        'sentiment',
        'published_at'
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}