<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $login = trim((string) $credentials['login']);
        $password = (string) $credentials['password'];
        $remember = $request->boolean('remember');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = User::query()
            ->where($field, $login)
            ->first();

        if (! $user || ! $user->is_active) {
            return back()
                ->withInput($request->only('login', 'remember'))
                ->withErrors([
                    'login' => 'Akun tidak ditemukan atau sedang nonaktif.',
                ]);
        }

        if (! Auth::attempt([$field => $login, 'password' => $password], $remember)) {
            return back()
                ->withInput($request->only('login', 'remember'))
                ->withErrors([
                    'login' => 'Username atau password belum sesuai.',
                ]);
        }

        $request->session()->regenerate();

        $user->forceFill([
            'last_login_at' => now(),
        ])->save();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('status', 'Anda berhasil logout dari panel admin.');
    }
}
