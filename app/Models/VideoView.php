<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoView extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'video_id',
        'watch_time',
        'total_duration',
        'completed',
        'started_at',
        'last_watched_at',
    ];

    protected function casts(): array
    {
        return [
            'watch_time' => 'integer',
            'total_duration' => 'integer',
            'completed' => 'boolean',
            'started_at' => 'datetime',
            'last_watched_at' => 'datetime',
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

    public function getProgressPercentageAttribute(): int
    {
        if ($this->total_duration === 0) {
            return 0;
        }
        return min(100, round(($this->watch_time / $this->total_duration) * 100));
    }

    public function getFormattedWatchTimeAttribute(): string
    {
        return $this->formatTime($this->watch_time);
    }

    public function getFormattedTotalDurationAttribute(): string
    {
        return $this->formatTime($this->total_duration);
    }

    private function formatTime(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $secs);
        }
        return sprintf('%d:%02d', $minutes, $secs);
    }

    public function updateProgress(int $currentTime, int $duration): void
    {
        $this->update([
            'watch_time' => max($this->watch_time, $currentTime),
            'total_duration' => $duration,
            'completed' => $currentTime >= ($duration * 0.95),
            'last_watched_at' => now(),
        ]);
    }
}
