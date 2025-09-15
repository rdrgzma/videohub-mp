@if($video)
    <div class="group bg-white/5 backdrop-blur-sm rounded-2xl overflow-hidden border border-white/10 hover:border-purple-500/50 transition-all duration-300 hover:shadow-2xl hover:shadow-purple-500/20 animate-scale-in relative">
        <!-- Badge de Nível -->
        <div class="absolute top-3 left-3 z-10 {{ $video->level_color ?? 'bg-gray-500' }}/80 backdrop-blur-sm px-2 py-1 rounded-md text-xs font-medium text-white border border-white/20">
            {{ $video->level_text ?? 'N/A' }}
        </div>

        <!-- Premium Badge -->
        @if($video->is_premium ?? false)
            <div class="absolute top-3 right-3 z-10 bg-yellow-500/80 backdrop-blur-sm px-2 py-1 rounded-md text-xs font-medium text-black">
                <i class="fas fa-crown mr-1"></i>Premium
            </div>
        @endif

        <!-- Lock Overlay para usuários sem acesso -->
        @if($showLock)
            <div class="absolute inset-0 bg-black/80 backdrop-blur-sm z-20 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-2xl">
                <i class="fas fa-lock text-4xl text-purple-400 mb-4"></i>
                <h4 class="text-lg font-bold text-white mb-2">Conteúdo Premium</h4>
                <p class="text-purple-200 text-center text-sm mb-4 px-4">Faça login ou assine um plano para assistir</p>
                <div class="flex space-x-2">
                    @guest
                        <a href="{{ route('login') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="bg-pink-600 hover:bg-pink-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                            Cadastrar
                        </a>
                    @else
                        <a href="{{ route('plans') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                            Assinar Plano
                        </a>
                    @endguest
                </div>
            </div>
        @endif

        <!-- Thumbnail -->
        <div class="relative overflow-hidden">
            <img
                src="{{ $video->thumbnail_url ?? 'https://via.placeholder.com/400x300?text=Video' }}"
                alt="{{ $video->title ?? 'Vídeo' }}"
                class="w-full h-48 object-cover transition-transform duration-300 group-hover:scale-110"
                loading="lazy"
                onerror="this.src='https://via.placeholder.com/400x300?text=Video+Indisponivel'"
            >
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
            <div class="absolute bottom-3 right-3 bg-black/80 text-white text-sm px-2 py-1 rounded-md">
                {{ $video->duration ?? '0:00' }}
            </div>

            <!-- Play Overlay -->
            @if(!$showLock)
                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-black/30">
                    <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center border border-white/30">
                        <i class="fas fa-play text-white text-xl ml-1"></i>
                    </div>
                </div>
            @endif
        </div>

        <!-- Content -->
        <div class="p-6">
            <h4 class="font-bold text-lg mb-2 text-white group-hover:text-purple-300 transition-colors">
                {{ $video->title ?? 'Título do Vídeo' }}
            </h4>
            <p class="text-purple-200 text-sm mb-4 line-clamp-2">
                {{ $video->description ?? 'Descrição do vídeo não disponível.' }}
            </p>

            <!-- Action Button -->
            <div class="flex justify-between items-center">
                @if(!$showLock && $video)
                    <a
                        href="{{ $video->watch_url ?? '#' }}"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-medium rounded-lg transition-all duration-300 hover:shadow-lg hover:shadow-purple-500/25 transform hover:-translate-y-0.5"
                    >
                        <i class="fas fa-play mr-2"></i>
                        Assistir
                    </a>
                @else
                    @guest
                        <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-all duration-300">
                            <i class="fas fa-lock mr-2"></i>
                            Fazer Login
                        </a>
                    @else
                        <a href="{{ route('plans') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-all duration-300">
                            <i class="fas fa-crown mr-2"></i>
                            Assinar
                        </a>
                    @endguest
                @endif

                <button class="p-2 text-purple-300 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-300">
                    <i class="fas fa-heart"></i>
                </button>
            </div>
        </div>
    </div>
@else
    {{-- Fallback caso não tenha vídeo --}}
    <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/10 text-center">
        <i class="fas fa-video text-4xl text-purple-400 mb-4"></i>
        <p class="text-purple-200">Vídeo não encontrado</p>
    </div>
@endif



