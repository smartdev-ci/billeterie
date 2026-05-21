<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('customer_email');
            $table->text('qr_code'); // Base64 ou SVG string
            $table->string('qr_signature', 64);
            $table->enum('status', ['valid', 'used', 'cancelled'])->default('valid');
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->index('uuid', 'idx_ticket_uuid');
            $table->index('status', 'idx_ticket_status');
            $table->index(['event_id', 'status'], 'idx_ticket_event_status');
            $table->index('customer_email', 'idx_ticket_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};