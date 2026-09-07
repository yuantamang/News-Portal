<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostViews extends Model
{
    protected $fillable = [
        "post_id",
        'ip_address',
        'viewed_at'
    ];

    /**
     * Get the post that owns the PostViews
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
