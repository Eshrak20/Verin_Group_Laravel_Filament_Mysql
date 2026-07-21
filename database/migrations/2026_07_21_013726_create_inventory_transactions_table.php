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
        Schema::create('inventory_transactions', function (Blueprint $table) {

            $table->id();

            $table->foreignId('inventory_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('type', [

                'purchase',

                'sale',

                'reserve',

                'release',

                'refund',

                'adjustment',

                'transfer_in',

                'transfer_out',

                'damage',

            ]);

            $table->integer('quantity');

            $table->unsignedInteger('before_stock');

            $table->unsignedInteger('after_stock');

            $table->text('remarks')->nullable();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
