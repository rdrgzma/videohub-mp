<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\User;
use App\Models\Subscription;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Resources\Payment;
use MercadoPago\Exceptions\MPApiException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MercadoPagoService
{
    private PaymentClient $paymentClient;
    private PreferenceClient $preferenceClient;

    public function __construct()
    {
        MercadoPagoConfig::setAccessToken(env('MERCADO_PAGO_ACCESS_TOKEN'));
        MercadoPagoConfig::setRuntimeEnviroment('LOCAL');

        $this->paymentClient = new PaymentClient();
        $this->preferenceClient = new PreferenceClient();
    }

    /**
     * Processa pagamento com cartão de crédito
     */
    public function processCreditCardPayment(array $paymentData, Plan $plan, User $user): array
    {
        try {
            $payment = $this->paymentClient->create([
                "transaction_amount" => (float) $plan->price,
                "payment_method_id" => "visa", // ou detectar automaticamente
                "payer" => [
                    "email" => $user->email,
                    "identification" => [
                        "type" => "CPF",
                        "number" => $paymentData['cpf'] ?? "11111111111"
                    ]
                ],
                "token" => $paymentData['token'], // Token do cartão gerado no frontend
                "description" => "Assinatura {$plan->name} - VideoHub",
                "external_reference" => $this->generateExternalReference($plan, $user),
                "notification_url" => config('mercadopago.notification_url'),
                "statement_descriptor" => config('mercadopago.statement_descriptor'),
            ]);

            return [
                'success' => true,
                'payment_id' => $payment->id,
                'status' => $payment->status,
                'payment' => $payment
            ];

        } catch (MPApiException $e) {
            Log::error('MercadoPago Credit Card Error: ' . $e->getMessage(), [
                'plan_id' => $plan->id,
                'user_id' => $user->id,
                'error_details' => $e->getApiResponse()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => $e->getStatusCode()
            ];
        }
    }

    /**
     * Processa pagamento PIX
     */
    public function processPixPayment(Plan $plan, User $user): array
    {
        try {
            $payment = $this->paymentClient->create([
                "transaction_amount" => (float) $plan->price,
                "payment_method_id" => "pix",
                "payer" => [
                    "email" => $user->email,
                    "first_name" => explode(' ', $user->name)[0],
                    "last_name" => implode(' ', array_slice(explode(' ', $user->name), 1)) ?: explode(' ', $user->name)[0],
                    "identification" => [
                        "type" => "CPF",
                        "number" => "11111111111" // Implementar campo CPF no User
                    ]
                ],
                "description" => "Assinatura {$plan->name} - VideoHub",
                "external_reference" => $this->generateExternalReference($plan, $user),
                "notification_url" => config('mercadopago.notification_url'),
            ]);

            return [
                'success' => true,
                'payment_id' => $payment->id,
                'status' => $payment->status,
                'qr_code' => $payment->point_of_interaction->transaction_data->qr_code ?? null,
                'qr_code_base64' => $payment->point_of_interaction->transaction_data->qr_code_base64 ?? null,
                'payment' => $payment
            ];

        } catch (MPApiException $e) {
            Log::error('MercadoPago PIX Error: ' . $e->getMessage(), [
                'plan_id' => $plan->id,
                'user_id' => $user->id,
                'error_details' => $e->getApiResponse()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => $e->getStatusCode()
            ];
        }
    }

    /**
     * Busca informações de um pagamento
     */
    public function getPayment(string $paymentId): ?Payment
    {
        try {
            return $this->paymentClient->get($paymentId);
        } catch (MPApiException $e) {
            Log::error('MercadoPago Get Payment Error: ' . $e->getMessage(), [
                'payment_id' => $paymentId
            ]);
            return null;
        }
    }

    /**
     * Processa webhook de notificação
     */
    public function processWebhook(array $data): array
    {
        try {
            if (!isset($data['data']['id'])) {
                throw new \Exception('Invalid webhook data: missing payment id');
            }

            $paymentId = $data['data']['id'];
            $payment = $this->getPayment($paymentId);

            if (!$payment) {
                throw new \Exception("Payment not found: {$paymentId}");
            }

            // Encontrar assinatura pela external_reference
            $subscription = Subscription::where('payment_reference', $payment->external_reference)->first();

            if (!$subscription) {
                Log::warning("Subscription not found for external_reference: {$payment->external_reference}");
                return ['success' => false, 'message' => 'Subscription not found'];
            }

            // Atualizar status da assinatura baseado no pagamento
            $this->updateSubscriptionFromPayment($subscription, $payment);

            return ['success' => true, 'subscription' => $subscription];

        } catch (\Exception $e) {
            Log::error('MercadoPago Webhook Error: ' . $e->getMessage(), $data);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Atualiza assinatura baseado no status do pagamento
     */
    private function updateSubscriptionFromPayment(Subscription $subscription, Payment $payment): void
    {
        $paymentData = [
            'mp_payment_id' => $payment->id,
            'mp_status' => $payment->status,
            'mp_status_detail' => $payment->status_detail,
            'payment_method' => $payment->payment_method_id,
            'updated_at' => now()
        ];

        switch ($payment->status) {
            case 'approved':
                $subscription->update([
                    'status' => 'active',
                    'payment_data' => array_merge($subscription->payment_data ?? [], $paymentData)
                ]);
                $subscription->activate();
                break;

            case 'rejected':
                $subscription->update([
                    'status' => 'cancelled',
                    'payment_data' => array_merge($subscription->payment_data ?? [], $paymentData)
                ]);
                break;

            case 'pending':
                $subscription->update([
                    'status' => 'pending',
                    'payment_data' => array_merge($subscription->payment_data ?? [], $paymentData)
                ]);
                break;

            default:
                Log::info("Unknown payment status: {$payment->status} for payment {$payment->id}");
        }
    }

    /**
     * Gera referência externa única
     */
    private function generateExternalReference(Plan $plan, User $user): string
    {
        return "VIDEOHUB_P{$plan->id}_U{$user->id}_" . Str::random(8);
    }

    /**
     * Valida assinatura do webhook
     */
    public function validateWebhookSignature(string $payload, string $signature): bool
    {
        $secret = config('mercadopago.webhook_secret');
        if (!$secret) {
            return true; // Se não configurado, aceita
        }

        $expectedSignature = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expectedSignature, $signature);
    }
}
