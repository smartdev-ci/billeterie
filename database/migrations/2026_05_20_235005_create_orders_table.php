<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('total_amount');
            $table->enum('payment_status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->string('payment_reference')->nullable();
            $table->string('mobile_provider')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('uuid', 'idx_order_uuid');
            $table->index(['payment_status', 'created_at'], 'idx_order_status_created');
            $table->index('customer_email', 'idx_order_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};