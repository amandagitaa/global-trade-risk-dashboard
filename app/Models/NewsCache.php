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
        'slug',
    ];

    protected $casts = [

        'published_at' => 'datetime',

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