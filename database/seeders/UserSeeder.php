<?php
namespace Database\Seeders;

use App\Models\User;
use App\Models\Plan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        $admin = User::create([
            'name' => 'Admin VideoHub',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
            'is_admin' => true,
            'is_active' => true,
            'bio' => 'Administrador da plataforma VideoHub',
        ]);

        // Demo user with subscription
        $trimestralPlan = Plan::where('slug', 'trimestral')->first();
        $demoUser = User::create([
            'name' => 'Usuário de demostração',
            'email' => 'user@user.com',
            'password' => Hash::make('user123'),
            'email_verified_at' => now(),
            'current_plan_id' => $trimestralPlan->id,
            'plan_started_at' => now(),
            'plan_expires_at' => now()->addMonths(3),
            'is_admin' => false,
            'is_active' => true,
            'bio' => 'Usuário de demonstração da plataforma',
        ]);

        // Create subscription for demo user
        $demoUser->subscriptions()->create([
            'plan_id' => $trimestralPlan->id,
            'amount_paid' => $trimestralPlan->price,
            'payment_method' => 'credit_card',
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => now()->addMonths(3),
            'payment_reference' => 'demo_payment_' . uniqid(),
        ]);

        // Free user
        User::create([
            'name' => 'Usuário Gratuito',
            'email' => 'free@user.com',
            'password' => Hash::make('free123'),
            'email_verified_at' => now(),
            'is_admin' => false,
            'is_active' => true,
            'bio' => 'Usuário com plano gratuito',
        ]);
    }
}
