<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_title')->default('Lara CMS');
            $table->string('theme_color')->default('#4f46e5');
            $table->string('currency', 10)->default('USD');
            $table->string('logo_light')->nullable();
            $table->string('logo_dark')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('admin_theme')->default('dark');
            $table->string('cms_version')->default('1.0.0');
            $table->json('seo')->nullable();
            $table->json('payment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
