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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('max_emails')->default(0);
            $table->integer('max_contacts')->default(0);
            $table->integer('max_campaigns')->default(0);
            $table->integer('max_groups')->default(0);
            $table->boolean('active_on_register')->default(false);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->integer('max_emails')->nullable();
            $table->integer('max_contacts')->nullable();
            $table->integer('max_campaigns')->nullable();
            $table->integer('max_groups')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['max_emails', 'max_contacts', 'max_campaigns', 'max_groups']);
        });
    }
};
