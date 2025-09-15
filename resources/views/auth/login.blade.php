@extends('layouts.app')

@section('title', 'Login - VideoHub')

@section('content')
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user text-white text-2xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-white">Bem-vindo de volta!</h2>
                <p class="mt-2 text-purple-200">Entre na sua conta para continuar</p>
            </div>

            <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-8 border border-white/10">
                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="email" class="block text-purple-200 text-sm font-medium mb-2">E-mail</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            required
                            autofocus
                            value="{{ old('email') }}"
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-purple-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all @error('email') border-red-500 @enderror"
                            placeholder="seu@email.com"
                        >
                        @error('email')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-purple-200 text-sm font-medium mb-2">Senha</label>
                        <div class="relative">
                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-purple-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all pr-12 @error('password') border-red-500 @enderror"
                                placeholder="••••••••"
                            >
                            <button
                                type="button"
                                onclick="togglePassword('password')"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-purple-300 hover:text-white"
                            >
                                <i class="fas fa-eye" id="passwordIcon"></i>
                            </button>
                        </div>
                        @error('password')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center text-purple-200 text-sm">
                            <input
                                type="checkbox"
                                name="remember"
                                class="mr-2 rounded bg-white/10 border-white/20 text-purple-600 focus:ring-purple-500"
                            >
                            Lembrar-me
                        </label>
                        <a href="#" class="text-purple-400 hover:text-purple-300 text-sm">Esqueci a senha</a>
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-bold py-3 rounded-lg transition-all duration-300 transform hover:scale-105"
                    >
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Entrar
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-purple-200 text-sm">
                        Não tem conta?
                        <a href="{{ route('register') }}" class="text-purple-400 hover:text-purple-300 font-medium">
                            Cadastre-se aqui
                        </a>
                    </p>
                </div>
            </div>

            <!-- Demo Credentials -->
            <div class="bg-blue-600/10 border border-blue-500/30 rounded-lg p-4">
                <h4 class="text-blue-300 font-medium mb-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    Credenciais de Demonstração
                </h4>
                <div class="text-blue-200 text-sm space-y-1">
                    <p><strong>E-mail:</strong> user@user.com</p>
                    <p><strong>Senha:</strong> user123</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function togglePassword(inputId) {
                const input = document.getElementById(inputId);
                const icon = document.getElementById(inputId + 'Icon');

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.className = 'fas fa-eye-slash';
                } else {
                    input.type = 'password';
                    icon.className = 'fas fa-eye';
                }
            }
        </script>
    @endpush
@endsection

