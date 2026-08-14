<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (! Schema::hasColumn('settings', 'custom_fields')) {
                $table->json('custom_fields')->nullable();
            }
            if (! Schema::hasColumn('settings', 'custom_values')) {
                $table->json('custom_values')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'custom_fields')) {
                $table->dropColumn('custom_fields');
            }
            if (Schema::hasColumn('settings', 'custom_values')) {
                $table->dropColumn('custom_values');
            }
        });
    }
};
