<?php

use App\Http\Controllers\Auth\CustomAuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

// Página inicial
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/plans', [HomeController::class, 'plans'])->name('plans');

// Autenticação customizada
Route::middleware('guest')->group(function () {
    Route::get('/login', [CustomAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [CustomAuthController::class, 'login']);
    Route::get('/register', [CustomAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [CustomAuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [CustomAuthController::class, 'logout'])->name('logout');
});

// Vídeos (middleware aplicado na rota, não no controller)
Route::middleware('auth')->group(function () {
    Route::get('/videos/{video:slug}', [VideoController::class, 'watch'])->name('videos.watch');
    Route::post('/videos/{video}/progress', [VideoController::class, 'updateProgress'])->name('videos.progress');
    Route::post('/videos/{video}/comments', [VideoController::class, 'addComment'])->name('videos.comments.store');
    Route::delete('/videos/{video}/comments/{comment}', [VideoController::class, 'deleteComment'])->name('videos.comments.destroy');
});

// Assinaturas (middleware aplicado na rota)
Route::middleware('auth')->group(function () {
    Route::get('/checkout/{plan:slug}', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');
    Route::post('/checkout/{plan:slug}', [SubscriptionController::class, 'process'])->name('subscription.process');
    Route::get('/subscription/{subscription}/success', [SubscriptionController::class, 'success'])->name('subscription.success');
    Route::get('/subscription/{subscription}/failure', [SubscriptionController::class, 'failure'])->name('subscription.failure');
    Route::post('/subscription/{subscription}/confirm-pix', [SubscriptionController::class, 'confirmPix'])->name('subscription.confirm-pix');
});

// Perfil do usuário (middleware aplicado na rota)
Route::middleware('auth')->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');
    Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
    Route::patch('/update', [ProfileController::class, 'update'])->name('update');
    Route::patch('/password', [ProfileController::class, 'updatePassword'])->name('password');
    Route::delete('/delete', [ProfileController::class, 'destroy'])->name('destroy');
});

// Webhooks (sem middleware de autenticação)
Route::prefix('webhooks')->name('webhooks.')->group(function () {
    Route::post('/mercadopago', [WebhookController::class, 'mercadoPago'])->name('mercadopago');
});
