@extends('layouts.app')

@section('title', 'Cadastro - VideoHub')

@section('content')
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-plus text-white text-2xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-white">Crie sua conta</h2>
                <p class="mt-2 text-purple-200">Comece sua jornada de aprendizado</p>
            </div>

            <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-8 border border-white/10">
                <form method="POST" action="{{ route('register') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="name" class="block text-purple-200 text-sm font-medium mb-2">Nome Completo</label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            required
                            autofocus
                            value="{{ old('name') }}"
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-purple-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all @error('name') border-red-500 @enderror"
                            placeholder="Seu nome completo"
                        >
                        @error('name')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-purple-200 text-sm font-medium mb-2">E-mail</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            required
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
                        <div class="mt-1 text-xs text-purple-300">
                            Mínimo 8 caracteres
                        </div>
                        @error('password')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-purple-200 text-sm font-medium mb-2">Confirmar Senha</label>
                        <div class="relative">
                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                required
                                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-purple-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all pr-12"
                                placeholder="••••••••"
                            >
                            <button
                                type="button"
                                onclick="togglePassword('password_confirmation')"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-purple-300 hover:text-white"
                            >
                                <i class="fas fa-eye" id="password_confirmationIcon"></i>
                            </button>
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-bold py-3 rounded-lg transition-all duration-300 transform hover:scale-105"
                    >
                        <i class="fas fa-user-plus mr-2"></i>
                        Criar Conta
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-purple-200 text-sm">
                        Já tem conta?
                        <a href="{{ route('login') }}" class="text-purple-400 hover:text-purple-300 font-medium">
                            Faça login
                        </a>
                    </p>
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

            // Validação de senha em tempo real
            document.getElementById('password_confirmation').addEventListener('input', function() {
                const password = document.getElementById('password').value;
                const confirmation = this.value;

                if (confirmation && password !== confirmation) {
                    this.setCustomValidity('As senhas não conferem');
                    this.classList.add('border-red-500');
                } else {
                    this.setCustomValidity('');
                    this.classList.remove('border-red-500');
                }
            });
        </script>
    @endpush
@endsection
