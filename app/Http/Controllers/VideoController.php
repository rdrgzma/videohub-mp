<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\VideoView;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VideoController extends Controller
{
    // REMOVER o __construct() com middleware
    // No Laravel 12, middleware é definido nas rotas

    public function watch(Video $video)
    {
        // Verificar se usuário está logado (redundante, mas por segurança)
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Verificar se o vídeo está publicado
        if (!$video->is_published) {
            abort(404);
        }

        // Verificar acesso a conteúdo premium
        if ($video->is_premium && !Auth::user()->canAccessPremiumContent()) {
            return redirect()->route('home')->with('error', 'Este é um conteúdo premium. Assine um plano para ter acesso.');
        }

        // Incrementar visualizações
        $video->incrementViews();

        // Registrar ou atualizar visualização do usuário
        $videoView = VideoView::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'video_id' => $video->id,
            ],
            [
                'started_at' => now(),
                'last_watched_at' => now(),
            ]
        );

        // Log da atividade
        ActivityLog::log(
            'video_watch',
            "Assistindo vídeo: {$video->title}",
            Auth::user(),
            ['video_id' => $video->id, 'video_title' => $video->title]
        );

        // Carregar comentários aprovados do usuario


        $video->load(['comments' => function ($query) {
            $query->approved()
                ->with('user')
                ->where('user_id', Auth::id())
                ->orderBy('created_at', 'desc');
        }]);

        return view('videos.watch', compact('video', 'videoView'));
    }

    public function updateProgress(Request $request, Video $video)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'current_time' => 'required|integer|min:0',
            'duration' => 'required|integer|min:1',
        ]);

        $videoView = VideoView::where('user_id', Auth::id())
            ->where('video_id', $video->id)
            ->first();

        if ($videoView) {
            $videoView->updateProgress(
                $request->current_time,
                $request->duration
            );
        }

        return response()->json(['success' => true]);
    }

    public function addComment(Request $request, Video $video)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'content' => 'required|string|max:500',
            'video_timestamp' => 'integer|min:0',
        ]);

        $comment = $video->comments()->create([
            'user_id' => Auth::id(),
            'content' => $request->content,
            'video_timestamp' => $request->video_timestamp ?? 0,
            'is_approved' => true, // Auto-aprovação por enquanto
        ]);

        $comment->load('user');

        return response()->json([
            'success' => true,
            'comment' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'formatted_timestamp' => $comment->formatted_timestamp,
                'created_at' => $comment->created_at->format('d/m/Y H:i:s'),
                'user' => [
                    'name' => $comment->user->name,
                    'avatar_url' => $comment->user->avatar_url,
                ],
            ],
        ]);
    }

    public function deleteComment(Video $video, $commentId)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $comment = $video->comments()
            ->where('id', $commentId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$comment) {
            return response()->json(['success' => false, 'message' => 'Comentário não encontrado.'], 404);
        }

        $comment->delete();

        return response()->json(['success' => true]);
    }
}

