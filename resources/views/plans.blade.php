@extends('layouts.app')

@section('title', 'Planos - VideoHub')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Hero Section -->
        <section class="text-center mb-16 animate-fade-in">
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-r from-purple-600/20 to-pink-600/20 rounded-3xl blur-3xl"></div>
                <div class="relative bg-white/5 backdrop-blur-sm rounded-3xl p-8 md:p-12 border border-white/10">
                    <h2 class="text-4xl md:text-5xl font-bold mb-6 bg-gradient-to-r from-purple-400 via-pink-400 to-purple-400 bg-clip-text text-transparent">
                        Escolha Seu Plano
                    </h2>
                    <p class="text-lg md:text-xl text-purple-200 max-w-3xl mx-auto mb-8">
                        Acesso ilimitado a todos os cursos e materiais exclusivos.
                        Cancele quando quiser, sem complicações.
                    </p>
                </div>
            </div>
        </section>

        <!-- Planos -->
        <section class="mb-16">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($plans as $plan)
                    <x-plan-card :plan="$plan" :featured="$plan->is_popular" />
                @endforeach
            </div>
        </section>

        <!-- FAQ -->
        <section class="bg-white/5 backdrop-blur-sm rounded-3xl p-8 md:p-12 border border-white/10">
            <h3 class="text-2xl font-bold text-center mb-8 text-white">Perguntas Frequentes</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h4 class="text-lg font-bold text-white mb-2">Posso cancelar a qualquer momento?</h4>
                    <p class="text-purple-200">Sim! Você pode cancelar sua assinatura a qualquer momento sem taxas de cancelamento.</p>
                </div>

                <div>
                    <h4 class="text-lg font-bold text-white mb-2">Os certificados são reconhecidos?</h4>
                    <p class="text-purple-200">Nossos certificados são reconhecidos por empresas parceiras e podem ser adicionados ao seu LinkedIn.</p>
                </div>

                <div>
                    <h4 class="text-lg font-bold text-white mb-2">Posso assistir offline?</h4>
                    <p class="text-purple-200">Atualmente os vídeos precisam ser assistidos online, mas estamos trabalhando na funcionalidade offline.</p>
                </div>

                <div>
                    <h4 class="text-lg font-bold text-white mb-2">Há suporte técnico?</h4>
                    <p class="text-purple-200">Sim! Oferecemos suporte via chat e email para todos os assinantes.</p>
                </div>
            </div>
        </section>
    </div>
@endsection

