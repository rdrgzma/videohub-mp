<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use MercadoPago\MercadoPagoConfig;

class MercadoPagoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('mercadopago', function ($app) {
            MercadoPagoConfig::setAccessToken(config('mercadopago.access_token'));
            MercadoPagoConfig::setRuntimeEnviroment(config('mercadopago.environment'));

            return new \MercadoPago\Client\Payment\PaymentClient();
        });
    }

    public function boot(): void
    {
        //
    }
}
