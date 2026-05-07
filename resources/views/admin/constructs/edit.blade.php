@extends('admin._layout')

@section('title', 'Admin · Edit construct')

@section('content')
    <div class="card" style="margin-bottom: 12px;">
        <div class="actions" style="justify-content: space-between;">
            <div class="actions">
                <span class="pill">{{ $construct->language->code }}</span>
                <span class="pill">{{ $construct->slug }}</span>
                <a class="btn2" href="{{ url('/kb/'.$construct->language->code.'/'.$construct->slug) }}" target="_blank" rel="noreferrer">Открыть публично</a>
            </div>
            <form method="post" action="{{ route('admin.constructs.destroy', $construct) }}" onsubmit="return confirm('Удалить?')">
                @csrf
                @method('delete')
                <button class="btn danger" type="submit">Удалить</button>
            </form>
        </div>
    </div>

    <div class="card" style="margin-bottom: 12px;">
        <h2 style="margin:0 0 12px;">Construct</h2>
        <form method="post" action="{{ route('admin.constructs.update', $construct) }}">
            @csrf
            @method('put')

            <div class="row">
                <div>
                    <label class="pill">language</label>
                    <select name="language_id" required>
                        @foreach($languages as $l)
                            <option value="{{ $l->id }}" @selected($construct->language_id === $l->id)>{{ $l->code }} · {{ $l->name }}</option>
                        @endforeach
                    </select>
                    @error('language_id')<div class="err">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="pill">slug</label>
                    <input name="slug" value="{{ old('slug', $construct->slug) }}" required>
                    @error('slug')<div class="err">{{ $message }}</div>@enderror
                </div>
            </div>

            <div style="margin-top: 12px;">
                <label class="pill">title</label>
                <input name="title" value="{{ old('title', $construct->title) }}" required>
                @error('title')<div class="err">{{ $message }}</div>@enderror
            </div>

            <div style="margin-top: 12px;">
                <label class="pill">summary</label>
                <textarea name="summary">{{ old('summary', $construct->summary) }}</textarea>
                @error('summary')<div class="err">{{ $message }}</div>@enderror
            </div>

            <div style="margin-top: 12px;">
                <label class="pill">details</label>
                <textarea name="details">{{ old('details', $construct->details) }}</textarea>
                @error('details')<div class="err">{{ $message }}</div>@enderror
            </div>

            <div class="actions" style="margin-top: 12px;">
                <button class="btn" type="submit">Сохранить</button>
                <a class="btn2" href="{{ route('admin.constructs.index') }}">Назад</a>
            </div>
        </form>
    </div>

    <div class="row">
        <div class="card">
            <h2 style="margin:0 0 12px;">Snippets</h2>
            @foreach($construct->snippets as $s)
                <div class="card" style="margin-bottom: 10px;">
                    <div class="actions" style="justify-content: space-between;">
                        <div>
                            <div class="pill">sort: {{ $s->sort }}</div>
                            <div style="margin-top: 6px;">{{ $s->title }}</div>
                        </div>
                        <form method="post" action="{{ route('admin.snippets.destroy', $s) }}" onsubmit="return confirm('Удалить snippet?')">
                            @csrf
                            @method('delete')
                            <button class="btn danger" type="submit">Удалить</button>
                        </form>
                    </div>
                    <pre><code>{{ $s->code }}</code></pre>
                </div>
            @endforeach

            <form method="post" action="{{ route('admin.constructs.snippets.store', $construct) }}">
                @csrf
                <div class="row">
                    <div>
                        <label class="pill">title</label>
                        <input name="title">
                        @error('title')<div class="err">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="pill">sort</label>
                        <input name="sort" type="number" value="0" min="0">
                        @error('sort')<div class="err">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div style="margin-top: 12px;">
                    <label class="pill">code</label>
                    <textarea name="code" required></textarea>
                    @error('code')<div class="err">{{ $message }}</div>@enderror
                </div>
                <div class="actions" style="margin-top: 12px;">
                    <button class="btn" type="submit">Добавить snippet</button>
                </div>
            </form>
        </div>

        <div class="card">
            <h2 style="margin:0 0 12px;">Links</h2>
            @foreach($construct->links as $l)
                <div class="card" style="margin-bottom: 10px;">
                    <div class="actions" style="justify-content: space-between;">
                        <div>
                            <div class="pill">sort: {{ $l->sort }}</div>
                            <div style="margin-top: 6px;">
                                <a href="{{ $l->url }}" target="_blank" rel="noreferrer">{{ $l->title ?: $l->url }}</a>
                            </div>
                        </div>
                        <form method="post" action="{{ route('admin.links.destroy', $l) }}" onsubmit="return confirm('Удалить link?')">
                            @csrf
                            @method('delete')
                            <button class="btn danger" type="submit">Удалить</button>
                        </form>
                    </div>
                </div>
            @endforeach

            <form method="post" action="{{ route('admin.constructs.links.store', $construct) }}">
                @csrf
                <div class="row">
                    <div>
                        <label class="pill">title</label>
                        <input name="title">
                        @error('title')<div class="err">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="pill">sort</label>
                        <input name="sort" type="number" value="0" min="0">
                        @error('sort')<div class="err">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div style="margin-top: 12px;">
                    <label class="pill">url</label>
                    <input name="url" required>
                    @error('url')<div class="err">{{ $message }}</div>@enderror
                </div>
                <div class="actions" style="margin-top: 12px;">
                    <button class="btn" type="submit">Добавить link</button>
                </div>
            </form>
        </div>
    </div>
@endsection

