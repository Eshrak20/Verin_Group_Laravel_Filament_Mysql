<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {

            $table->id();

            $table->foreignId('order_id')->constrained()->cascadeOnDelete();

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('transaction_id')->nullable();

            $table->string('gateway_transaction_id')->nullable();

            $table->string('gateway_name')->nullable();

            $table->string('payment_method');

            $table->decimal('amount', 12, 2);

            $table->string('currency')->default('BDT');

            $table->string('bank_transaction_id')->nullable();

            $table->string('card_type')->nullable();

            $table->string('card_brand')->nullable();

            $table->string('card_issuer')->nullable();

            $table->enum('payment_status', [
                'pending',
                'paid',
                'failed',
                'cancelled',
                'refunded'
            ])->default('pending');

            $table->string('risk_level')->nullable();

            $table->string('risk_title')->nullable();

            $table->timestamp('paid_at')->nullable();

            $table->json('raw_response')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
