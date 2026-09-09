<?php

namespace App\Models;

use App\Enums\AdvertisementPosition;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;
use Override;

class Advertisement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'image',

        'link',
        'position',
        'status',

        'start_at',
        'end_at',

        'view_count',
        'click_count',
    ];

    protected $casts = [
        'position' => AdvertisementPosition::class,
        'start_at' => 'date',
        'end_at' => 'date',
        'view_count' => 'integer',
        'click_count' => 'integer',
    ];

    #[Override]
    protected static function booted(): void
    {
        static::updating(function (Advertisement $model): void {
            if ($model->isDirty('image')) {
                Storage::disk('public')->delete($model->getOriginal('image'));
            }
        });

        static::deleting(function (Advertisement $model): void {
            $model->media()->each(function (Media $media): void {
                $media->delete();
            });

            Storage::disk('public')->delete($model->image);
        });
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }
}
