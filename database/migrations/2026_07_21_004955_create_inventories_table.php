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
        Schema::create('inventories', function (Blueprint $table) {

            $table->id();

            $table->foreignId('branch_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('product_variant_id')
                ->constrained()
                ->cascadeOnDelete();

            // Physical stock in branch
            $table->unsignedInteger('stock')->default(0);

            // Reserved by pending orders
            $table->unsignedInteger('reserved_stock')->default(0);

            // Alert when available stock reaches this value
            $table->unsignedInteger('low_stock_alert')->default(5);

            $table->timestamps();

            // One inventory record per branch per variant
            $table->unique([
                'branch_id',
                'product_variant_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};