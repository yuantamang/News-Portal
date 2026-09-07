<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;
use Override;

class Media extends Model
{
    protected $casts = [
        'file_path' => 'array'
    ];

    protected $fillable = [
        'file_path',
        'video_url',
        'type',
        'caption',
    ];

    #[Override]
    protected static function booted(): void
    {
        static::saving(function (Media $media): void {
            if ($media->isVideo()) {
                $media->file_path = null;

                return;
            }

            $media->video_url = null;
        });

        static::updating(function (Media $media): void {
            if (! $media->isDirty('file_path')) {
                return;
            }

            $originalPath = $media->getOriginal('file_path');

            if (blank($originalPath)) {
                return;
            }

            Storage::disk('public')->delete($originalPath);
        });

        static::deleting(function (Media $media): void {
            if (blank($media->file_path)) {
                return;
            }

            Storage::disk('public')->delete($media->file_path);
        });
    }

    public function isImage(): bool
    {
        return $this->type === 'image';
    }

    public function isFile(): bool
    {
        return $this->type === 'file';
    }

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }

    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }
}
