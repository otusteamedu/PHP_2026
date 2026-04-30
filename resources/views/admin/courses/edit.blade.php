@extends('admin.layout')

@section('title', 'Редактирование курса')

@section('content')
    <h1 class="h3 mb-3">Редактирование курса</h1>
    <div class="card shadow-sm">
        <div class="card-body">
            <form method="post" action="{{ route('admin.courses.update', $course) }}">
                @csrf
                @method('PUT')
                @include('admin.courses._form', ['course' => $course])
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                    <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary">К списку</a>
                </div>
            </form>
        </div>
    </div>
@endsection
