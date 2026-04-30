<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    @stack('head')
</head>
<body class="d-flex flex-column min-vh-100 bg-body-tertiary">
    <nav class="navbar navbar-expand-md navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="{{ route('home') }}">Сайт</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                    aria-controls="mainNav" aria-expanded="false" aria-label="Меню">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto mb-2 mb-md-0 gap-md-1">
                    <li class="nav-item">
                        <a class="nav-link @yield('nav_home_active')" href="{{ route('home') }}">Главная</a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link @yield('nav_tasks_active')" href="{{ route('tasks.index') }}">Задачи</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @yield('nav_user_active')" href="{{ route('user.profile') }}">Профиль</a>
                        </li>
                        @can('access-admin')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.pages.index') }}">Админка</a>
                            </li>
                        @endcan
                        <li class="nav-item">
                            <form method="post" action="{{ route('logout') }}" class="d-inline-flex h-100 align-items-center">
                                @csrf
                                <button type="submit" class="btn btn-link nav-link text-white text-decoration-none border-0 py-2">
                                    Выйти
                                </button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Вход</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Регистрация</a>
                        </li>
                    @endauth
                    <li class="nav-item">
                        <a class="nav-link @yield('nav_static_active')" href="{{ route('static.info') }}">О проекте</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="flex-grow-1 py-4 py-md-5">
        <div class="container">
            @include('partials.flash')
            @yield('content')
        </div>
    </main>

    <footer class="border-top bg-white py-4 mt-auto">
        <div class="container small text-body-secondary text-center text-md-start">
            <div class="row row-cols-1 row-cols-md-2 gy-2 align-items-center">
                <div class="col">Подвал сайта</div>
                <div class="col text-md-end">
                    @guest
                        <a href="{{ route('login') }}" class="link-secondary">Вход для администраторов</a>
                    @else
                        @can('access-admin')
                            <a href="{{ route('admin.pages.index') }}" class="link-secondary">Панель управления</a>
                        @endcan
                    @endguest
                    · © {{ date('Y') }}
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>
    @stack('scripts')
</body>
</html>
