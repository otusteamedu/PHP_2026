@extends('layouts.app')

@section('title', 'Регистрация')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <h1 class="h4 mb-4">Регистрация</h1>
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger small">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="post" action="{{ route('register') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="name">Имя</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}"
                                   required autocomplete="name">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}"
                                   required autocomplete="username">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="password">Пароль</label>
                            <input type="password" class="form-control" id="password" name="password" required
                                   autocomplete="new-password">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="password_confirmation">Повтор пароля</label>
                            <input type="password" class="form-control" id="password_confirmation"
                                   name="password_confirmation" required autocomplete="new-password">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Создать аккаунт</button>
                    </form>
                </div>
            </div>
            <p class="text-center small text-body-secondary mt-3 mb-0">
                Уже есть аккаунт? <a href="{{ route('login') }}">Войти</a>
            </p>
        </div>
    </div>
@endsection
