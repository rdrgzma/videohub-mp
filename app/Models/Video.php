<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'description',
        'youtube_id',
        'thumbnail_url',
        'duration',
        'level',
        'is_premium',
        'is_published',
        'views_count',
        'sort_order',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'is_premium' => 'boolean',
            'is_published' => 'boolean',
            'views_count' => 'integer',
            'sort_order' => 'integer',
            'meta' => 'array',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($video) {
            if (empty($video->slug)) {
                $video->slug = Str::slug($video->title);
            }

            if (empty($video->thumbnail_url) && !empty($video->youtube_id)) {
                $video->thumbnail_url = "https://img.youtube.com/vi/{$video->youtube_id}/maxresdefault.jpg";
            }
        });

        static::updating(function ($video) {
            if ($video->isDirty('title') && empty($video->getOriginal('slug'))) {
                $video->slug = Str::slug($video->title);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(VideoView::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function approvedComments(): HasMany
    {
        return $this->comments()->where('is_approved', true);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    public function scopeFree($query)
    {
        return $query->where('is_premium', false);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
    }

    public function getLevelTextAttribute(): string
    {
        return match($this->level) {
            'iniciante' => 'Iniciante',
            'intermediario' => 'Intermediário',
            'avancado' => 'Avançado',
            'livre' => 'Livre',
            default => ucfirst($this->level)
        };
    }

    public function getLevelColorAttribute(): string
    {
        return match($this->level) {
            'iniciante' => 'bg-green-500',
            'intermediario' => 'bg-yellow-500',
            'avancado' => 'bg-red-500',
            'livre' => 'bg-blue-500',
            default => 'bg-gray-500'
        };
    }

    public function getYoutubeUrlAttribute(): string
    {
        return "https://youtu.be/{$this->youtube_id}";
    }

    public function getWatchUrlAttribute(): string
    {
        return route('videos.watch', $this->slug);
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }
}

