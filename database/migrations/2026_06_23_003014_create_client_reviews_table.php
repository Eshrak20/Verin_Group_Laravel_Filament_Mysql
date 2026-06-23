<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('client_reviews', function (Blueprint $table) {
            $table->id();

            $table->string('client_name');
            $table->string('client_position')->nullable(); // e.g. CEO, Developer
            $table->string('client_image')->nullable(); // avatar

            $table->tinyInteger('rating')->default(5); // 1-5 stars
            $table->text('review');
            $table->string('item');

            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_reviews');
    }
};