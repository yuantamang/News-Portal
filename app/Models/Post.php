<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Override;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'context',

        'image',
        'status',
        'published_at',

        'view_count',
        'click_count',
        'is_featured',

        'is_breaking',
        'is_trending',
    ];

    protected $casts = [
        'published_at' => 'date',
        'view_count' => 'integer',
        'click_count' => 'integer',
        'is_featured' => 'boolean',
        'is_breaking' => 'boolean',
        'is_trending' => 'boolean',
    ];

    #[Override]
    protected static function booted(): void
    {
        static::updating(function (Post $model): void {
            if ($model->isDirty('image')) {
                Storage::disk('public')->delete($model->getOriginal('image'));
            }
        });

        static::deleting(function (Post $model): void {
            $model->media()->each(function (Media $media): void {
                $media->delete();
            });

            Storage::disk('public')->delete($model->image);
        });
    }

    /**
     * The tags that belong to the Post
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'posts_tags', 'post_id', 'tag_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_posts', 'post_id', 'category_id');
    }

    public function postViews(): HasMany
    {
        return $this->hasMany(PostViews::class);
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    /**
     * Scope a query to only include posts eligible for public display.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }
}
