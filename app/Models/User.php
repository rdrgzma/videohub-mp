<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'current_plan_id',
        'plan_started_at',
        'plan_expires_at',
        'is_admin',
        'bio',
        'avatar',
        'last_activity_at',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Laravel 12 - Novo formato de casts
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'plan_started_at' => 'datetime',
            'plan_expires_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'is_admin' => 'boolean',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    // Relacionamentos
    public function currentPlan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'current_plan_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function videoViews(): HasMany
    {
        return $this->hasMany(VideoView::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSubscribed($query)
    {
        return $query->whereNotNull('current_plan_id')
            ->where('plan_expires_at', '>', now());
    }

    // Métodos de negócio
    public function hasActivePlan(): bool
    {
        return $this->current_plan_id &&
            $this->plan_expires_at &&
            $this->plan_expires_at->isFuture();
    }

    public function canAccessPremiumContent(): bool
    {
        return $this->is_admin || $this->hasActivePlan();
    }

    public function updateLastActivity(): void
    {
        $this->update(['last_activity_at' => now()]);
    }

    // Accessors
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=8B5CF6&color=fff';
    }

    public function getDaysRemainingAttribute(): int
    {
        if (!$this->plan_expires_at) {
            return 0;
        }
        return max(0, now()->diffInDays($this->plan_expires_at, false));
    }

    public function getSubscriptionStatusAttribute(): string
    {
        if ($this->is_admin) return 'admin';
        if (!$this->hasActivePlan()) return 'free';
        if ($this->plan_expires_at->diffInDays() <= 7) return 'expiring';
        return 'active';
    }

    public function formatWatchTime(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'min';
        }
        return $minutes . 'min';
    }

}
