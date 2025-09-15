<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\ActivityLog;
use App\Services\MercadoPagoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    private MercadoPagoService $mercadoPagoService;

    public function __construct(MercadoPagoService $mercadoPagoService)
    {
        $this->mercadoPagoService = $mercadoPagoService;
    }

    public function checkout(Plan $plan)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->hasActivePlan()) {
            return redirect()->route('profile.index')
                ->with('info', 'Você já possui um plano ativo.');
        }

        return view('subscriptions.checkout_bkp', compact('plan'));
    }

    public function process(Request $request, Plan $plan)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $validationRules = [
            'payment_method' => 'required|in:credit_card,pix',
        ];

        // Validações específicas para cartão de crédito
        if ($request->payment_method === 'credit_card') {
            $validationRules = array_merge($validationRules, [
                'card_token' => 'required|string',
                'cpf' => 'required|string|size:11',
                'card_holder_name' => 'required|string|max:255',
            ]);
        }

        $request->validate($validationRules);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $externalReference = "VIDEOHUB_P{$plan->id}_U{$user->id}_" . uniqid();

            // Criar assinatura pendente
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'amount_paid' => $plan->price,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'starts_at' => now(),
                'expires_at' => now()->addMonths($plan->duration_months),
                'payment_reference' => $externalReference,
                'payment_data' => [],
            ]);

            // Processar pagamento no Mercado Pago
            if ($request->payment_method === 'credit_card') {
                $result = $this->mercadoPagoService->processCreditCardPayment([
                    'token' => $request->card_token,
                    'cpf' => $request->cpf,
                    'card_holder_name' => $request->card_holder_name,
                ], $plan, $user);
            } else {
                $result = $this->mercadoPagoService->processPixPayment($plan, $user);
            }

            if (!$result['success']) {
                DB::rollBack();
                return back()->with('error', 'Erro ao processar pagamento: ' . $result['error']);
            }

            // Atualizar assinatura com dados do pagamento
            $subscription->update([
                'payment_data' => [
                    'mp_payment_id' => $result['payment_id'],
                    'mp_status' => $result['status'],
                    'payment_method' => $request->payment_method,
                    'qr_code' => $result['qr_code'] ?? null,
                    'qr_code_base64' => $result['qr_code_base64'] ?? null,
                ]
            ]);

            // Se cartão foi aprovado imediatamente, ativar assinatura
            if ($request->payment_method === 'credit_card' && $result['status'] === 'approved') {
                $subscription->activate();
                $subscription->update(['status' => 'active']);
            }

            // Log da atividade
            ActivityLog::log(
                'subscription_created',
                "Assinatura criada para o plano: {$plan->name}",
                $user,
                [
                    'plan_id' => $plan->id,
                    'subscription_id' => $subscription->id,
                    'payment_method' => $request->payment_method,
                    'amount' => $plan->price,
                    'mp_payment_id' => $result['payment_id'],
                    'mp_status' => $result['status'],
                ]
            );

            DB::commit();

            // Redirecionar baseado no método de pagamento
            if ($request->payment_method === 'pix') {
                return view('subscriptions.pix-payment', compact('subscription', 'plan'));
            }

            return redirect()->route('subscription.success', $subscription);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Subscription Process Error: ' . $e->getMessage(), [
                'plan_id' => $plan->id,
                'user_id' => Auth::id(),
                'payment_method' => $request->payment_method,
            ]);

            return back()->with('error', 'Erro interno. Tente novamente em alguns minutos.');
        }
    }

    public function success(Subscription $subscription)
    {
        if (!Auth::check() || $subscription->user_id !== Auth::id()) {
            abort(403);
        }

        return view('subscriptions.success', compact('subscription'));
    }

    public function failure(Subscription $subscription)
    {
        if (!Auth::check() || $subscription->user_id !== Auth::id()) {
            abort(403);
        }

        return view('subscriptions.failure', compact('subscription'));
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

        // Verificar status no Mercado Pago
        $paymentData = $subscription->payment_data;
        if (!isset($paymentData['mp_payment_id'])) {
            return back()->with('error', 'Dados de pagamento não encontrados.');
        }

        $payment = $this->mercadoPagoService->getPayment($paymentData['mp_payment_id']);

        if (!$payment) {
            return back()->with('error', 'Pagamento não encontrado.');
        }

        if ($payment->status === 'approved') {
            $subscription->activate();
            $subscription->update(['status' => 'active']);

            ActivityLog::log(
                'pix_payment_confirmed',
                "Pagamento PIX confirmado para assinatura #{$subscription->id}",
                Auth::user(),
                ['subscription_id' => $subscription->id, 'mp_payment_id' => $payment->id]
            );

            return redirect()->route('subscription.success', $subscription)
                ->with('success', 'Pagamento PIX confirmado com sucesso!');
        }

        return back()->with('info', 'Pagamento ainda não foi confirmado. Aguarde alguns minutos.');
    }
}
