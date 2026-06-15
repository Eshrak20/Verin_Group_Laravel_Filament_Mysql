<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('footer_links', function (Blueprint $table) {
            $table->id();

            $table->foreignId('footer_setting_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('title'); // About Us, Privacy Policy

            $table->string('url');

            $table->boolean('open_new_tab')->default(false);

            $table->integer('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('footer_links');
    }
};