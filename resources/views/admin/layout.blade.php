<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Админка')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body class="bg-light min-vh-100 d-flex flex-column">
    <nav class="navbar navbar-dark bg-dark shadow-sm">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1 fs-5">Панель</span>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <a class="btn btn-sm btn-outline-light" href="{{ route('admin.pages.index') }}">Страницы</a>
                <a class="btn btn-sm btn-outline-light" href="{{ route('admin.courses.index') }}">Курсы</a>
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('home') }}" target="_blank">Сайт</a>
                <form action="{{ route('logout') }}" method="post" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger">Выйти</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="flex-grow-1 py-4">
        <div class="container">
            @include('admin.partials.flash')
            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>
</body>
</html>
