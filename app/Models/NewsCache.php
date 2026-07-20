<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class NewsCache extends Model
{
    protected $table = 'news_cache';

    protected $fillable = [

        'country_id',

        'country_name',

        'title',

        'description',

        'content',

        'url',

        'original_url',

        'image_url',

        'author',

        'source',

        'category',

        'positive_score',

        'negative_score',

        'sentiment',

        'published_at',
        
        'status',
    ];

    protected $casts = [

        'published_at' => 'datetime',

    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    protected static function booted(): void
    {
        static::addGlobalScope('published', function (Builder $builder) {
            $builder->where('status', 'Published');
        });
    }
}