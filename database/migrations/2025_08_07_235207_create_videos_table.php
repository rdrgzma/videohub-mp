<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('youtube_id');
            $table->string('thumbnail_url')->nullable();
            $table->string('duration'); // formato MM:SS ou HH:MM:SS
            $table->enum('level', ['iniciante', 'intermediario', 'avancado', 'livre']);
            $table->boolean('is_premium')->default(true);
            $table->boolean('is_published')->default(false);
            $table->integer('views_count')->default(0);
            $table->integer('sort_order')->default(0);
            $table->json('meta')->nullable(); // dados extras
            $table->timestamps();

            $table->index(['category_id', 'is_published']);
            $table->index(['is_premium', 'is_published']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};

