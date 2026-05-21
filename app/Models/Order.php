<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory; 

    public const TICKET_PRICE = 5000;

    protected $fillable = [
        'uuid', 'user_id', 'customer_name', 'customer_email', 'customer_phone',
        'quantity', 'total_amount', 'payment_status', 'payment_reference',
        'mobile_provider', 'paid_at'
    ];

    protected static function booted(): void
    {
        static::creating(fn (Order $order) => $order->uuid ??= (string) Str::uuid());
    }

    protected function casts(): array
    {
        return [
            'total_amount' => 'integer',
            'quantity' => 'integer',
            'paid_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public static function calculateTotal(int $quantity): int
    {
        return $quantity * self::TICKET_PRICE;
    }

    public function markAsPaid(?string $reference = null, ?string $provider = null): void
    {
        $this->update([
            'payment_status' => 'completed',
            'payment_reference' => $reference,
            'mobile_provider' => $provider,
            'paid_at' => now(),
        ]);
    }
}