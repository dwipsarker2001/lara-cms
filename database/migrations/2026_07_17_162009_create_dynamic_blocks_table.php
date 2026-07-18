<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dynamic_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('label');
            $table->boolean('global')->default(false);
            $table->boolean('background')->default(true);
            $table->json('fields');
            $table->longText('template');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dynamic_blocks');
    }
};
