<?php
namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Plano Mensal',
                'slug' => 'mensal',
                'description' => 'Acesso completo por 1 mês',
                'price' => 29.90,
                'billing_cycle' => 'monthly',
                'duration_months' => 1,
                'features' => [
                    'Acesso completo',
                    'Suporte 24/7',
                    'Certificados',
                ],
                'is_popular' => false,
                'sort_order' => 1,
            ],
            [
                'name' => 'Plano Trimestral',
                'slug' => 'trimestral',
                'description' => 'Acesso completo por 3 meses com desconto',
                'price' => 69.90,
                'billing_cycle' => 'quarterly',
                'duration_months' => 3,
                'features' => [
                    'Acesso completo',
                    'Suporte prioritário',
                    'Certificados',
                    'Material extra',
                ],
                'is_popular' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Plano Anual',
                'slug' => 'anual',
                'description' => 'Acesso completo por 1 ano com maior desconto',
                'price' => 199.90,
                'billing_cycle' => 'yearly',
                'duration_months' => 12,
                'features' => [
                    'Acesso vitalício',
                    'Suporte VIP',
                    'Todos certificados',
                    'Mentoria 1:1',
                ],
                'is_popular' => false,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::create($plan);
        }
    }
}

