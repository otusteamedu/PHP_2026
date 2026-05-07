<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function loginForm(): View
    {
        return view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $expected = (string) config('services.admin_web.password', '');
        if ($expected === '' || ! hash_equals($expected, (string) $request->input('password'))) {
            return back()->withErrors(['password' => 'Неверный пароль.']);
        }

        $request->session()->put('admin_web', true);
        $request->session()->regenerate();

        return redirect()->route('admin.constructs.index');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('admin_web');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
