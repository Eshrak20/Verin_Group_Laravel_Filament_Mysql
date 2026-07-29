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
        Schema::create('shipments', function (Blueprint $table) {

            $table->id();

            $table->foreignId('order_id')->constrained()->cascadeOnDelete();

            $table->string('courier_name')->default('Steadfast');

            $table->string('tracking_code')->nullable();

            $table->string('consignment_id')->nullable();

            $table->decimal('delivery_charge', 12, 2)->default(0);

            $table->decimal('cod_amount', 12, 2)->default(0);

            $table->enum('status', [
                'pending',
                'processing',
                'picked',
                'in_transit',
                'delivered',
                'returned',
                'cancelled'
            ])->default('pending');

            $table->json('response')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
