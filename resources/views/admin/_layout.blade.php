<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin')</title>
    <style>
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; margin: 0; background: #0b1220; color: #e5e7eb; }
        a { color: #93c5fd; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .wrap { max-width: 1100px; margin: 0 auto; padding: 22px 16px; }
        .top { display:flex; align-items:center; justify-content:space-between; gap: 12px; margin-bottom: 14px; }
        .pill { display: inline-block; padding: 4px 10px; border-radius: 999px; background: rgba(255,255,255,.08); color: #cbd5e1; font-size: 12px; }
        .card { background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.08); border-radius: 16px; padding: 14px; }
        table { width:100%; border-collapse: collapse; }
        th, td { padding: 10px 8px; border-bottom: 1px solid rgba(255,255,255,.08); text-align:left; vertical-align: top; }
        th { color: #94a3b8; font-size: 12px; text-transform: uppercase; letter-spacing: .08em; }
        input, select, textarea { width: 100%; padding: 10px 12px; border-radius: 10px; border: 1px solid rgba(255,255,255,.14); background: rgba(0,0,0,.25); color: #e5e7eb; }
        textarea { min-height: 120px; }
        .row { display:grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .btn { display:inline-block; padding: 10px 12px; border-radius: 10px; border: 0; background: #2563eb; color: white; font-weight: 600; cursor: pointer; }
        .btn2 { display:inline-block; padding: 10px 12px; border-radius: 10px; border: 1px solid rgba(255,255,255,.12); background: transparent; color: #e5e7eb; cursor: pointer; }
        .danger { background: #ef4444; }
        .actions { display:flex; gap: 8px; flex-wrap: wrap; align-items:center; }
        .ok { color: #86efac; }
        .err { color: #fecaca; }
        .pager { margin-top: 14px; display:flex; gap: 10px; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="top">
        <div class="actions">
            <span class="pill">admin</span>
            <a href="{{ route('admin.constructs.index') }}">Constructs</a>
        </div>
        <form method="post" action="{{ route('admin.logout') }}">
            @csrf
            <button class="btn2" type="submit">Выйти</button>
        </form>
    </div>

    @if(session('ok'))
        <div class="card ok" style="margin-bottom: 12px;">{{ session('ok') }}</div>
    @endif

    @yield('content')
</div>
</body>
</html>

