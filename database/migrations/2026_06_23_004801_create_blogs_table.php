<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();

            // Titles
            $table->string('title');
            $table->string('title_bng')->nullable();

            // SEO Slug
            $table->string('slug')->unique();

            // Content
            $table->longText('content')->nullable();
            $table->longText('summary')->nullable();
            $table->longText('excerpt')->nullable();

            $table->longText('content_bng')->nullable();
            $table->longText('summary_bng')->nullable();

            // Image
            $table->string('featured_image')->nullable();

            // Category
            $table->foreignId('category_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // Optional author (future-proof)
            $table->foreignId('author_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Status
            $table->enum('status', ['draft', 'published'])->default('draft');

            // SEO Fields
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            // Extra features
            $table->unsignedBigInteger('views')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('reading_time')->nullable(); // minutes

            // Publish time
            $table->timestamp('published_at')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};