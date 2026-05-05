@extends('admin.layout')

@section('title', 'Редактирование страницы')

@section('content')
    <h1 class="h3 mb-3">Редактирование</h1>
    <div class="card shadow-sm">
        <div class="card-body">
            <form method="post" action="{{ route('admin.pages.update', $page) }}">
                @csrf
                @method('PUT')
                @include('admin.pages._form', ['page' => $page])
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                    <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary">К списку</a>
                </div>
            </form>
        </div>
    </div>
@endsection
