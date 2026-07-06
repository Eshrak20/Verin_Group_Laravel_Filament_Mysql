<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('footer_settings', function (Blueprint $table) {

            // Rename page_key to company_key
            $table->renameColumn('page_key', 'company_key');

            // Ensure company names are unique
            $table->string('company_name')->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('footer_settings', function (Blueprint $table) {

            $table->renameColumn('company_key', 'page_key');

            $table->string('company_name')->nullable()->change();
        });
    }
};
