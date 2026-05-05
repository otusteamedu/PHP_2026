@extends('admin.layout')

@section('title', 'Курсы')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
        <h1 class="h3 mb-0">Курсы</h1>
        <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">Новый курс</a>
    </div>
    <div class="table-responsive card shadow-sm">
        <table class="table table-striped table-hover mb-0 align-middle">
            <thead class="table-light">
            <tr>
                <th>Название</th>
                <th>Направление</th>
                <th>Slug</th>
                <th class="text-end">Часы</th>
                <th class="text-end">Действия</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($courses as $course)
                <tr>
                    <td>{{ $course->title }}</td>
                    <td>{{ $course->direction?->name }}</td>
                    <td><code>{{ $course->slug }}</code></td>
                    <td class="text-end">{{ $course->duration_hours ?? '—' }}</td>
                    <td class="text-end text-nowrap">
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.courses.edit', $course) }}">Изменить</a>
                        <form action="{{ route('admin.courses.destroy', $course) }}" method="post" class="d-inline" onsubmit="return confirm('Удалить курс?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Удалить</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-body-secondary text-center py-4">Пока нет курсов.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $courses->links() }}</div>
@endsection
