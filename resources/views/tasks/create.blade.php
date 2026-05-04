@extends('layouts.app')

@section('title', 'Новая задача')

@section('nav_tasks_active', 'active')

@section('content')
    <h1 class="h4 mb-4">Новая задача</h1>
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
            <form method="post" action="{{ route('tasks.store') }}">
                @csrf
                @include('tasks._form', ['task' => new \App\Models\Task()])
                <div class="mt-4 d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary">Создать</button>
                    <a class="btn btn-outline-secondary" href="{{ route('tasks.index') }}">Отмена</a>
                </div>
            </form>
        </div>
    </div>
@endsection
