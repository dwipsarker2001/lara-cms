<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->decimal('price', 12, 2)->default(0)->after('name');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->string('currency', 10)->default('USD')->after('theme_color');
        });
    }

    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->dropColumn('price');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('currency');
        });
    }
};
