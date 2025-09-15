@extends('layouts.app')

@section('title', 'Meu Perfil - VideoHub')

@section('content')
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-white">Meu Perfil</h1>
            <a href="{{ route('profile.edit') }}" class="bg-purple-600 hover:bg-purple-700 px-4 py-2 rounded-lg transition-colors text-white">
                <i class="fas fa-edit mr-2"></i>Editar
            </a>
        </div>

        <!-- Profile Card -->
        <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/10 mb-8">
            <div class="flex items-center space-x-6">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-full">
                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-white">{{ $user->name }}</h2>
                    <p class="text-purple-200">{{ $user->email }}</p>
                    @if($user->bio)
                        <p class="text-purple-300 mt-2">{{ $user->bio }}</p>
                    @endif

                    <!-- Status do Plano -->
                    <div class="mt-4">
                        @if($user->hasActivePlan())
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-600/20 text-green-400 border border-green-500/50">
                            <i class="fas fa-crown mr-2"></i>
                            {{ $user->currentPlan->name }} - Expira em {{ $user->plan_expires_at->format('d/m/Y') }}
                        </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-600/20 text-gray-400 border border-gray-500/50">
                            <i class="fas fa-user mr-2"></i>
                            Plano Gratuito
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-blue-600/20 border border-blue-500/30 rounded-xl p-4 text-center">
                <div class="text-3xl font-bold text-blue-400 mb-2">{{ $stats['total_logins'] }}</div>
                <div class="text-blue-200 text-sm">Total de Logins</div>
            </div>

            <div class="bg-green-600/20 border border-green-500/30 rounded-xl p-4 text-center">
                <div class="text-3xl font-bold text-green-400 mb-2">{{ $stats['videos_watched'] }}</div>
                <div class="text-green-200 text-sm">Vídeos Assistidos</div>
            </div>

            <div class="bg-purple-600/20 border border-purple-500/30 rounded-xl p-4 text-center">
                <div class="text-3xl font-bold text-purple-400 mb-2">{{ $user->formatWatchTime($stats['watch_time']) }}</div>
                <div class="text-purple-200 text-sm">Tempo Assistido</div>
            </div>

            <div class="bg-yellow-600/20 border border-yellow-500/30 rounded-xl p-4 text-center">
                <div class="text-3xl font-bold text-yellow-400 mb-2">{{ $stats['days_as_member'] }}</div>
                <div class="text-yellow-200 text-sm">Dias como Membro</div>
            </div>
        </div>

        <!-- Assinaturas -->
        @if($user->subscriptions->isNotEmpty())
            <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/10 mb-8">
                <h3 class="text-xl font-bold text-white mb-4">Histórico de Assinaturas</h3>

                <div class="space-y-4">
                    @foreach($user->subscriptions as $subscription)
                        <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg">
                            <div>
                                <h4 class="font-medium text-white">{{ $subscription->plan->name }}</h4>
                                <p class="text-purple-200 text-sm">
                                    {{ $subscription->starts_at->format('d/m/Y') }} - {{ $subscription->expires_at->format('d/m/Y') }}
                                </p>
                            </div>
                            <div class="text-right">
                                <div class="text-white font-bold">{{ $subscription->formatted_amount }}</div>
                                <span class="text-xs px-2 py-1 rounded
                                {{ $subscription->status === 'active' ? 'bg-green-600/20 text-green-400' : 'bg-gray-600/20 text-gray-400' }}">
                                {{ $subscription->status_text }}
                            </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Últimas Atividades -->
        @if($recentActivities->isNotEmpty())
            <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/10">
                <h3 class="text-xl font-bold text-white mb-4">Atividades Recentes</h3>

                <div class="space-y-3">
                    @foreach($recentActivities as $activity)
                        <div class="flex items-center space-x-3 p-3 bg-white/5 rounded-lg">
                            <div class="w-8 h-8 bg-purple-600/20 rounded-full flex items-center justify-center">
                                <i class="{{ $activity->action_icon }} text-purple-400 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-white text-sm">{{ $activity->description }}</p>
                                <p class="text-purple-300 text-xs">{{ $activity->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
