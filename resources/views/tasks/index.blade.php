@extends('layouts.app')

@section('title', 'Мои задачи')

@section('nav_tasks_active', 'active')

@section('content')
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
        <h1 class="h4 mb-0">Мои задачи</h1>
        @can('create', \App\Models\Task::class)
            <a class="btn btn-primary" href="{{ route('tasks.create') }}">Новая задача</a>
        @endcan
    </div>

    @if ($tasks->isEmpty())
        <p class="text-body-secondary mb-0">Задач пока нет. <a href="{{ route('tasks.create') }}">Создать первую</a>.</p>
    @else
        <div class="table-responsive shadow-sm rounded bg-white">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                <tr>
                    <th>Название</th>
                    <th class="d-none d-md-table-cell">Срок</th>
                    <th>Статус</th>
                    <th class="text-end">Действия</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($tasks as $task)
                    <tr>
                        <td>
                            <span class="fw-medium">{{ $task->title }}</span>
                            @if ($task->description)
                                <div class="small text-body-secondary text-truncate" style="max-width: 320px;">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($task->description), 80) }}
                                </div>
                            @endif
                        </td>
                        <td class="d-none d-md-table-cell small text-body-secondary">
                            {{ $task->due_at ? $task->due_at->translatedFormat('d.m.Y H:i') : '—' }}
                        </td>
                        <td>
                            @if ($task->is_done)
                                <span class="badge text-bg-success">Готово</span>
                            @else
                                <span class="badge text-bg-secondary">В работе</span>
                            @endif
                        </td>
                        <td class="text-end">
                            @can('update', $task)
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('tasks.edit', $task) }}">Изменить</a>
                            @endcan
                            @can('delete', $task)
                                <form class="d-inline" method="post" action="{{ route('tasks.destroy', $task) }}"
                                      onsubmit="return confirm('Удалить задачу?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Удалить</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $tasks->links() }}
        </div>
    @endif
@endsection
