<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('taxonomies') && ! Schema::hasColumn('taxonomies', 'position')) {
            Schema::table('taxonomies', function (Blueprint $table) {
                $table->integer('position')->default(0)->after('description');
            });
        }
    }

    public function down(): void
    {
        Schema::table('taxonomies', function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }
};
