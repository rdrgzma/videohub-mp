<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSubscription
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Admins sempre têm acesso
        if ($user->is_admin) {
            return $next($request);
        }

        // Verificar se tem plano ativo
        if (!$user->hasActivePlan()) {
            return redirect()->route('plans')
                ->with('error', 'Você precisa de um plano ativo para acessar este conteúdo.');
        }

        return $next($request);
    }
}

