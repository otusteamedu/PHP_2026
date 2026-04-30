@extends('layouts.app')

@section('title', 'Восстановление пароля')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
            <h1 class="h4 mb-4">Восстановление пароля</h1>
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger small">{{ $errors->first() }}</div>
                    @endif
                    <p class="small text-body-secondary">
                        Укажите email — если аккаунт существует, придёт ссылка для сброса пароля.
                    </p>
                    <form method="post" action="{{ route('password.email') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="{{ old('email') }}" required autofocus autocomplete="username">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Отправить ссылку</button>
                    </form>
                </div>
            </div>
            <p class="text-center small text-body-secondary mt-3 mb-0">
                <a href="{{ route('login') }}">Назад ко входу</a>
            </p>
        </div>
    </div>
@endsection
