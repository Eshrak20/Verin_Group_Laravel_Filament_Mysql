<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variant_videos', function (Blueprint $table) {

            $table->string('video_provider')
                ->default('cloudinary')
                ->after('product_variant_id');

            // You don't really need this anymore
            $table->dropColumn('video');
        });
    }

    public function down(): void
    {
        Schema::table('product_variant_videos', function (Blueprint $table) {

            $table->dropColumn('video_provider');

            $table->string('video')->nullable();
        });
    }
};