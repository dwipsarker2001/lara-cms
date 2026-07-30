<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('collection_entries')) {
            Schema::table('collection_entries', function (Blueprint $table) {
                if (Schema::hasColumn('collection_entries', 'page_id')) {
                    $table->dropColumn('page_id');
                }
                if (! Schema::hasColumn('collection_entries', 'slug')) {
                    $table->string('slug')->nullable()->index();
                }
                if (! Schema::hasColumn('collection_entries', 'published')) {
                    $table->boolean('published')->default(true);
                }
                if (! Schema::hasColumn('collection_entries', 'meta')) {
                    $table->json('meta')->nullable();
                }
            });
        }

        Schema::dropIfExists('pages');
    }

    public function down(): void
    {
        // Re-create pages table if rolled back
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->json('sections')->nullable();
            $table->json('meta')->nullable();
            $table->boolean('published')->default(true);
            $table->integer('position')->default(0);
            $table->timestamps();
        });

        Schema::table('collection_entries', function (Blueprint $table) {
            $table->foreignId('page_id')->nullable()->constrained('pages')->nullOnDelete();
            $table->dropColumn(['slug', 'published', 'meta']);
        });
    }
};
