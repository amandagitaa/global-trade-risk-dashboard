<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Article extends Model
{
    protected $fillable = [
        'title',
        'category',
        'content',
        'author',
        'status', // Draft, Published, Archived
        'user_id',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        // Automatically hide Draft and Archived articles from standard queries
        // Admin views will need to use Article::withoutGlobalScopes()
        static::addGlobalScope('published', function (Builder $builder) {
            $builder->where('status', 'Published');
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
