<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /**
     * Принять данные формы регистрации. Полноценное создание пользователя будет в следующих заданиях.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            'terms' => ['accepted'],
        ]);

        return redirect()
            ->route('register.form')
            ->with('status', 'Данные приняты. Сохранение аккаунта будет добавлено при внедрении аутентификации.');
    }
}
