<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Try admin login first
        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended('/admin');
        }

        // Try user login
        if (Auth::guard('web')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended('/app/campaigns');
        }

        return back()->withErrors([
            'email' => __('auth.failed'),
        ])->onlyInput('email');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $guard = Auth::check() ? 'web' : 'admin';
        Auth::guard($guard)->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
