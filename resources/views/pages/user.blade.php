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
                        <div class="d-flex align-items-center justify-content-center display-5 text-secondary">{{ $initials }}</div>
                    </div>
                    <h1 class="h4 mb-1">{{ $fullName }}</h1>
                    <p class="text-body-secondary small mb-3">{{ $roleLabel }}</p>
                    <span class="badge text-bg-primary">Активный аккаунт</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm mb-3">
                <div class="card-header fw-semibold">О себе</div>
                <div class="card-body">
                    <p class="card-text mb-0">
                        {{ $bio }}
                    </p>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-header fw-semibold">Курсы</div>
                <ul class="list-group list-group-flush">
                    @foreach ($courses as $course)
                        <li class="list-group-item d-flex flex-column flex-sm-row justify-content-between gap-2">
                            <span>{{ $course['title'] }}</span>
                            <span class="badge {{ $course['badgeClass'] }} align-self-sm-center">{{ $course['status'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endsection
