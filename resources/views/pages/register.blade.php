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
                        Прототип формы: поля для демонстрации интерфейса, отправка на сервер не выполняется.
                    </p>

                    <form action="#" method="get" class="needs-validation" novalidate>
                        <div class="row g-3">
                            <div class="col-12 col-sm-6">
                                <label class="form-label" for="regFirstName">Имя</label>
                                <input type="text" class="form-control" id="regFirstName" name="first_name"
                                       placeholder="Ерке" autocomplete="given-name">
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label" for="regLastName">Фамилия</label>
                                <input type="text" class="form-control" id="regLastName" name="last_name"
                                       placeholder="Баксаисов" autocomplete="family-name">
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="regEmail">Электронная почта</label>
                                <input type="email" class="form-control" id="regEmail" name="email"
                                       placeholder="baxaisov.erke@example.com" autocomplete="email">
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="regPassword">Пароль</label>
                                <input type="password" class="form-control" id="regPassword" name="password"
                                       placeholder="Не менее 8 символов" autocomplete="new-password">
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="regTerms">
                                    <label class="form-check-label" for="regTerms">
                                        Соглашаюсь с условиями
                                    </label>
                                </div>
                            </div>
                            <div class="col-12 d-grid d-sm-flex gap-2">
                                <button type="button" class="btn btn-primary btn-lg flex-sm-grow-0">
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
