@extends('layouts.app')

@section('title', $video->title . ' - VideoHub')

@section('content')
    <div class="bg-gray-50 min-h-screen">
        <div class="container mx-auto px-4 py-8 max-w-6xl">
            <!-- Cabeçalho -->
            <div class="bg-white rounded-lg shadow-md mb-6 p-6">
                <div class="flex items-center justify-between mb-4">
                    <a href="{{ route('home') }}" class="text-purple-600 hover:text-purple-800 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Voltar para Home
                    </a>

                    <!-- Badge da categoria -->
                    <div class="flex items-center space-x-2">
                    <span class="px-3 py-1 rounded-full text-sm font-medium" style="background-color: {{ $video->category->color }}20; color: {{ $video->category->color }}">
                        <i class="{{ $video->category->icon }} mr-1"></i>
                        {{ $video->category->name }}
                    </span>
                        <span class="px-2 py-1 rounded text-xs font-medium {{ $video->level_color }} text-white">
                        {{ $video->level_text }}
                    </span>
                    </div>
                </div>

                <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $video->title }}</h1>
                <p class="text-gray-600">{{ $video->description }}</p>

                <!-- Estatísticas do vídeo -->
                <div class="mt-4 flex flex-wrap gap-4 text-sm">
                    <div class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full">
                        <i class="fas fa-eye mr-1"></i>
                        {{ number_format($video->views_count) }} visualizações
                    </div>
                    <div class="bg-green-100 text-green-800 px-3 py-1 rounded-full">
                        <i class="fas fa-comments mr-1"></i>
                        {{ $video->comments->count() }} comentários
                    </div>
                    <div class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full">
                        <i class="fas fa-clock mr-1"></i>
                        Duração: {{ $video->duration }}
                    </div>
                    @if($videoView && $videoView->watch_time > 0)
                        <div class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full">
                            <i class="fas fa-history mr-1"></i>
                            Assistido: {{ $videoView->formatted_watch_time }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Player de Vídeo -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <!-- Status do Vídeo -->
                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div id="videoStatus" class="flex items-center space-x-2">
                                        <div class="w-3 h-3 bg-gray-300 rounded-full" id="statusIndicator"></div>
                                        <span id="statusText">Carregando...</span>
                                    </div>
                                    <div class="text-sm opacity-90">
                                        <i class="fas fa-clock mr-1"></i>
                                        <span id="currentTime">0:00</span> / <span id="totalTime">{{ $video->duration }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    @if($videoView && $videoView->completed)
                                        <div class="bg-green-500 px-3 py-1 rounded-full text-sm font-medium flex items-center space-x-2 shadow-lg">
                                            <i class="fas fa-check-circle"></i>
                                            <span>Vídeo Assistido</span>
                                        </div>
                                    @endif
                                    <div class="text-sm opacity-90">
                                        <span id="progressPercent">{{ $videoView ? $videoView->progress_percentage : 0 }}%</span> completo
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Container do Vídeo -->
                        <div class="relative bg-black">
                            <div id="youtubePlayer"></div>

                            <!-- Overlay de Loading -->
                            <div id="loadingOverlay" class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                                <div class="text-white text-center">
                                    <i class="fas fa-spinner fa-spin text-4xl mb-2"></i>
                                    <p>Carregando vídeo...</p>
                                </div>
                            </div>
                        </div>

                        <!-- Barra de Progresso -->
                        <div class="p-4 bg-gray-50">
                            <div class="mb-2">
                                <div class="flex justify-between text-sm text-gray-600 mb-1">
                                    <span>Progresso do Vídeo</span>
                                    <span id="watchedTime">0:00 assistido</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2 relative cursor-pointer" id="progressContainer">
                                    <div id="progressBar" class="bg-blue-500 h-2 rounded-full transition-all duration-300 relative" style="width: {{ $videoView ? $videoView->progress_percentage : 0 }}%">
                                        <div class="absolute right-0 top-1/2 transform translate-x-1/2 -translate-y-1/2 w-4 h-4 bg-blue-500 rounded-full shadow-md"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seção de Comentários -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md h-full">
                        <div class="p-4 border-b border-gray-200">
                            <h2 class="text-xl font-bold text-gray-800 flex items-center">
                                <i class="fas fa-comments text-blue-500 mr-2"></i>
                                Comentários
                                <span class="ml-2 bg-blue-100 text-blue-800 text-sm px-2 py-1 rounded-full">
                                {{ $video->comments->count() }}
                            </span>
                            </h2>
                        </div>

                        <!-- Form de Comentário -->
                        <div class="p-4 border-b border-gray-200 text-gray-800">
                            <form id="commentForm" class="space-y-3">
                                @csrf
                                <input type="hidden" id="videoTimeInput" name="video_timestamp" value="0">

                                <textarea
                                    id="commentContent"
                                    name="content"
                                    placeholder="Escreva seu comentário..."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                    rows="3"
                                    required
                                    maxlength="500"
                                ></textarea>

                                <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">
                                    Comentário em: <span id="commentTimestamp">0:00</span>
                                </span>
                                    <span class="text-xs text-gray-500">
                                    <span id="charCount">0</span>/500
                                </span>
                                </div>

                                <button
                                    type="submit"
                                    class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 disabled:opacity-50"
                                    id="submitComment"
                                >
                                <span class="submit-text">
                                    <i class="fas fa-paper-plane mr-2"></i>
                                    Enviar Comentário
                                </span>
                                    <span class="loading-text hidden">
                                    <i class="fas fa-spinner fa-spin mr-2"></i>
                                    Enviando...
                                </span>
                                </button>
                            </form>
                        </div>

                        <!-- Lista de Comentários -->
                        <div class="p-4 space-y-4 max-h-96 overflow-y-auto" id="commentsList">
                            @forelse($video->comments as $comment)
                                <div class="comment-item bg-gray-50 rounded-lg p-3 relative group" data-comment-id="{{ $comment->id }}">
                                    @if($comment->user_id === auth()->id())
                                        <button
                                            onclick="deleteComment({{ $comment->id }})"
                                            class="absolute top-2 right-2 text-red-500 hover:text-red-700 opacity-0 group-hover:opacity-100 transition-opacity"
                                            title="Excluir comentário"
                                        >
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    @endif

                                    <div class="flex items-start space-x-3">
                                        <img
                                            src="{{ $comment->user->avatar_url }}"
                                            alt="{{ $comment->user->name }}"
                                            class="w-8 h-8 rounded-full flex-shrink-0"
                                        >
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between mb-1">
                                                <h4 class="font-medium text-gray-800 truncate text-sm">{{ $comment->user->name }}</h4>
                                                <span class="text-xs text-gray-500 flex-shrink-0 ml-2">{{ $comment->formatted_timestamp }}</span>
                                            </div>
                                            <p class="text-gray-600 text-sm break-words">{{ $comment->content }}</p>
                                            <p class="text-xs text-gray-400 mt-1">{{ $comment->created_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-gray-500 py-8" id="noComments">
                                    <i class="fas fa-comment text-4xl mb-2 opacity-50"></i>
                                    <p>Nenhum comentário ainda.</p>
                                    <p class="text-sm">Seja o primeiro a comentar!</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let player;
            let videoData = {
                currentTime: 0,
                duration: 0,
                isPlaying: false,
                isPaused: false
            };

            // Carregar API do YouTube
            function onYouTubeIframeAPIReady() {
                player = new YT.Player('youtubePlayer', {
                    height: '400',
                    width: '100%',
                    videoId: '{{ $video->youtube_id }}',
                    playerVars: {
                        'rel': 0,
                        'modestbranding': 1,
                        'showinfo': 0,
                        'controls': 1,
                        'disablekb': 1,
                        'iv_load_policy': 3,
                        'cc_load_policy': 0,
                        'playsinline': 1
                    },
                    events: {
                        'onReady': onPlayerReady,
                        'onStateChange': onPlayerStateChange
                    }
                });
            }

            function onPlayerReady(event) {
                document.getElementById('loadingOverlay').style.display = 'none';
                updateVideoInfo();

                // Começar do ponto onde parou se aplicável
                @if($videoView && $videoView->watch_time > 0)
                player.seekTo({{ $videoView->watch_time }}, true);
                @endif

                // Atualizar progresso a cada 5 segundos
                setInterval(updateProgress, 5000);

                // Progress bar clicável
                document.getElementById('progressContainer').addEventListener('click', function(e) {
                    if (!player || !player.getDuration) return;

                    const rect = this.getBoundingClientRect();
                    const clickX = e.clientX - rect.left;
                    const percentage = clickX / rect.width;
                    const seekTime = percentage * player.getDuration();

                    player.seekTo(seekTime, true);
                });
            }

            function onPlayerStateChange(event) {
                const status = event.data;

                switch(status) {
                    case YT.PlayerState.PLAYING:
                        videoData.isPlaying = true;
                        videoData.isPaused = false;
                        updateStatus('Reproduzindo', 'bg-green-500');
                        break;

                    case YT.PlayerState.PAUSED:
                        videoData.isPlaying = false;
                        videoData.isPaused = true;
                        updateStatus('Pausado', 'bg-yellow-500');
                        break;

                    case YT.PlayerState.ENDED:
                        updateStatus('Concluído', 'bg-green-600');
                        break;
                }
            }

            function updateStatus(text, bgClass) {
                const indicator = document.getElementById('statusIndicator');
                const statusText = document.getElementById('statusText');

                indicator.className = `w-3 h-3 rounded-full ${bgClass}`;
                statusText.textContent = text;
            }

            function updateVideoInfo() {
                if (player && player.getDuration) {
                    videoData.duration = player.getDuration();
                    document.getElementById('totalTime').textContent = formatTime(videoData.duration);
                }
            }

            function updateProgress() {
                if (player && player.getCurrentTime) {
                    videoData.currentTime = player.getCurrentTime();
                    const progress = (videoData.currentTime / videoData.duration) * 100;

                    document.getElementById('currentTime').textContent = formatTime(videoData.currentTime);
                    document.getElementById('progressPercent').textContent = Math.round(progress) + '%';
                    document.getElementById('progressBar').style.width = progress + '%';
                    document.getElementById('watchedTime').textContent = formatTime(videoData.currentTime) + ' assistido';
                    document.getElementById('commentTimestamp').textContent = formatTime(videoData.currentTime);
                    document.getElementById('videoTimeInput').value = Math.floor(videoData.currentTime);

                    // Enviar progresso para o servidor
                    fetch(`{{ route('videos.progress', $video) }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': window.Laravel.csrfToken
                        },
                        body: JSON.stringify({
                            current_time: Math.floor(videoData.currentTime),
                            duration: Math.floor(videoData.duration)
                        })
                    }).catch(console.error);
                }
            }

            function formatTime(seconds) {
                const mins = Math.floor(seconds / 60);
                const secs = Math.floor(seconds % 60);
                return `${mins}:${secs.toString().padStart(2, '0')}`;
            }

            // Sistema de comentários
            document.getElementById('commentForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const content = document.getElementById('commentContent').value.trim();
                const timestamp = document.getElementById('videoTimeInput').value;

                if (!content) return;

                const submitBtn = document.getElementById('submitComment');
                const submitText = submitBtn.querySelector('.submit-text');
                const loadingText = submitBtn.querySelector('.loading-text');

                submitBtn.disabled = true;
                submitText.classList.add('hidden');
                loadingText.classList.remove('hidden');

                fetch(`{{ route('videos.comments.store', $video) }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.Laravel.csrfToken
                    },
                    body: JSON.stringify({
                        content: content,
                        video_timestamp: parseInt(timestamp)
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            addCommentToList(data.comment);
                            document.getElementById('commentContent').value = '';
                            updateCharCount();
                        } else {
                            alert('Erro ao enviar comentário');
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        alert('Erro ao enviar comentário');
                    })
                    .finally(() => {
                        submitBtn.disabled = false;
                        submitText.classList.remove('hidden');
                        loadingText.classList.add('hidden');
                    });
            });

            function addCommentToList(comment) {
                const commentsList = document.getElementById('commentsList');
                const noComments = document.getElementById('noComments');

                if (noComments) {
                    noComments.remove();
                }

                const commentHtml = `
            <div class="comment-item bg-gray-50 rounded-lg p-3 relative group" data-comment-id="${comment.id}">
                <button
                    onclick="deleteComment(${comment.id})"
                    class="absolute top-2 right-2 text-red-500 hover:text-red-700 opacity-0 group-hover:opacity-100 transition-opacity"
                    title="Excluir comentário"
                >
                    <i class="fas fa-trash text-xs"></i>
                </button>

                <div class="flex items-start space-x-3">
                    <img
                        src="${comment.user.avatar_url}"
                        alt="${comment.user.name}"
                        class="w-8 h-8 rounded-full flex-shrink-0"
                    >
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <h4 class="font-medium text-gray-800 truncate text-sm">${comment.user.name}</h4>
                            <span class="text-xs text-gray-500 flex-shrink-0 ml-2">${comment.formatted_timestamp}</span>
                        </div>
                        <p class="text-gray-600 text-sm break-words">${comment.content}</p>
                        <p class="text-xs text-gray-400 mt-1">${comment.created_at}</p>
                    </div>
                </div>
            </div>
        `;

                commentsList.insertAdjacentHTML('afterbegin', commentHtml);
            }

            function deleteComment(commentId) {
                if (!confirm('Deseja realmente excluir este comentário?')) return;

                fetch(`{{ route('videos.comments.destroy', ['video' => $video, 'comment' => '__ID__']) }}`.replace('__ID__', commentId), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': window.Laravel.csrfToken
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.querySelector(`[data-comment-id="${commentId}"]`).remove();

                            // Se não há mais comentários, mostrar mensagem
                            const remainingComments = document.querySelectorAll('.comment-item');
                            if (remainingComments.length === 0) {
                                document.getElementById('commentsList').innerHTML = `
                        <div class="text-center text-gray-500 py-8" id="noComments">
                            <i class="fas fa-comment text-4xl mb-2 opacity-50"></i>
                            <p>Nenhum comentário ainda.</p>
                            <p class="text-sm">Seja o primeiro a comentar!</p>
                        </div>
                    `;
                            }
                        }
                    })
                    .catch(console.error);
            }

            // Contador de caracteres
            function updateCharCount() {
                const content = document.getElementById('commentContent').value;
                document.getElementById('charCount').textContent = content.length;
            }

            document.getElementById('commentContent').addEventListener('input', updateCharCount);

            // Carregar API do YouTube
            if (!window.YT) {
                const tag = document.createElement('script');
                tag.src = "https://www.youtube.com/iframe_api";
                const firstScriptTag = document.getElementsByTagName('script')[0];
                firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
            } else if (window.YT && window.YT.Player) {
                onYouTubeIframeAPIReady();
            }
        </script>
    @endpush
@endsection
