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

            // User (nullable for guest checkout)
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Snapshot of shipping address
            $table->foreignId('shipping_address_id')->nullable()->constrained()->nullOnDelete();

            // Unique Order Number
            $table->string('order_number')->unique();

            // Pricing
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('shipping_charge', 12, 2)->default(0);
            $table->decimal('cod_charge', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);

            $table->decimal('total_amount', 12, 2);

            // Coupon
            $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();

            // Status
            $table->enum('payment_status', [
                'pending',
                'paid',
                'failed',
                'refunded'
            ])->default('pending');

            $table->enum('order_status', [
                'pending',
                'confirmed',
                'processing',
                'packed',
                'shipped',
                'delivered',
                'cancelled',
                'returned'
            ])->default('pending');

            $table->enum('shipment_status', [
                'pending',
                'processing',
                'picked',
                'in_transit',
                'delivered',
                'returned',
                'cancelled'
            ])->default('pending');

            $table->enum('fraud_status', [
                'pending',
                'safe',
                'suspicious',
                'blocked'
            ])->default('pending');

            $table->enum('payment_method', [
                'cod',
                'online'
            ]);

            $table->text('notes')->nullable();

            $table->timestamp('ordered_at')->nullable();

            $table->timestamps();

            $table->index('order_number');
            $table->index('payment_status');
            $table->index('order_status');
            $table->index('shipment_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};