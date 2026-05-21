<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->dateTime('event_date');
            $table->string('location');
            $table->unsignedInteger('max_tickets');
            $table->unsignedInteger('tickets_sold')->default(0);
            $table->enum('status', ['draft', 'active', 'sold_out', 'cancelled'])->default('active');
            $table->timestamps();

            $table->index(['status', 'event_date'], 'idx_event_status_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};