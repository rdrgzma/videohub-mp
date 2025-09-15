<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('video_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('video_id')->constrained()->onDelete('cascade');
            $table->integer('watch_time')->default(0); // em segundos
            $table->integer('total_duration')->default(0); // duração total do vídeo
            $table->boolean('completed')->default(false);
            $table->timestamp('started_at');
            $table->timestamp('last_watched_at');
            $table->timestamps();

            $table->unique(['user_id', 'video_id']);
            $table->index(['user_id', 'completed']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_views');
    }
};

