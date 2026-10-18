<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('delivery_zone')
                ->nullable()
                ->after('area');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('regular_unit_price', 12, 2)
                ->nullable()
                ->after('unit_price');

            $table->decimal('discount_amount', 12, 2)
                ->default(0)
                ->after('regular_unit_price');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn([
                'regular_unit_price',
                'discount_amount',
            ]);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('delivery_zone');
        });
    }
};