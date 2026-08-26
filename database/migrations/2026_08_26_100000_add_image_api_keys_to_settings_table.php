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
        if (Schema::hasTable('settings')) {
            Schema::table('settings', function (Blueprint $table) {
                if (! Schema::hasColumn('settings', 'unsplash_access_key')) {
                    $table->text('unsplash_access_key')->nullable()->after('ai_model');
                }
                if (! Schema::hasColumn('settings', 'pexels_api_key')) {
                    $table->text('pexels_api_key')->nullable()->after('unsplash_access_key');
                }
                if (! Schema::hasColumn('settings', 'pixabay_api_key')) {
                    $table->text('pixabay_api_key')->nullable()->after('pexels_api_key');
                }
                if (! Schema::hasColumn('settings', 'image_provider')) {
                    $table->string('image_provider', 50)->default('auto')->nullable()->after('pixabay_api_key');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('settings')) {
            Schema::table('settings', function (Blueprint $table) {
                $columns = array_filter(['unsplash_access_key', 'pexels_api_key', 'pixabay_api_key', 'image_provider'], fn ($col) => Schema::hasColumn('settings', $col));
                if (! empty($columns)) {
                    $table->dropColumn($columns);
                }
            });
        }
    }
};
