<?php

namespace App\Features\Post\Models;

use App\Models\User;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'category',
        'excerpt',
        'content',
        'cover_image_path',
        'isPublished',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'isPublished' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected $appends = [
        'is_published',
    ];

    protected static function newFactory(): PostFactory
    {
        return PostFactory::new();
    }

    public function getIsPublishedAttribute(): bool
    {
        return (bool) ($this->attributes['isPublished'] ?? false);
    }

    public function setIsPublishedAttribute($value): void
    {
        $this->attributes['isPublished'] = (bool) $value;
    }

    /**
     * Relasi ke Model User (Pemilik Artikel)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
