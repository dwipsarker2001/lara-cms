<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('settings')) {
            Schema::table('settings', function (Blueprint $table) {
                if (! Schema::hasColumn('settings', 'cloudflare_r2_enabled')) {
                    $table->boolean('cloudflare_r2_enabled')->default(false);
                }
                if (! Schema::hasColumn('settings', 'cloudflare_r2_account_id')) {
                    $table->string('cloudflare_r2_account_id')->nullable();
                }
                if (! Schema::hasColumn('settings', 'cloudflare_r2_access_key_id')) {
                    $table->string('cloudflare_r2_access_key_id')->nullable();
                }
                if (! Schema::hasColumn('settings', 'cloudflare_r2_secret_access_key')) {
                    $table->text('cloudflare_r2_secret_access_key')->nullable();
                }
                if (! Schema::hasColumn('settings', 'cloudflare_r2_bucket')) {
                    $table->string('cloudflare_r2_bucket')->nullable();
                }
                if (! Schema::hasColumn('settings', 'cloudflare_r2_public_url')) {
                    $table->string('cloudflare_r2_public_url')->nullable();
                }
            });
        }

        if (Schema::hasTable('assets')) {
            Schema::table('assets', function (Blueprint $table) {
                if (! Schema::hasColumn('assets', 'disk')) {
                    $table->string('disk', 30)->default('public')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('settings')) {
            Schema::table('settings', function (Blueprint $table) {
                $columns = [
                    'cloudflare_r2_enabled',
                    'cloudflare_r2_account_id',
                    'cloudflare_r2_access_key_id',
                    'cloudflare_r2_secret_access_key',
                    'cloudflare_r2_bucket',
                    'cloudflare_r2_public_url',
                ];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('settings', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('assets')) {
            Schema::table('assets', function (Blueprint $table) {
                if (Schema::hasColumn('assets', 'disk')) {
                    $table->dropColumn('disk');
                }
            });
        }
    }
};
