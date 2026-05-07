<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $construct->language->name }} · {{ $construct->title }}</title>
    <style>
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; margin: 0; background: #0b1220; color: #e5e7eb; }
        a { color: #93c5fd; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .wrap { max-width: 900px; margin: 0 auto; padding: 32px 16px; }
        .pill { display: inline-block; padding: 4px 10px; border-radius: 999px; background: rgba(255,255,255,.08); color: #cbd5e1; font-size: 12px; }
        .card { background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.08); border-radius: 16px; padding: 18px; }
        h1 { margin: 10px 0 4px; font-size: 28px; }
        .muted { color: #cbd5e1; }
        pre { background: #0a0f1a; border: 1px solid rgba(255,255,255,.08); padding: 14px; border-radius: 12px; overflow: auto; }
        code { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; font-size: 13px; }
        .grid { display: grid; gap: 14px; }
        .section-title { margin: 20px 0 8px; font-size: 14px; color: #94a3b8; text-transform: uppercase; letter-spacing: .08em; }
        ul { margin: 0; padding-left: 18px; }
        .aliases { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <div class="pill">{{ $construct->language->code }}</div>
        <h1>{{ $construct->title }}</h1>
        @if($construct->summary)
            <p class="muted">{{ $construct->summary }}</p>
        @endif
        @if($construct->details)
            <p>{{ $construct->details }}</p>
        @endif

        @if($construct->aliases->isNotEmpty())
            <div class="section-title">Aliases</div>
            <div class="aliases">
                @foreach($construct->aliases as $a)
                    <span class="pill">{{ $a->alias }}</span>
                @endforeach
            </div>
        @endif
    </div>

    <div class="grid">
        @if($construct->snippets->isNotEmpty())
            <div>
                <div class="section-title">Примеры</div>
                @foreach($construct->snippets as $s)
                    <div class="card" style="margin-bottom: 12px;">
                        @if($s->title)
                            <div class="muted" style="margin-bottom: 10px;">{{ $s->title }}</div>
                        @endif
                        <pre><code>{{ $s->code }}</code></pre>
                    </div>
                @endforeach
            </div>
        @endif

        @if($construct->links->isNotEmpty())
            <div>
                <div class="section-title">Ссылки</div>
                <div class="card">
                    <ul>
                        @foreach($construct->links as $l)
                            <li>
                                <a href="{{ $l->url }}" target="_blank" rel="noreferrer">
                                    {{ $l->title ?: $l->url }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>
</div>
</body>
</html>

