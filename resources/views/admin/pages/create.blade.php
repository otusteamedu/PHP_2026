@extends('admin.layout')

@section('title', 'Новая страница')

@section('content')
    <h1 class="h3 mb-3">Новая страница</h1>
    <div class="card shadow-sm">
        <div class="card-body">
            <form method="post" action="{{ route('admin.pages.store') }}">
                @csrf
                @include('admin.pages._form', ['page' => null])
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                    <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary">Отмена</a>
                </div>
            </form>
        </div>
    </div>
@endsection
