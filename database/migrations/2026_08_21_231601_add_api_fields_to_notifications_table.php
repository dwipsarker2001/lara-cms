<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->string('type')->default('info')->after('tone');
            $table->string('url')->nullable()->after('type');
            $table->string('action_label')->nullable()->after('url');
            $table->timestamp('read_at')->nullable()->after('action_label');
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['type', 'url', 'action_label', 'read_at']);
        });
    }
};
