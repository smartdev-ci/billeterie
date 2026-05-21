<?php

use App\Models\User;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\AdminAuditLog;
use App\Services\QR\QRCodeService;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $this->event = Event::factory()->create();
    $this->qrService = app(QRCodeService::class);
});

/** @test */
it('validates a ticket successfully on first scan', function () {
    $uuid = (string) Str::uuid();
    $ticket = Ticket::factory()->create([
        'uuid'         => $uuid,
        'event_id'     => $this->event->id,
        'qr_signature' => $this->qrService->sign($uuid),
        'status'       => 'valid',
    ]);

    $response = $this->actingAs($this->admin)
    ->postJson('/staff/qr/validate', [  // ← Vérifie ce chemin
        'ticket_uuid' => $uuid,
        'signature'   => $ticket->qr_signature,
    ]);

    $response->assertStatus(200)
        ->assertJson(['status' => 'success']);

    expect($ticket->refresh()->status)->toBe('used')
        ->and($ticket->refresh()->validated_by)->toBe($this->admin->id)
        ->and(AdminAuditLog::where('action', 'ticket.validated')->count())->toBe(1);
});

/** @test */
it('rejects a ticket that has already been used', function () {
    $uuid = (string) Str::uuid();
    $ticket = Ticket::factory()->create([
        'uuid'         => $uuid,
        'event_id'     => $this->event->id,
        'qr_signature' => $this->qrService->sign($uuid),
        'status'       => 'used',
    ]);

    $response = $this->actingAs($this->admin)
    ->postJson('/staff/qr/validate', [  // ← Vérifie ce chemin
        'ticket_uuid' => $uuid,
        'signature'   => $ticket->qr_signature,
    ]);

    $response->assertStatus(400)
        ->assertJsonPath('message', fn (string $msg) => str_contains($msg, 'déjà'));

    expect($ticket->refresh()->status)->toBe('used');
});

/** @test */
it('rejects a ticket with an invalid signature', function () {
    $uuid = (string) Str::uuid();
    $ticket = Ticket::factory()->create([
        'uuid'         => $uuid,
        'event_id'     => $this->event->id,
        'qr_signature' => $this->qrService->sign($uuid),
        'status'       => 'valid',
    ]);

    $response = $this->actingAs($this->admin)
        ->postJson('/staff/qr/validate', [
            'ticket_uuid' => $uuid,
            'signature'   => 'invalid_fake_signature',
        ]);

    $response->assertStatus(403)
        ->assertJson(['message' => 'Signature invalide.']);

    expect(AdminAuditLog::where('action', 'ticket.signature_failed')->count())->toBe(1)
        ->and($ticket->refresh()->status)->toBe('valid');
});

/** @test */
it('enforces rate limiting after 15 rapid attempts', function () {
    // Nettoyage du cache rate limiter pour garantir un état propre
    RateLimiter::clear('qr_scan:127.0.0.1');

    $uuid = (string) Str::uuid();
    Ticket::factory()->create([
        'uuid'         => $uuid,
        'event_id'     => $this->event->id,
        'qr_signature' => $this->qrService->sign($uuid),
        'status'       => 'valid',
    ]);

    // Envoi de 15 requêtes (seuil autorisé)
    for ($i = 0; $i < 15; $i++) {
        $this->actingAs($this->admin)
            ->postJson('/staff/qr/validate', [
                'ticket_uuid' => $uuid,
                'signature'   => $this->qrService->sign($uuid),
            ]);
    }

    // La 16e doit être bloquée
    $response = $this->actingAs($this->admin)
        ->postJson('/staff/qr/validate', [
            'ticket_uuid' => $uuid,
            'signature'   => $this->qrService->sign($uuid),
        ]);

    $response->assertStatus(429)
        ->assertJson(['message' => 'Trop de tentatives. Patientez 60s.']);
});