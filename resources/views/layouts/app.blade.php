<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'VideoHub - Sua Plataforma de Aprendizado Online')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'scale-in': 'scaleIn 0.3s ease-out',
                        'slide-in-right': 'slideInRight 0.3s ease-out',
                        'pulse-slow': 'pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite'
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        scaleIn: {
                            '0%': { transform: 'scale(0.9)' },
                            '100%': { transform: 'scale(1)' }
                        },
                        slideInRight: {
                            '0%': { transform: 'translateX(100%)', opacity: '0' },
                            '100%': { transform: 'translateX(0)', opacity: '1' }
                        }
                    }
                }
            }
        }
    </script>

    @stack('styles')
</head>
<style>
    .ultra-smooth {
        background: linear-gradient(135deg,
        #111827 0%,     /* Começa já escuro */
        #0f172a 33%,    /* Ligeiramente diferente */
        #0c1426 66%,    /* Quase preto */
        #000000 100%    /* preto */
        );
        min-height: 100vh;
    }
</style>

<body class="ultra-smooth text-white overflow-x-hidden">

<!-- Header -->
<header class="bg-black/20 backdrop-blur-lg border-b border-white/10 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-play-circle text-white text-xl"></i>
                </div>
                <a href="{{ route('home') }}" class="text-2xl font-bold bg-gradient-to-r from-white to-purple-200 bg-clip-text text-transparent">
                    VideoHub
                </a>
            </div>

            <nav class="hidden md:flex items-center space-x-8">
                <a href="{{ route('home') }}" class="text-purple-200 hover:text-white transition-colors">Início</a>
                <a href="{{ route('home') }}#categorias" class="text-purple-200 hover:text-white transition-colors">Categorias</a>
                <a href="{{ route('plans') }}" class="text-purple-200 hover:text-white transition-colors">Planos</a>

                @auth
                    <div class="flex items-center space-x-4">
                        <div class="relative group">
                            <button class="flex items-center space-x-2 text-purple-200 hover:text-white transition-colors">
                                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full">
                                <span>{{ auth()->user()->name }}</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>

                            <!-- Dropdown Menu -->
                            <div class="absolute right-0 mt-2 w-48 bg-slate-800 rounded-lg shadow-lg border border-white/10 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                                <div class="py-2">
                                    <a href="{{ route('profile.index') }}" class="block px-4 py-2 text-white hover:bg-purple-600/20 transition-colors">
                                        <i class="fas fa-user mr-2"></i>Meu Perfil
                                    </a>
                                    <div class="border-t border-white/10 my-1"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-white hover:bg-red-600/20 transition-colors">
                                            <i class="fas fa-sign-out-alt mr-2"></i>Sair
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('login') }}" class="text-purple-200 hover:text-white transition-colors">
                            <i class="fas fa-sign-in-alt mr-2"></i>Login
                        </a>
                        <a href="{{ route('register') }}" class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 px-4 py-2 rounded-lg transition-all duration-300">
                            <i class="fas fa-user-plus mr-2"></i>Cadastrar
                        </a>
                    </div>
                @endauth
            </nav>

            <!-- Mobile Menu Button -->
            <button class="md:hidden text-white" onclick="toggleMobileMenu()">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="hidden md:hidden mt-4 pt-4 border-t border-white/10">
            <div class="flex flex-col space-y-4">
                <a href="{{ route('home') }}" class="text-purple-200 hover:text-white transition-colors">Início</a>
                <a href="{{ route('home') }}#categorias" class="text-purple-200 hover:text-white transition-colors">Categorias</a>
                <a href="{{ route('plans') }}" class="text-purple-200 hover:text-white transition-colors">Planos</a>

                @auth
                    <div class="flex flex-col space-y-2 pt-2 border-t border-white/10">
                        <div class="flex items-center space-x-2 text-purple-200">
                            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-6 h-6 rounded-full">
                            <span>{{ auth()->user()->name }}</span>
                        </div>
                        <a href="{{ route('profile.index') }}" class="text-purple-200 hover:text-white transition-colors ml-8">Meu Perfil</a>
                        <form method="POST" action="{{ route('logout') }}" class="ml-8">
                            @csrf
                            <button type="submit" class="text-red-400 hover:text-red-300 transition-colors">
                                <i class="fas fa-sign-out-alt mr-2"></i>Sair
                            </button>
                        </form>
                    </div>
                @else
                    <div class="flex flex-col space-y-2 pt-2 border-t border-white/10">
                        <a href="{{ route('login') }}" class="text-purple-200 hover:text-white transition-colors">
                            <i class="fas fa-sign-in-alt mr-2"></i>Login
                        </a>
                        <a href="{{ route('register') }}" class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 px-4 py-2 rounded-lg transition-all duration-300 text-center">
                            <i class="fas fa-user-plus mr-2"></i>Cadastrar
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</header>

