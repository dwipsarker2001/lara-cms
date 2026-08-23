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
                if (! Schema::hasColumn('settings', 'ai_base_url')) {
                    $table->string('ai_base_url')->nullable()->after('cms_version');
                }
                if (! Schema::hasColumn('settings', 'ai_api_key')) {
                    $table->text('ai_api_key')->nullable()->after('ai_base_url');
                }
                if (! Schema::hasColumn('settings', 'ai_model')) {
                    $table->string('ai_model')->nullable()->after('ai_api_key');
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
                $columns = array_filter(['ai_base_url', 'ai_api_key', 'ai_model'], fn ($col) => Schema::hasColumn('settings', $col));
                if (! empty($columns)) {
                    $table->dropColumn($columns);
                }
            });
        }
    }
};
