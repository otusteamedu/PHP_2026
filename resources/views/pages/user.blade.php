@extends('layouts.app')

@section('title', 'Профиль пользователя')

@section('nav_user_active', 'active')

@section('content')
    <div class="row g-4">
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body text-center text-lg-start">
                    <div class="ratio ratio-1x1 mx-auto mb-3 rounded-circle overflow-hidden bg-secondary bg-opacity-25"
                         style="max-width: 140px;">
                        <div class="d-flex align-items-center justify-content-center display-5 text-secondary">ЕБ</div>
                    </div>
                    <h1 class="h4 mb-1">Ерке Баксаисов</h1>
                    <p class="text-body-secondary small mb-3">Студент</p>
                    <span class="badge text-bg-primary">Активный аккаунт</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm mb-3">
                <div class="card-header fw-semibold">О себе</div>
                <div class="card-body">
                    <p class="card-text mb-0">
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Commodi delectus eaque facilis ipsum itaque non placeat quae quia ratione ullam, unde vel, voluptate voluptates! Aperiam atque distinctio facere magni veniam!
                    </p>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-header fw-semibold">Курсы</div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex flex-column flex-sm-row justify-content-between gap-2">
                        <span>Курс 1</span>
                        <span class="badge text-bg-secondary align-self-sm-center">В процессе</span>
                    </li>
                    <li class="list-group-item d-flex flex-column flex-sm-row justify-content-between gap-2">
                        <span>Курс 2</span>
                        <span class="badge text-bg-success align-self-sm-center">Завершён</span>
                    </li>
                    <li class="list-group-item d-flex flex-column flex-sm-row justify-content-between gap-2">
                        <span>Курс 3</span>
                        <span class="badge text-bg-warning text-dark align-self-sm-center">Скоро</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection
