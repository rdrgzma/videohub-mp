<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'video_id',
        'content',
        'video_timestamp',
        'is_approved',
    ];

    protected function casts(): array
    {
        return [
            'video_timestamp' => 'integer',
            'is_approved' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function getFormattedTimestampAttribute(): string
    {
        $minutes = floor($this->video_timestamp / 60);
        $seconds = $this->video_timestamp % 60;
        return sprintf('%d:%02d', $minutes, $seconds);
    }

    public function approve(): void
    {
        $this->update(['is_approved' => true]);
    }

    public function reject(): void
    {
        $this->update(['is_approved' => false]);
    }
}
