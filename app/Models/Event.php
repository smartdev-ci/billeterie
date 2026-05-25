<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    /**
     * Les attributs mass assignable.
     */
    protected $fillable = [
        'title',
        'description',
        'location',
        'start_date',
        'end_date',
        'capacity',
        'tickets_sold',
        'ticket_price',
        'is_active',
        'hero_image',
        'faq',
        'gallery',
    ];

    /**
     * Attributs à caster.
     */
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'faq' => 'array',
        'gallery' => 'array',
        'ticket_price' => 'decimal:2',
    ];

    /**
     * Prix par défaut du billet (5000 FCFA).
     */
    const DEFAULT_TICKET_PRICE = 5000;

    /**
     * Relation avec les commandes.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Relation avec les tickets.
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Vérifier si l'événement est complet.
     */
    public function isSoldOut(): bool
    {
        return $this->tickets_sold >= $this->capacity;
    }

    /**
     * Obtenir le nombre de places disponibles.
     */
    public function availableTickets(): int
    {
        return max(0, $this->capacity - $this->tickets_sold);
    }

    /**
     * Incrémenter le nombre de billets vendus.
     */
    public function incrementTicketsSold(int $quantity = 1): void
    {
        $this->increment('tickets_sold', $quantity);
    }

    /**
     * Décrémenter le nombre de billets vendus.
     */
    public function decrementTicketsSold(int $quantity = 1): void
    {
        $this->decrement('tickets_sold', $quantity);
    }
}
