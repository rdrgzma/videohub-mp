<?php
namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    // REMOVER o __construct() com middleware

    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $user->load(['currentPlan', 'subscriptions' => function ($query) {
            $query->orderBy('created_at', 'desc')->limit(5);
        }]);

        // Estatísticas do usuário
        $stats = [
            'total_logins' => ActivityLog::where('user_id', $user->id)
                ->where('action', 'login')
                ->count(),
            'videos_watched' => $user->videoViews()->count(),
            'watch_time' => $user->videoViews()->sum('watch_time'),
            'comments_made' => $user->comments()->count(),
            'days_as_member' => $user->created_at->diffInDays(now()),
        ];

        // Últimas atividades
        $recentActivities = ActivityLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('profile.index', compact('user', 'stats', 'recentActivities'));
    }

    public function edit()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        return view('profile.edit', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Upload do avatar
        if ($request->hasFile('avatar')) {
            // Deletar avatar anterior se existir
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;
        }

        $user->update($validated);

        // Log da atividade
        ActivityLog::log(
            'profile_updated',
            'Perfil atualizado',
            $user,
            ['fields_updated' => array_keys($validated)]
        );

        return redirect()->route('profile.index')
            ->with('success', 'Perfil atualizado com sucesso!');
    }

    public function updatePassword(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Senha atual incorreta.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        ActivityLog::log(
            'password_updated',
            'Senha alterada',
            $user
        );

        return redirect()->route('profile.index')
            ->with('success', 'Senha alterada com sucesso!');
    }

    public function destroy()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Log da atividade antes de deletar
        ActivityLog::log(
            'account_deleted',
            'Conta deletada pelo usuário',
            $user
        );

        // Deletar avatar se existir
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        Auth::logout();
        $user->delete();

        return redirect()->route('home')
            ->with('success', 'Conta deletada com sucesso.');
    }
}

