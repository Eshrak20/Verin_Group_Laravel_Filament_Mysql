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
        Schema::create('fraud_checks', function (Blueprint $table) {

            $table->id();

            $table->foreignId('order_id')->constrained()->cascadeOnDelete();

            $table->string('phone');

            $table->integer('fraud_score')->default(0);

            $table->string('fraud_level');

            $table->integer('total_previous_orders')->default(0);

            $table->integer('total_cancelled_orders')->default(0);

            $table->integer('total_delivered_orders')->default(0);

            $table->json('response')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fraud_checks');
    }
};
