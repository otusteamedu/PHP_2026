@extends('layouts.app')

@section('title', 'Вход')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
            <h1 class="h4 mb-4">Вход</h1>
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger small">{{ $errors->first() }}</div>
                    @endif
                    <form method="post" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                   name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="password">Пароль</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password" required autocomplete="current-password">
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1">
                            <label class="form-check-label" for="remember">Запомнить</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mb-2">Войти</button>
                        <div class="text-center small">
                            <a href="{{ route('password.request') }}">Забыли пароль?</a>
                        </div>
                    </form>
                </div>
            </div>
            <p class="text-center small text-body-secondary mt-3 mb-0">
                Нет аккаунта? <a href="{{ route('register') }}">Регистрация</a>
            </p>
        </div>
    </div>
@endsection
