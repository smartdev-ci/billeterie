<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory; 

    protected $fillable = ['name', 'description', 'event_date', 'location', 'max_tickets', 'tickets_sold', 'status'];

    protected function casts(): array
    {
        return [
            'event_date' => 'datetime',
            'max_tickets' => 'integer',
            'tickets_sold' => 'integer',
        ];
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public static function current(): ?self
    {
        return self::active()->first();
    }

    public function isSoldOut(): bool
    {
        return $this->tickets_sold >= $this->max_tickets;
    }

    public function availableTickets(): int
    {
        return max(0, $this->max_tickets - $this->tickets_sold);
    }
}