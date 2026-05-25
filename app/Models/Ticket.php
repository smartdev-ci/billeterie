<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Ticket extends Model
{
    use HasFactory;

    /**
     * Statuts de ticket.
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_USED = 'used';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Les attributs mass assignable.
     */
    protected $fillable = [
        'order_id',
        'event_id',
        'user_id',
        'ticket_token',
        'ticket_number',
        'qr_image_path',
        'status',
        'used_at',
        'scanned_by',
    ];

    /**
     * Attributs à caster.
     */
    protected $casts = [
        'used_at' => 'datetime',
    ];

    /**
     * Boot du modèle - génération automatique du token.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->ticket_token)) {
                $ticket->ticket_token = Str::uuid()->toString();
            }
        });
    }

    /**
     * Relation avec la commande.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relation avec l'événement.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Relation avec l'utilisateur (propriétaire).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec le scanner (utilisateur qui a scanné).
     */
    public function scanner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }

    /**
     * Vérifier si le ticket est actif.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Vérifier si le ticket a été utilisé.
     */
    public function isUsed(): bool
    {
        return $this->status === self::STATUS_USED;
    }

    /**
     * Vérifier si le ticket est annulé.
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Générer un numéro de ticket unique (ex: LPP-ABC123-001).
     */
    public static function generateTicketNumber(string $prefix = 'LPP'): string
    {
        $random = strtoupper(Str::random(6));
        $lastTicket = self::where('ticket_number', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastTicket ? intval(substr($lastTicket->ticket_number, -3)) + 1 : 1;

        return sprintf('%s-%s-%03d', $prefix, $random, $sequence);
    }

    /**
     * Générer l'URL du QR code via API externe.
     */
    public function generateQrCodeUrl(): string
    {
        return sprintf(
            'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=%s',
            urlencode($this->ticket_token)
        );
    }

    /**
     * Marquer le ticket comme utilisé.
     */
    public function markAsUsed(?User $scanner = null): void
    {
        $this->update([
            'status' => self::STATUS_USED,
            'used_at' => now(),
            'scanned_by' => $scanner?->id,
        ]);
    }

    /**
     * Obtenir l'URL complète du QR code.
     */
    public function getQrCodeUrlAttribute(): ?string
    {
        return $this->qr_image_path;
    }
}
