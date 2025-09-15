<?php

namespace App\Observers;

use App\Models\Plan;
use MercadoPago\Client\Plan\PlanClient;
use MercadoPago\MercadoPagoConfig;

class PlanObserver
{
    public function creating(Plan $plan)
    {
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));

        $client = new PlanClient();
        $request = [
            "reason" => $plan->name,
            "auto_recurring" => [
                "frequency" => 1,
                "frequency_type" => "months",
                "transaction_amount" => $plan->price,
                "currency_id" => "BRL"
            ],
            "back_url" => route('home') // URL genérica
        ];

        $response = $client->create($request);
        $plan->mercadopago_plan_id = $response->id;
    }

    // Você também pode implementar o método updating() para atualizar o plano no MP
}
