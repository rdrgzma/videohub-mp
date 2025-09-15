<?php
// ========================================

// database/migrations/2024_01_01_000007_create_comments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('video_id')->constrained()->onDelete('cascade');
            $table->text('content');
            $table->integer('video_timestamp')->default(0); // em segundos
            $table->boolean('is_approved')->default(true);
            $table->timestamps();

            $table->index(['video_id', 'is_approved']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};

