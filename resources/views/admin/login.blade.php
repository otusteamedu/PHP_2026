<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin · Login</title>
    <style>
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; margin: 0; background: #0b1220; color: #e5e7eb; }
        .wrap { max-width: 420px; margin: 0 auto; padding: 80px 16px; }
        .card { background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.08); border-radius: 16px; padding: 18px; }
        label { display:block; font-size: 14px; color: #cbd5e1; margin-bottom: 6px; }
        input { width: 100%; padding: 10px 12px; border-radius: 10px; border: 1px solid rgba(255,255,255,.14); background: rgba(0,0,0,.25); color: #e5e7eb; }
        button { margin-top: 12px; width: 100%; padding: 10px 12px; border-radius: 10px; border: 0; background: #2563eb; color: white; font-weight: 600; cursor: pointer; }
        .err { color: #fecaca; font-size: 13px; margin-top: 8px; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <h1 style="margin:0 0 12px;">Admin</h1>
        <form method="post" action="{{ route('admin.login.post') }}">
            @csrf
            <label for="password">Пароль</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required>
            @error('password')
                <div class="err">{{ $message }}</div>
            @enderror
            <button type="submit">Войти</button>
        </form>
    </div>
</div>
</body>
</html>

