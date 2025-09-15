@extends('layouts.app')

@section('title', 'Pagamento PIX - VideoHub')

@section('content')
    <div class="max-w-2xl mx-auto px-4 py-8">
        <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-8 border border-white/10">
            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-gradient-to-r from-green-500 to-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-qrcode text-white text-3xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">Pagamento PIX</h1>
                <p class="text-green-200">Escaneie o QR Code ou copie o código PIX</p>
            </div>

            <!-- Resumo da Assinatura -->
            <div class="bg-green-600/20 rounded-lg p-6 mb-8 border border-green-500/30">
                <h3 class="text-lg font-bold text-white mb-4">Resumo da Assinatura</h3>
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-medium text-white">{{ $plan->name }}</h4>
                        <p class="text-green-200 text-sm">Assinatura #{{ $subscription->id }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-green-400">{{ $plan->formatted_price }}</div>
                        <div class="text-green-200 text-sm">{{ $plan->billing_cycle_text }}</div>
                    </div>
                </div>
            </div>

            <!-- QR Code -->
            @if($subscription->payment_data['qr_code_base64'] ?? false)
                <div class="text-center mb-6">
                    <div class="bg-white p-6 rounded-2xl inline-block mb-4">
                        <img
                            src="data:image/png;base64,{{ $subscription->payment_data['qr_code_base64'] }}"
                            alt="QR Code PIX"
                            class="w-64 h-64 mx-auto"
                        >
                    </div>
                    <p class="text-green-200 text-sm">Escaneie o QR Code com o app do seu banco</p>
                </div>
            @endif

            <!-- Código PIX -->
            @if($subscription->payment_data['qr_code'] ?? false)
                <div class="mb-6">
                    <label class="block text-green-200 text-sm font-medium mb-2">Código PIX (Copia e Cola)</label>
                    <div class="flex">
                        <input
                            type="text"
                            id="pixCode"
                            value="{{ $subscription->payment_data['qr_code'] }}"
                            readonly
                            class="flex-1 px-4 py-3 bg-white/10 border border-white/20 rounded-l-lg text-white text-sm font-mono"
                        >
                        <button
                            onclick="copyPixCode()"
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-r-lg transition-colors"
                        >
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    <p class="text-green-300 text-xs mt-1">Cole este código no seu app de pagamentos</p>
                </div>
            @endif

            <!-- Instruções -->
            <div class="bg-blue-600/20 rounded-lg p-6 mb-8 border border-blue-500/30">
                <h4 class="text-lg font-bold text-blue-300 mb-3">
                    <i class="fas fa-info-circle mr-2"></i>
                    Como pagar com PIX
                </h4>
                <ol class="text-blue-200 space-y-2 text-sm">
                    <li class="flex items-start">
                        <span class="bg-blue-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs mr-3 mt-0.5 flex-shrink-0">1</span>
                        Abra o app do seu banco ou carteira digital
                    </li>
                    <li class="flex items-start">
                        <span class="bg-blue-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs mr-3 mt-0.5 flex-shrink-0">2</span>
                        Escolha a opção PIX
                    </li>
                    <li class="flex items-start">
                        <span class="bg-blue-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs mr-3 mt-0.5 flex-shrink-0">3</span>
                        Escaneie o QR Code ou cole o código PIX
                    </li>
                    <li class="flex items-start">
                        <span class="bg-blue-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs mr-3 mt-0.5 flex-shrink-0">4</span>
                        Confirme o pagamento
                    </li>
                </ol>
            </div>

            <!-- Status -->
            <div class="bg-yellow-600/20 rounded-lg p-6 mb-8 border border-yellow-500/30">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-bold text-yellow-300">
                        <i class="fas fa-clock mr-2"></i>
                        Aguardando Pagamento
                    </h4>
                    <div id="statusIndicator" class="w-3 h-3 bg-yellow-500 rounded-full animate-pulse"></div>
                </div>
                <p class="text-yellow-200 text-sm mb-4">
                    Assim que o pagamento for confirmado, sua assinatura será ativada automaticamente.
                </p>
                <button
                    onclick="checkPaymentStatus()"
                    id="checkStatusBtn"
                    class="bg-yellow-600 hover:bg-yellow-700 text-white font-medium px-4 py-2 rounded-lg transition-colors text-sm"
                >
                    <i class="fas fa-sync mr-2"></i>
                    Verificar Status
                </button>
            </div>

            <!-- Botões de Ação -->
            <div class="flex space-x-3">
                <a
                    href="{{ route('profile.index') }}"
                    class="flex-1 border-2 border-white/20 hover:border-white/40 text-white font-medium py-3 rounded-lg transition-all duration-300 text-center"
                >
                    Ir para Perfil
                </a>
                <a
                    href="{{ route('home') }}"
                    class="flex-1 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-bold py-3 rounded-lg transition-all duration-300 text-center"
                >
                    Voltar ao Início
                </a>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function copyPixCode() {
                const pixCode = document.getElementById('pixCode');
                pixCode.select();
                pixCode.setSelectionRange(0, 99999);

                navigator.clipboard.writeText(pixCode.value).then(function() {
                    // Feedback visual
                    const button = event.target.closest('button');
                    const originalHtml = button.innerHTML;
                    button.innerHTML = '<i class="fas fa-check"></i>';
                    button.classList.add('bg-green-700');
                    button.classList.remove('bg-green-600');

                    setTimeout(() => {
                        button.innerHTML = originalHtml;
                        button.classList.remove('bg-green-700');
                        button.classList.add('bg-green-600');
                    }, 2000);
                });
            }

            function checkPaymentStatus() {
                const btn = document.getElementById('checkStatusBtn');
                const originalHtml = btn.innerHTML;

                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Verificando...';
                btn.disabled = true;

                fetch(`{{ route('subscription.confirm-pix', $subscription) }}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            // Resetar botão
                            btn.innerHTML = originalHtml;
                            btn.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        btn.innerHTML = originalHtml;
                        btn.disabled = false;
                    });
            }

            // Auto-verificar status a cada 30 segundos
            setInterval(checkPaymentStatus, 30000);

            // Verificar status ao carregar a página
            setTimeout(checkPaymentStatus, 5000);
        </script>
    @endpush
@endsection
