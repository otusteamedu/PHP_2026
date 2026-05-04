@extends('layouts.app')

@section('title', 'Редактирование задачи')

@section('nav_tasks_active', 'active')

@section('content')
    <h1 class="h4 mb-4">Редактирование задачи</h1>
    <div class="card shadow-sm">
        <div class="card-body p-4">
            @if ($errors->any())
                <div class="alert alert-danger small">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="post" action="{{ route('tasks.update', $task) }}">
                @csrf
                @method('PUT')
                @include('tasks._form', ['task' => $task])
                <div class="mt-4 d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                    <a class="btn btn-outline-secondary" href="{{ route('tasks.index') }}">К списку</a>
                </div>
            </form>
        </div>
    </div>
@endsection