<!-- Flash Messages -->
@if(session('success'))
    <div id="flash-message" class="fixed top-20 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-in-right">
        <div class="flex items-center space-x-2">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
            <button onclick="closeFlashMessage()" class="ml-4 text-white/80 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
@endif

@if(session('error'))
    <div id="flash-message" class="fixed top-20 right-4 bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-in-right">
        <div class="flex items-center space-x-2">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
            <button onclick="closeFlashMessage()" class="ml-4 text-white/80 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
@endif

@if(session('info'))
    <div id="flash-message" class="fixed top-20 right-4 bg-blue-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-in-right">
        <div class="flex items-center space-x-2">
            <i class="fas fa-info-circle"></i>
            <span>{{ session('info') }}</span>
            <button onclick="closeFlashMessage()" class="ml-4 text-white/80 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
@endif

<!-- Main Content -->
<main>
    @yield('content')
</main>

<!-- Footer -->
<footer class="bg-black/20 backdrop-blur-lg border-t border-white/10 mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-play-circle text-white text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold bg-gradient-to-r from-white to-purple-200 bg-clip-text text-transparent">
                        VideoHub
                    </h2>
                </div>
                <p class="text-purple-200 mb-6 max-w-md">
                    Transforme seu futuro com os melhores cursos online.
                    Aprenda no seu ritmo, onde e quando quiser.
                </p>
                <div class="flex space-x-4">
                    <a href="#" class="bg-white/10 hover:bg-white/20 p-3 rounded-lg transition-colors">
                        <i class="fab fa-facebook-f text-purple-300"></i>
                    </a>
                    <a href="#" class="bg-white/10 hover:bg-white/20 p-3 rounded-lg transition-colors">
                        <i class="fab fa-twitter text-purple-300"></i>
                    </a>
                    <a href="#" class="bg-white/10 hover:bg-white/20 p-3 rounded-lg transition-colors">
                        <i class="fab fa-instagram text-purple-300"></i>
                    </a>
                    <a href="#" class="bg-white/10 hover:bg-white/20 p-3 rounded-lg transition-colors">
                        <i class="fab fa-youtube text-purple-300"></i>
                    </a>
                </div>
            </div>

            <div>
                <h3 class="text-white font-bold mb-4">Cursos</h3>
                <ul class="space-y-2 text-purple-200">
                    <li><a href="#" class="hover:text-white transition-colors">Tecnologia</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Educação</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Entretenimento</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Novidades</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-white font-bold mb-4">Suporte</h3>
                <ul class="space-y-2 text-purple-200">
                    <li><a href="#" class="hover:text-white transition-colors">Central de Ajuda</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Contato</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Termos de Uso</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Privacidade</a></li>
                </ul>
            </div>
        </div>

        <div class="border-t border-white/10 mt-8 pt-8 text-center">
            <p class="text-purple-200">
                © {{ date('Y') }} VideoHub. Todos os direitos reservados.
            </p>
        </div>
    </div>
</footer>

<script>
    function toggleMobileMenu() {
        const menu = document.getElementById('mobileMenu');
        menu.classList.toggle('hidden');
    }

    function closeFlashMessage() {
        const message = document.getElementById('flash-message');
        if (message) {
            message.style.transform = 'translateX(100%)';
            setTimeout(() => message.remove(), 300);
        }
    }

    // Auto-hide flash messages after 5 seconds
    setTimeout(() => {
        const message = document.getElementById('flash-message');
        if (message) {
            closeFlashMessage();
        }
    }, 5000);

    // Smooth scroll para âncoras
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // CSRF token para requisições AJAX
    window.Laravel = {
        csrfToken: '{{ csrf_token() }}'
    };

    // Configurar axios se disponível
    if (typeof axios !== 'undefined') {
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        axios.defaults.headers.common['X-CSRF-TOKEN'] = window.Laravel.csrfToken;
    }
</script>

@stack('scripts')
</body>
</html>


