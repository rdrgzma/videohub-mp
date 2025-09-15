@extends('layouts.app')

@section('title', 'VideoHub - Sua Plataforma de Aprendizado Online')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Hero Section -->
        <section id="inicio" class="text-center mb-16 animate-fade-in">
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-r from-purple-600 to-pink-600 rounded-3xl blur-3xl"></div>
                <div class="relative bg-white/5 backdrop-blur-sm rounded-3xl p-8 md:p-12 border border-white/10">
                    <h2 class="text-4xl md:text-6xl font-bold mb-6 bg-gradient-to-r from-purple-400 via-pink-400 to-purple-400 bg-clip-text text-transparent animate-pulse-slow">
                        Transforme seu Futuro
                    </h2>
                    <p class="text-lg md:text-xl text-purple-200 max-w-3xl mx-auto mb-8">
                        Acesse mais de <span class="text-yellow-400 font-bold">{{ $stats['total_videos'] }} vídeos exclusivos</span>
                        e domine as habilidades do futuro com nossos cursos premium
                    </p>

                    <!-- Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-gradient-to-r from-blue-600/20 to-blue-800/20 rounded-xl p-4 border border-blue-500/30">
                            <div class="text-3xl font-bold text-blue-400">{{ $stats['total_videos'] }}+</div>
                            <div class="text-blue-200">Vídeos Premium</div>
                        </div>
                        <div class="bg-gradient-to-r from-green-600/20 to-green-800/20 rounded-xl p-4 border border-green-500/30">
                            <div class="text-3xl font-bold text-green-400">{{ number_format($stats['total_students']) }}+</div>
                            <div class="text-green-200">Alunos Ativos</div>
                        </div>
                        <div class="bg-gradient-to-r from-yellow-600/20 to-yellow-800/20 rounded-xl p-4 border border-yellow-500/30">
                            <div class="text-3xl font-bold text-yellow-400">{{ $stats['average_rating'] }}★</div>
                            <div class="text-yellow-200">Avaliação Média</div>
                        </div>
                    </div>

                    @guest
                        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                            <a href="{{ route('register') }}" class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-bold py-4 px-8 rounded-xl text-lg transition-all duration-300 transform hover:scale-105 shadow-2xl hover:shadow-purple-500/25">
                                <i class="fas fa-rocket mr-2"></i>
                                Começar Agora
                            </a>
                            <a href="{{ route('login') }}" class="border-2 border-white/30 hover:border-white/50 text-white font-bold py-4 px-8 rounded-xl text-lg transition-all duration-300 hover:bg-white/10">
                                <i class="fas fa-sign-in-alt mr-2"></i>
                                Já sou Aluno
                            </a>
                        </div>
                    @else
                        <div class="bg-green-500/20 border border-green-500/50 rounded-xl p-6">
                            <h3 class="text-2xl font-bold text-green-400 mb-2">
                                <i class="fas fa-check-circle mr-2"></i>
                                Bem-vindo de volta, {{ auth()->user()->name }}!
                            </h3>
                            <p class="text-green-200 mb-4">Continue de onde parou e explore novos conteúdos</p>
                            @if(auth()->user()->hasActivePlan())
                                <p class="text-green-300 text-sm mb-4">
                                    <i class="fas fa-crown mr-1"></i>
                                    Plano {{ auth()->user()->currentPlan->name }} ativo até {{ auth()->user()->plan_expires_at->format('d/m/Y') }}
                                </p>
                            @endif
                            <a href="#categorias" class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white font-medium px-6 py-3 rounded-lg transition-colors">
                                <i class="fas fa-play mr-2"></i>
                                Ver Meus Cursos
                            </a>
                        </div>
                    @endguest
                </div>
            </div>
        </section>

        <!-- Planos de Preços -->
        @guest
            <section class="mb-16 animate-fade-in">
                <div class="text-center mb-12">
                    <h3 class="text-3xl font-bold mb-4 bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">
                        Escolha Seu Plano
                    </h3>
                    <p class="text-purple-200 text-lg">Acesso ilimitado a todos os cursos e materiais exclusivos</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach($plans as $plan)
                        <x-plan-card :plan="$plan" :featured="$plan->is_popular" />
                    @endforeach
                </div>
            </section>
        @else
            @if(!auth()->user()->hasActivePlan())
                <section class="mb-16 animate-fade-in">
                    <div class="bg-gradient-to-r from-purple-600/10 to-pink-600/10 backdrop-blur-sm rounded-3xl p-8 border border-purple-500/30">
                        <div class="text-center mb-8">
                            <h3 class="text-2xl font-bold text-white mb-2">
                                <i class="fas fa-crown text-yellow-400 mr-2"></i>
                                Desbloqueie Todo o Conteúdo Premium
                            </h3>
                            <p class="text-purple-200">Assine um plano e tenha acesso completo à nossa biblioteca</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            @foreach($plans as $plan)
                                <x-plan-card :plan="$plan" :featured="$plan->is_popular" />
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif
        @endguest

        <!-- Videos Grid -->
        <section id="categorias">
            @foreach($categories as $category)
                @if($category->publishedVideos->isNotEmpty())
                    <div class="mb-12 animate-fade-in">
                        <div class="flex items-center mb-6">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center mr-4" style="background-color: {{ $category->color }}">
                                <i class="{{ $category->icon }} text-white"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-white mr-4">{{ $category->name }}</h3>
                            <div class="flex-1 h-px bg-gradient-to-r from-purple-500/50 to-transparent"></div>
                            <span class="text-sm text-purple-300 ml-4">{{ $category->published_videos_count }} vídeos</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($category->publishedVideos as $video)
                                @php
                                    $showLock = $video->is_premium && (!auth()->check() || !auth()->user()->canAccessPremiumContent());
                                @endphp
                                <x-video-card :video="$video" :show-lock="$showLock" />
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </section>

        <!-- Seção Sobre -->
        <section id="sobre" class="mb-16 animate-fade-in">
            <div class="bg-gradient-to-r from-purple-600/10 to-pink-600/10 backdrop-blur-sm rounded-3xl p-8 md:p-12 border border-white/10">
                <div class="text-center mb-12">
                    <h3 class="text-3xl font-bold mb-4 bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">
                        Por que escolher a VideoHub?
                    </h3>
                    <p class="text-purple-200 text-lg max-w-3xl mx-auto">
                        Somos mais que uma plataforma de cursos. Somos seu parceiro na jornada de crescimento profissional.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-graduation-cap text-white text-2xl"></i>
                        </div>
                        <h4 class="text-xl font-bold text-white mb-2">Ensino de Qualidade</h4>
                        <p class="text-purple-200">Conteúdo criado por especialistas reconhecidos no mercado</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-certificate text-white text-2xl"></i>
                        </div>
                        <h4 class="text-xl font-bold text-white mb-2">Certificação</h4>
                        <p class="text-purple-200">Certificados reconhecidos no mercado para impulsionar sua carreira</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-headset text-white text-2xl"></i>
                        </div>
                        <h4 class="text-xl font-bold text-white mb-2">Suporte 24/7</h4>
                        <p class="text-purple-200">Nossa equipe está sempre pronta para ajudar você a crescer</p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @push('scripts')
        <script>
            // Animações de entrada quando os elementos aparecem na tela
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Observar todos os cards de vídeo
            document.querySelectorAll('.group').forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(card);
            });
        </script>
    @endpush
@endsection
