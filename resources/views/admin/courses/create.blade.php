@extends('admin.layout')

@section('title', 'Новый курс')

@section('content')
    <h1 class="h3 mb-3">Новый курс</h1>
    <div class="card shadow-sm">
        <div class="card-body">
            <form method="post" action="{{ route('admin.courses.store') }}">
                @csrf
                @include('admin.courses._form', ['course' => null])
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                    <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary">Отмена</a>
                </div>
            </form>
        </div>
    </div>
@endsection
