@extends('layouts.app')

@section('title', 'Главная')

@section('nav_home_active', 'active')

@section('content')
    <div class="p-4 p-lg-5 mb-4 rounded-3 bg-primary text-white shadow">
        <div class="row align-items-center g-4">
            <div class="col-12 col-lg-8">
                <h1 class="display-6 fw-bold">Курсы и практикумы</h1>
                <p class="lead mb-3 mb-lg-4 opacity-90">
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ab adipisci aliquam consectetur consequatur corporis doloribus eos iusto laudantium magnam nam, natus nesciunt, odio optio saepe sapiente sed ullam unde ut!
                </p>
                <div class="d-flex flex-column flex-sm-row gap-2">
                    <a class="btn btn-light btn-lg" href="{{ route('register.form') }}">Попробовать бесплатно</a>
                    <a class="btn btn-outline-light btn-lg" href="{{ route('static.info') }}">Как устроен сервис</a>
                </div>
            </div>
        </div>
    </div>

    <h2 class="h4 mb-3">Направления</h2>
    <div class="row g-3 g-md-4">
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h3 class="h5 card-title">Бэкенд</h3>
                    <p class="card-text small text-body-secondary">
                        PHP, фреймворки, API
                    </p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h3 class="h5 card-title">Фронтенд</h3>
                    <p class="card-text small text-body-secondary">
                        Семантика, адаптив
                    </p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h3 class="h5 card-title">Инструменты</h3>
                    <p class="card-text small text-body-secondary">
                        Окружение, сборщики, стили и дебаг
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
