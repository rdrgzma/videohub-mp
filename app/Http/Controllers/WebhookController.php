<?php

namespace App\Http\Controllers;

use App\Services\MercadoPagoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    private MercadoPagoService $mercadoPagoService;

    public function __construct(MercadoPagoService $mercadoPagoService)
    {
        $this->mercadoPagoService = $mercadoPagoService;
    }

    /**
     * Handle MercadoPago webhook notifications
     */
    public function mercadoPago(Request $request)
    {
        try {
            // Log do webhook recebido
            Log::info('MercadoPago Webhook Received', [
                'headers' => $request->headers->all(),
                'body' => $request->all()
            ]);

            // Validar assinatura do webhook (se configurada)
            $signature = $request->header('x-signature');
            if ($signature && !$this->mercadoPagoService->validateWebhookSignature(
                    $request->getContent(),
                    $signature
                )) {
                Log::warning('Invalid webhook signature from MercadoPago');
                return response('Unauthorized', 401);
            }

            // Processar apenas notificações de pagamento
            if ($request->type !== 'payment') {
                Log::info('Ignoring non-payment webhook', ['type' => $request->type]);
                return response('OK', 200);
            }

            // Processar webhook
            $result = $this->mercadoPagoService->processWebhook($request->all());

            if ($result['success']) {
                Log::info('Webhook processed successfully', [
                    'subscription_id' => $result['subscription']->id ?? null
                ]);
                return response('OK', 200);
            } else {
                Log::error('Webhook processing failed', ['error' => $result['error'] ?? 'Unknown error']);
                return response('Error', 500);
            }

        } catch (\Exception $e) {
            Log::error('Webhook Exception: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response('Error', 500);
        }
    }
}
