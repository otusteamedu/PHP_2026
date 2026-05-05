<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Вход — админка</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body class="bg-secondary-subtle min-vh-100 d-flex align-items-center py-4">
<div class="container" style="max-width: 420px;">
    <h1 class="h4 mb-4 text-center">Вход для администратора</h1>
    <div class="card shadow">
        <div class="card-body p-4">
            @if ($errors->any())
                <div class="alert alert-danger small">{{ $errors->first() }}</div>
            @endif
            <form method="post" action="{{ route('admin.login.attempt') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">Пароль</label>
                    <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password">
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1">
                    <label class="form-check-label" for="remember">Запомнить</label>
                </div>
                <button type="submit" class="btn btn-primary w-100">Войти</button>
            </form>
        </div>
    </div>
    <p class="text-center small text-muted mt-3 mb-0"><a href="{{ route('home') }}">На главную</a></p>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>
</body>
</html>
