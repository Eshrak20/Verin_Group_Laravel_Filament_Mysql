<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('site_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('page_type');

            $table->string('title');

            $table->text('short_description')->nullable();

            $table->longText('content')->nullable();

            $table->boolean('is_published')->default(true);

            $table->timestamp('published_at')->nullable();

            $table->timestamps();

            $table->softDeletes();

            $table->unique(['site_id', 'page_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
