<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('taxonomies') && ! Schema::hasColumn('taxonomies', 'fields')) {
            Schema::table('taxonomies', function (Blueprint $table) {
                $table->json('fields')->nullable()->after('description');
            });
        }

        if (Schema::hasTable('terms')) {
            Schema::table('terms', function (Blueprint $table) {
                if (! Schema::hasColumn('terms', 'data')) {
                    $table->json('data')->nullable()->after('slug');
                }
                if (! Schema::hasColumn('terms', 'description')) {
                    $table->text('description')->nullable()->after('data');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('taxonomies') && Schema::hasColumn('taxonomies', 'fields')) {
            Schema::table('taxonomies', function (Blueprint $table) {
                $table->dropColumn('fields');
            });
        }

        if (Schema::hasTable('terms')) {
            Schema::table('terms', function (Blueprint $table) {
                if (Schema::hasColumn('terms', 'data')) {
                    $table->dropColumn('data');
                }
                if (Schema::hasColumn('terms', 'description')) {
                    $table->dropColumn('description');
                }
            });
        }
    }
};
