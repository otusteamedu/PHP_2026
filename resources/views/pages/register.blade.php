@extends('layouts.app')

@section('title', 'Регистрация')

@section('nav_register_active', 'active')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-7 col-xl-6">
            <div class="card shadow">
                <div class="card-body p-4 p-md-5">
                    <h1 class="h3 mb-2">Создать аккаунт</h1>
                    <p class="text-body-secondary small mb-4">
                        Заполните поля и отправьте форму. Создание аккаунта в базе будет реализовано при подключении аутентификации.
                    </p>

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('register') }}" method="post" class="needs-validation" novalidate>
                        @csrf
                        <div class="row g-3">
                            <div class="col-12 col-sm-6">
                                <label class="form-label" for="regFirstName">Имя</label>
                                <input type="text" class="form-control" id="regFirstName" name="first_name"
                                       value="{{ old('first_name') }}"
                                       placeholder="Ерке" autocomplete="given-name" required>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label" for="regLastName">Фамилия</label>
                                <input type="text" class="form-control" id="regLastName" name="last_name"
                                       value="{{ old('last_name') }}"
                                       placeholder="Баксаисов" autocomplete="family-name" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="regEmail">Электронная почта</label>
                                <input type="email" class="form-control" id="regEmail" name="email"
                                       value="{{ old('email') }}"
                                       placeholder="baxaisov.erke@example.com" autocomplete="email" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="regPassword">Пароль</label>
                                <input type="password" class="form-control" id="regPassword" name="password"
                                       placeholder="Не менее 8 символов" autocomplete="new-password" required>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="regTerms"
                                           name="terms" @checked(old('terms'))>
                                    <label class="form-check-label" for="regTerms">
                                        Соглашаюсь с условиями
                                    </label>
                                </div>
                            </div>
                            <div class="col-12 d-grid d-sm-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-lg flex-sm-grow-0">
                                    Зарегистрироваться
                                </button>
                                <a class="btn btn-outline-secondary btn-lg" href="{{ route('home') }}">На главную</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <p class="text-center text-body-secondary small mt-3">
                Уже есть аккаунт? <a href="{{ route('user.profile') }}" class="link-primary">Откройте профиль</a>
            </p>
        </div>
    </div>
@endsection
