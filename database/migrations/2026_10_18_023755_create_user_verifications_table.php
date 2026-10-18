<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('email');
            $table->string('otp');
            $table->string('purpose', 50)->default('email_verification');
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamp('expired_at');
            $table->timestamps();

            $table->index(['email', 'purpose']);
            $table->index('expired_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_verifications');
    }
};
