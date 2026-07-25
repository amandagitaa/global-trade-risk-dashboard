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
        'trade_impact',
        'positive_score',
        'negative_score',
        'sentiment',
        'published_at',
        'status',
        'slug',
        'impact_score',
        'impact_level',
        'risk_direction',
        'impact_confidence',
        'affected_countries',
        'affected_sectors',
        'intelligence_summary',
        'mapped_countries',
        'mapped_ports',
        'regional_entities',
        'port_impact_type',
        'trade_exposure_type',
        'mapping_confidence',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'trade_impact' => 'array',
        'impact_score' => 'integer',
        'impact_confidence' => 'float',
        'affected_countries' => 'array',
        'affected_sectors' => 'array',
        'impact_factors' => 'array',
        'mapped_countries' => 'array',
        'mapped_ports' => 'array',
        'regional_entities' => 'array',
        'mapping_confidence' => 'decimal:2',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    protected static function booted(): void
    {
        static::addGlobalScope('published', function (Builder $builder) {
            $builder->where('status', 'Published');
        });

        static::saving(function ($news) {
            // Slug is generated only from the real title, no more fallback dummy logic
            if (empty($news->slug)) {
                $news->slug = \Illuminate\Support\Str::slug($news->title) . '-' . \Illuminate\Support\Str::random(8);
            }
        });
    }
}