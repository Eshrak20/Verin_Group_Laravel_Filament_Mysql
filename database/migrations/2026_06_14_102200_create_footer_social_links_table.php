<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('footer_social_links', function (Blueprint $table) {
            $table->id();

            $table->foreignId('footer_setting_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('platform'); // facebook, youtube, linkedin

            $table->string('url');

            $table->string('icon')->nullable();

            $table->integer('sort_order')->default(0);

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('footer_social_links');
    }
};