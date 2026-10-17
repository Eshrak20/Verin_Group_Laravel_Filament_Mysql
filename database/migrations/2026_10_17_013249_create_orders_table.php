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

            $table->string('order_number')->unique();

            $table->string('customer_name');
            $table->string('phone', 20);
            $table->string('email')->nullable();

            $table->text('delivery_address');
            $table->string('district')->nullable();
            $table->string('area')->nullable();
            $table->text('customer_note')->nullable();

            $table->string('payment_method', 20);
            $table->string('payment_provider', 30)->nullable();
            $table->string('transaction_id')->nullable();

            $table->string('payment_status', 30)->default('unpaid');
            $table->string('order_status', 30)->default('pending');

            $table->decimal('subtotal', 12, 2);
            $table->decimal('delivery_charge', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);

            $table->timestamps();

            $table->index(['order_status', 'created_at']);
            $table->index(['payment_status', 'created_at']);
            $table->index('phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};