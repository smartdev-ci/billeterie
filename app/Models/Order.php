<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Ramsey\Uuid\Uuid;

class Order extends Model
{
    use HasFactory;

    /**
     * Statuts de commande.
     */
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_FAILED = 'failed';

    /**
     * Méthodes de paiement supportées.
     */
    const PAYMENT_METHOD_CINETPAY = 'cinetpay';
    const PAYMENT_METHOD_FEDAPAY = 'fedapay';
    const PAYMENT_METHOD_ORANGE_MONEY = 'orange_money';
    const PAYMENT_METHOD_MTN_MONEY = 'mtn_money';
    const PAYMENT_METHOD_MOOV_MONEY = 'moov_money';

    /**
     * Les attributs mass assignable.
     */
    protected $fillable = [
        'user_id',
        'event_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'quantity',
        'total_amount',
        'status',
        'payment_method',
        'transaction_id',
        'payment_data',
        'paid_at',
    ];

    /**
     * Attributs à caster.
     */
    protected $casts = [
        'quantity' => 'integer',
        'total_amount' => 'integer',
        'payment_data' => 'array',
        'paid_at' => 'datetime',
    ];

    /**
     * Relation avec l'utilisateur.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec l'événement.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Relation avec les tickets.
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Vérifier si la commande est confirmée.
     */
    public function isConfirmed(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    /**
     * Vérifier si la commande est en attente.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Vérifier si la commande est annulée.
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Vérifier si la commande a échoué.
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Marquer la commande comme confirmée.
     */
    public function markAsConfirmed(?string $transactionId = null): void
    {
        $this->update([
            'status' => self::STATUS_CONFIRMED,
            'transaction_id' => $transactionId ?? $this->transaction_id,
            'paid_at' => now(),
        ]);
    }

    /**
     * Marquer la commande comme annulée.
     */
    public function markAsCancelled(): void
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
        ]);
    }

    /**
     * Marquer la commande comme échouée.
     */
    public function markAsFailed(): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
        ]);
    }

    /**
     * Générer un identifiant unique pour la transaction.
     */
    public static function generateTransactionId(): string
    {
        return 'CMD-' . strtoupper(uniqid()) . '-' . rand(1000, 9999);
    }

    /**
     * Scope: Récupérer les commandes en attente.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope: Récupérer les commandes confirmées.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    /**
     * Scope: Récupérer les commandes d'un utilisateur.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Récupérer les commandes d'un événement.
     */
    public function scopeForEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }
}
