<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('footer_settings', function (Blueprint $table) {
            $table->id();

            $table->string('page_key')->unique(); // home, about, services, etc.

            $table->string('logo')->nullable();

            $table->string('company_name')->nullable();

            $table->text('description')->nullable();

            $table->string('copyright_text')->nullable();

            $table->boolean('show_social_links')->default(true);

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('footer_settings');
    }
};