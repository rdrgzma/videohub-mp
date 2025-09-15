{{-- ======================================== --}}

{{-- resources/views/components/plan-card.blade.php --}}
@props(['plan', 'featured' => false])

<div class="bg-white/5 backdrop-blur-sm rounded-2xl p-8 border border-white/10 hover:border-purple-500/50 transition-all duration-300 {{ $featured ? 'transform scale-105 border-2 border-purple-500/50 relative' : '' }}">
    @if($featured)
        <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-gradient-to-r from-purple-500 to-pink-500 px-4 py-1 rounded-full text-sm font-bold text-white">
            MAIS POPULAR
        </div>
    @endif

    <div class="text-center">
        <h4 class="text-xl font-bold text-white mb-2">{{ $plan->name }}</h4>
        <div class="text-3xl font-bold text-purple-400 mb-1">{{ $plan->formatted_price }}</div>
        <div class="text-sm text-purple-200 mb-4">{{ $plan->billing_cycle_text }}</div>

        @if($plan->description)
            <p class="text-purple-200 text-sm mb-6">{{ $plan->description }}</p>
        @endif

        <ul class="text-purple-200 space-y-2 mb-6 text-left">
            @foreach($plan->features_list as $feature)
                <li class="flex items-center">
                    <i class="fas fa-check text-green-400 mr-2 flex-shrink-0"></i>
                    <span>{{ $feature }}</span>
                </li>
            @endforeach
        </ul>

        @auth
            @if(auth()->user()->hasActivePlan())
                <button disabled class="w-full bg-gray-600 text-gray-300 font-medium py-3 rounded-lg cursor-not-allowed">
                    <i class="fas fa-check mr-2"></i>
                    Plano Ativo
                </button>
            @else
                <a
                    href="{{ route('subscription.checkout', $plan->slug) }}"
                    class="block w-full {{ $featured ? 'bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600' : 'bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800' }} text-white font-medium py-3 rounded-lg transition-all duration-300 text-center"
                >
                    Escolher Plano
                </a>
            @endif
        @else
            <a
                href="{{ route('register') }}"
                class="block w-full {{ $featured ? 'bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600' : 'bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800' }} text-white font-medium py-3 rounded-lg transition-all duration-300 text-center"
            >
                Começar Agora
            </a>
        @endauth
    </div>
</div>
