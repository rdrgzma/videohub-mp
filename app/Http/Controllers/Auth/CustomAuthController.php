<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class CustomAuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();
            $user->updateLastActivity();

            ActivityLog::log(
                'login',
                'Login realizado com sucesso',
                $user,
                ['ip' => $request->ip(), 'user_agent' => $request->userAgent()]
            );

            return redirect()->intended(route('home'))
                ->with('success', 'Bem-vindo de volta!');
        }

        // Log de tentativa de login falhada
        ActivityLog::log(
            'failed_login',
            'Tentativa de login falhada',
            null,
            ['email' => $request->email, 'ip' => $request->ip()]
        );

        return back()->withErrors([
            'email' => 'As credenciais fornecidas não conferem com nossos registros.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(), // Auto-verificação para simplificar
        ]);

        Auth::login($user);

        ActivityLog::log(
            'register',
            'Conta criada com sucesso',
            $user,
            ['ip' => $request->ip(), 'user_agent' => $request->userAgent()]
        );

        return redirect()->route('home')
            ->with('success', 'Conta criada com sucesso! Bem-vindo à VideoHub!');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        ActivityLog::log(
            'logout',
            'Logout realizado',
            $user
        );

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'Logout realizado com sucesso.');
    }
}


