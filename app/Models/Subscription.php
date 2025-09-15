<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'amount_paid',
        'payment_method',
        'status',
        'starts_at',
        'expires_at',
        'cancelled_at',
        'payment_reference',
        'payment_data',
    ];

    protected function casts(): array
    {
        return [
            'amount_paid' => 'decimal:2',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'payment_data' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('expires_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->expires_at->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'R$ ' . number_format($this->amount_paid, 2, ',', '.');
    }

    public function getPaymentMethodTextAttribute(): string
    {
        return match($this->payment_method) {
            'credit_card' => 'Cartão de Crédito',
            'pix' => 'PIX',
            'boleto' => 'Boleto',
            default => ucfirst($this->payment_method)
        };
    }

    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pendente',
            'active' => 'Ativo',
            'cancelled' => 'Cancelado',
            'expired' => 'Expirado',
            default => ucfirst($this->status)
        };
    }

    public function activate(): void
    {
        $this->update([
            'status' => 'active',
            'starts_at' => now(),
        ]);

        $this->user->update([
            'current_plan_id' => $this->plan_id,
            'plan_started_at' => $this->starts_at,
            'plan_expires_at' => $this->expires_at,
        ]);
    }

    public function cancel(): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }
}
