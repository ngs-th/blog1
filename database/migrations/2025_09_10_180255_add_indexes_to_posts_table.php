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
        Schema::table('posts', function (Blueprint $table) {
            // Index for filtering by user
            $table->index('user_id');

            // Index for published posts queries
            $table->index('published_at');

            // Index for title searches
            $table->index('title');

            // Composite index for published posts by user
            $table->index(['user_id', 'published_at']);

            // Composite index for published posts ordering
            $table->index(['published_at', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Drop indexes in reverse order
            $table->dropIndex(['published_at', 'created_at']);
            $table->dropIndex(['user_id', 'published_at']);
            $table->dropIndex(['title']);
            $table->dropIndex(['published_at']);
            $table->dropIndex(['user_id']);
        });
    }
};
