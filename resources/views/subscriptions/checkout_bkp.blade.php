@extends('layouts.app')

@section('title', 'Checkout - VideoHub')

@section('content')
    <div class="max-w-2xl mx-auto px-4 py-8">
        <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-8 border border-white/10">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-white mb-2">Finalizar Compra</h1>
                <p class="text-purple-200">Complete seu cadastro e escolha a forma de pagamento</p>
            </div>

            <!-- Resumo do Plano -->
            <div class="bg-purple-600/20 rounded-lg p-6 mb-8 border border-purple-500/30">
                <h3 class="text-lg font-bold text-white mb-4">Resumo do Pedido</h3>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="font-medium text-white">{{ $plan->name }}</h4>
                        <p class="text-purple-200 text-sm">{{ $plan->description }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-purple-400">{{ $plan->formatted_price }}</div>
                        <div class="text-purple-200 text-sm">{{ $plan->billing_cycle_text }}</div>
                    </div>
                </div>

                <div class="border-t border-purple-500/30 pt-4">
                    <div class="flex justify-between items-center">
                        <span class="text-white font-bold">Total</span>
                        <span class="text-2xl font-bold text-purple-400">{{ $plan->formatted_price }}</span>
                    </div>
                </div>
            </div>

            <!-- Formulário de Pagamento -->
            <form method="POST" action="{{ route('subscription.process', $plan) }}" id="checkoutForm">
                @csrf

                <!-- Método de Pagamento -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-white mb-4">Forma de Pagamento</h3>

                    <!-- Cartão de Crédito -->
                    <label class="flex items-center p-4 bg-white/5 rounded-lg border border-white/10 hover:border-purple-500/50 cursor-pointer transition-all mb-3">
                        <input type="radio" name="payment_method" value="credit_card" class="mr-3" checked>
                        <div class="flex items-center">
                            <i class="fas fa-credit-card text-purple-400 text-xl mr-3"></i>
                            <span class="text-white font-medium">Cartão de Crédito</span>
                        </div>
                    </label>

                    <div id="creditCardForm" class="ml-8 space-y-3 mb-4">
                        <input
                            type="text"
                            name="card_name"
                            placeholder="Nome no cartão"
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-purple-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all"
                            required
                        >
                        <input
                            type="text"
                            name="card_number"
                            placeholder="Número do cartão"
                            maxlength="19"
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-purple-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all"
                            oninput="formatCardNumber(this)"
                            required
                        >
                        <div class="grid grid-cols-2 gap-3">
                            <input
                                type="text"
                                name="card_expiry"
                                placeholder="MM/AA"
                                maxlength="5"
                                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-purple-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all"
                                oninput="formatExpiry(this)"
                                required
                            >
                            <input
                                type="text"
                                name="card_cvv"
                                placeholder="CVV"
                                maxlength="4"
                                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-purple-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all"
                                required
                            >
                        </div>
                    </div>

                    <!-- PIX -->
                    <label class="flex items-center p-4 bg-white/5 rounded-lg border border-white/10 hover:border-purple-500/50 cursor-pointer transition-all">
                        <input type="radio" name="payment_method" value="pix" class="mr-3">
                        <div class="flex items-center">
                            <i class="fas fa-qrcode text-green-400 text-xl mr-3"></i>
                            <div>
                                <span class="text-white font-medium">PIX</span>
                                <p class="text-purple-200 text-sm">Pagamento instantâneo</p>
                            </div>
                        </div>
                    </label>
                </div>

                <!-- Botões -->
                <div class="flex space-x-3">
                    <a href="{{ route('plans') }}" class="flex-1 border-2 border-white/20 hover:border-white/40 text-white font-medium py-3 rounded-lg transition-all duration-300 text-center">
                        Voltar
                    </a>
                    <button
                        type="submit"
                        class="flex-1 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold py-3 rounded-lg transition-all duration-300"
                        id="submitBtn"
                    >
                    <span class="submit-text">
                        <i class="fas fa-check mr-2"></i>
                        Finalizar Compra
                    </span>
                        <span class="loading-text hidden">
                        <i class="fas fa-spinner fa-spin mr-2"></i>
                        Processando...
                    </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function formatCardNumber(input) {
                let value = input.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
                let formattedValue = value.match(/.{1,4}/g)?.join(' ') || '';
                if (formattedValue.length > 19) {
                    formattedValue = formattedValue.substr(0, 19);
                }
                input.value = formattedValue;
            }

            function formatExpiry(input) {
                let value = input.value.replace(/\D/g, '');
                if (value.length >= 2) {
                    value = value.substring(0, 2) + '/' + value.substring(2, 4);
                }
                input.value = value;
            }

            // Toggle credit card form
            document.addEventListener('change', function(e) {
                if (e.target.name === 'payment_method') {
                    const creditCardForm = document.getElementById('creditCardForm');
                    if (e.target.value === 'credit_card') {
                        creditCardForm.style.display = 'block';
                        creditCardForm.querySelectorAll('input').forEach(input => input.required = true);
                    } else {
                        creditCardForm.style.display = 'none';
                        creditCardForm.querySelectorAll('input').forEach(input => input.required = false);
                    }
                }
            });

            // Form submission
            document.getElementById('checkoutForm').addEventListener('submit', function(e) {
                const submitBtn = document.getElementById('submitBtn');
                const submitText = submitBtn.querySelector('.submit-text');
                const loadingText = submitBtn.querySelector('.loading-text');

                submitBtn.disabled = true;
                submitText.classList.add('hidden');
                loadingText.classList.remove('hidden');
            });
        </script>
    @endpush
@endsection
