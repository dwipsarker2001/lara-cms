<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('taxonomies') && ! Schema::hasColumn('taxonomies', 'icon')) {
            Schema::table('taxonomies', function (Blueprint $table) {
                $table->string('icon')->nullable()->after('slug');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('taxonomies') && Schema::hasColumn('taxonomies', 'icon')) {
            Schema::table('taxonomies', function (Blueprint $table) {
                $table->dropColumn('icon');
            });
        }
    }
};
