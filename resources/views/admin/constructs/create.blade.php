@extends('admin._layout')

@section('title', 'Admin · Create construct')

@section('content')
    <div class="card">
        <h1 style="margin: 0 0 12px;">Create construct</h1>

        <form method="post" action="{{ route('admin.constructs.store') }}">
            @csrf

            <div class="row">
                <div>
                    <label class="pill">language</label>
                    <select name="language_id" required>
                        @foreach($languages as $l)
                            <option value="{{ $l->id }}">{{ $l->code }} · {{ $l->name }}</option>
                        @endforeach
                    </select>
                    @error('language_id')<div class="err">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="pill">slug</label>
                    <input name="slug" value="{{ old('slug') }}" required>
                    @error('slug')<div class="err">{{ $message }}</div>@enderror
                </div>
            </div>

            <div style="margin-top: 12px;">
                <label class="pill">title</label>
                <input name="title" value="{{ old('title') }}" required>
                @error('title')<div class="err">{{ $message }}</div>@enderror
            </div>

            <div style="margin-top: 12px;">
                <label class="pill">summary</label>
                <textarea name="summary">{{ old('summary') }}</textarea>
                @error('summary')<div class="err">{{ $message }}</div>@enderror
            </div>

            <div style="margin-top: 12px;">
                <label class="pill">details</label>
                <textarea name="details">{{ old('details') }}</textarea>
                @error('details')<div class="err">{{ $message }}</div>@enderror
            </div>

            <div class="actions" style="margin-top: 12px;">
                <button class="btn" type="submit">Создать</button>
                <a class="btn2" href="{{ route('admin.constructs.index') }}">Назад</a>
            </div>
        </form>
    </div>
@endsection

