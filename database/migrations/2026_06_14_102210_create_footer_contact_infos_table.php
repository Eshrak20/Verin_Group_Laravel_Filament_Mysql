<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('footer_contact_infos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('footer_setting_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('phone')->nullable();

            $table->string('email')->nullable();

            $table->text('address')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('footer_contact_infos');
    }
};