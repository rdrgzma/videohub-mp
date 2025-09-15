<?php
namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
// REMOVER o __construct() com middleware

public function checkout(Plan $plan)
{
// Verificar se usuário está logado
if (!Auth::check()) {
return redirect()->route('login');
}

// Verificar se usuário já tem plano ativo
if (Auth::user()->hasActivePlan()) {
return redirect()->route('profile.index')
->with('info', 'Você já possui um plano ativo.');
}

return view('subscriptions.checkout', compact('plan'));
}

public function process(Request $request, Plan $plan)
{
if (!Auth::check()) {
return redirect()->route('login');
}

$request->validate([
'payment_method' => 'required|in:credit_card,pix',
'card_name' => 'required_if:payment_method,credit_card|string|max:255',
'card_number' => 'required_if:payment_method,credit_card|string',
'card_expiry' => 'required_if:payment_method,credit_card|string',
'card_cvv' => 'required_if:payment_method,credit_card|string',
]);

try {
DB::beginTransaction();

// Simular processamento de pagamento
$paymentReference = 'PAY_' . time() . '_' . uniqid();
$paymentData = [];

if ($request->payment_method === 'credit_card') {
// Simular processamento de cartão
$paymentData = [
'card_last_four' => substr($request->card_number, -4),
'card_brand' => $this->detectCardBrand($request->card_number),
];
} elseif ($request->payment_method === 'pix') {
// Simular PIX
$paymentData = [
'pix_key' => 'pix@videohub.com.br',
'qr_code' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
];
}

// Criar assinatura
$subscription = Subscription::create([
'user_id' => Auth::id(),
'plan_id' => $plan->id,
'amount_paid' => $plan->price,
'payment_method' => $request->payment_method,
'status' => $request->payment_method === 'pix' ? 'pending' : 'active',
'starts_at' => now(),
'expires_at' => now()->addMonths($plan->duration_months),
'payment_reference' => $paymentReference,
'payment_data' => $paymentData,
]);

// Ativar assinatura se não for PIX
if ($request->payment_method === 'credit_card') {
$subscription->activate();
}

// Log da atividade
ActivityLog::log(
'subscription_created',
"Assinatura criada para o plano: {$plan->name}",
Auth::user(),
[
'plan_id' => $plan->id,
'subscription_id' => $subscription->id,
'payment_method' => $request->payment_method,
'amount' => $plan->price,
]
);

DB::commit();

if ($request->payment_method === 'pix') {
return view('subscriptions.pix-payment', compact('subscription', 'plan'));
}

return redirect()->route('subscription.success', $subscription);

} catch (\Exception $e) {
DB::rollBack();

return back()->with('error', 'Erro ao processar pagamento. Tente novamente.');
}
}

public function success(Subscription $subscription)
{
if (!Auth::check() || $subscription->user_id !== Auth::id()) {
abort(403);
}

return view('subscriptions.success', compact('subscription'));
}

public function confirmPix(Subscription $subscription)
{
if (!Auth::check() || $subscription->user_id !== Auth::id()) {
abort(403);
}

if ($subscription->status !== 'pending') {
return redirect()->route('profile.index')
->with('info', 'Esta assinatura já foi processada.');
}

// Simular confirmação do PIX
$subscription->activate();

ActivityLog::log(
'pix_payment_confirmed',
"Pagamento PIX confirmado para assinatura #{$subscription->id}",
Auth::user(),
['subscription_id' => $subscription->id]
);

return redirect()->route('subscription.success', $subscription)
->with('success', 'Pagamento PIX confirmado com sucesso!');
}

private function detectCardBrand(string $cardNumber): string
{
$number = preg_replace('/\D/', '', $cardNumber);

if (preg_match('/^4/', $number)) {
return 'Visa';
} elseif (preg_match('/^5[1-5]/', $number)) {
return 'Mastercard';
} elseif (preg_match('/^3[47]/', $number)) {
return 'American Express';
}

return 'Outros';
}
}
