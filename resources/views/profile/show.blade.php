@extends('layouts.app')

@section('title', 'Профиль')

@section('nav_user_active', 'active')

@section('content')
    @php
        $initials = collect(preg_split('/\s+/', trim($user->name)))->filter()->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('');
    @endphp
    <div class="row g-4">
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body text-center text-lg-start">
                    <div class="ratio ratio-1x1 mx-auto mb-3 rounded-circle overflow-hidden bg-secondary bg-opacity-25"
                         style="max-width: 140px;">
                        <div class="d-flex align-items-center justify-content-center fs-2 text-secondary fw-semibold">
                            {{ mb_strtoupper($initials ?: '?') }}
                        </div>
                    </div>
                    <h1 class="h4 mb-1">{{ $user->name }}</h1>
                    <p class="text-body-secondary small mb-2">{{ $user->email }}</p>
                    @if ($user->roles->isNotEmpty())
                        <div class="d-flex flex-wrap gap-1 mb-3">
                            @foreach ($user->roles as $role)
                                <span class="badge text-bg-primary">{{ $role->name }}</span>
                            @endforeach
                        </div>
                    @else
                        <p class="small text-body-secondary mb-3">Роли не назначены</p>
                    @endif
                    <a class="btn btn-outline-primary btn-sm" href="{{ route('tasks.index') }}">Мои задачи</a>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm mb-3">
                <div class="card-header fw-semibold">О себе</div>
                <div class="card-body">
                    @if ($user->profile && ($user->profile->headline || $user->profile->bio))
                        @if ($user->profile->headline)
                            <p class="fw-semibold mb-2">{{ $user->profile->headline }}</p>
                        @endif
                        <p class="card-text mb-0">{{ $user->profile->bio ?: '—' }}</p>
                    @else
                        <p class="card-text text-body-secondary mb-0">Профиль пока не заполнен.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
