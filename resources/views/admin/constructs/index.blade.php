@extends('admin._layout')

@section('title', 'Admin · Constructs')

@section('content')
    @php($filters = $filters ?? ['language' => '', 'q' => ''])

    <div class="card" style="margin-bottom: 12px;">
        <form method="get" action="{{ route('admin.constructs.index') }}">
            <div class="row">
                <div>
                    <label class="pill">language</label>
                    <select name="language">
                        <option value="">all</option>
                        @foreach($languages as $l)
                            <option value="{{ $l->code }}" @selected(($filters['language'] ?? '') === $l->code)>
                                {{ $l->code }} · {{ $l->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="pill">q</label>
                    <input name="q" value="{{ $filters['q'] ?? '' }}" placeholder="search...">
                </div>
            </div>
            <div class="actions" style="margin-top: 12px;">
                <button class="btn" type="submit">Фильтр</button>
                <a class="btn2" href="{{ route('admin.constructs.create') }}">Добавить</a>
            </div>
        </form>
    </div>

    <div class="card">
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Language</th>
                <th>Slug</th>
                <th>Title</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $c)
                <tr>
                    <td>{{ $c->id }}</td>
                    <td>{{ $c->language->code }}</td>
                    <td>{{ $c->slug }}</td>
                    <td>{{ $c->title }}</td>
                    <td><a href="{{ route('admin.constructs.edit', $c) }}">edit</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="pager">
            @if($items->previousPageUrl())
                <a class="btn2" href="{{ $items->previousPageUrl() }}">← prev</a>
            @endif
            @if($items->nextPageUrl())
                <a class="btn2" href="{{ $items->nextPageUrl() }}">next →</a>
            @endif
        </div>
    </div>
@endsection

