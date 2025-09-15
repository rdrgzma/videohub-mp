<?php

namespace App\Console\Commands;

use App\Services\MercadoPagoService;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Console\Command;

class TestMercadoPago extends Command
{
    protected $signature = 'mercadopago:test';
    protected $description = 'Testa a integração com Mercado Pago';

    public function handle(MercadoPagoService $mercadoPagoService)
    {
        $this->info('Testando integração Mercado Pago...');

        // Buscar um plano e usuário para teste
        $plan = Plan::first();
        $user = User::first();

        if (!$plan || !$user) {
            $this->error('Necessário ter pelo menos um plano e um usuário cadastrados.');
            return 1;
        }

        $this->info("Testando com Plano: {$plan->name} (R$ {$plan->price})");
        $this->info("Usuário: {$user->name} ({$user->email})");

        // Teste PIX
        $this->info("\n--- Testando PIX ---");
        $pixResult = $mercadoPagoService->processPixPayment($plan, $user);

        if ($pixResult['success']) {
            $this->info("✅ PIX criado com sucesso!");
            $this->info("Payment ID: {$pixResult['payment_id']}");
            $this->info("Status: {$pixResult['status']}");

            if (isset($pixResult['qr_code'])) {
                $this->info("QR Code: " . substr($pixResult['qr_code'], 0, 50) . '...');
            }

            // Buscar detalhes do pagamento
            $this->info("\n--- Buscando detalhes do pagamento ---");
            $payment = $mercadoPagoService->getPayment($pixResult['payment_id']);

            if ($payment) {
                $this->info("✅ Pagamento encontrado!");
                $this->info("ID: {$payment->id}");
                $this->info("Status: {$payment->status}");
                $this->info("Valor: R$ {$payment->transaction_amount}");
            } else {
                $this->error("❌ Erro ao buscar pagamento");
            }

        } else {
            $this->error("❌ Erro no PIX: {$pixResult['error']}");
        }

        $this->info("\n--- Teste concluído ---");
        $this->warn("Lembre-se de configurar as credenciais corretas no .env");

        return 0;
    }
}
