<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_charges', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->decimal('inside_dhaka_charge', 10, 2);

            $table->decimal('outside_dhaka_charge', 10, 2);

            $table->text('description')->nullable();

            $table->boolean('status')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_charges');
    }
};