<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Ticket extends Model
{
    use HasFactory; 

    protected $fillable = [
        'uuid', 'order_id', 'user_id', 'event_id', 'customer_email', 'customer_name',
        'qr_code', 'qr_signature', 'status', 'validated_by', 'used_at'
    ];

    protected static function booted(): void
    {
        static::creating(fn (Ticket $ticket) => $ticket->uuid ??= (string) Str::uuid());
    }

    protected function casts(): array
    {
        return ['used_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function scopeValid($query)
    {
        return $query->where('status', 'valid');
    }

    public function isUsable(): bool
    {
        return $this->status === 'valid';
    }
}