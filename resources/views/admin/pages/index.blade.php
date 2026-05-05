@extends('admin.layout')

@section('title', 'Страницы')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
        <h1 class="h3 mb-0">Текстовые страницы</h1>
        <a href="{{ route('admin.pages.create') }}" class="btn btn-primary">Новая страница</a>
    </div>
    <div class="table-responsive card shadow-sm">
        <table class="table table-striped table-hover mb-0 align-middle">
            <thead class="table-light">
            <tr>
                <th>Заголовок</th>
                <th>Slug</th>
                <th>Опубликована</th>
                <th class="text-end">Действия</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($pages as $page)
                <tr>
                    <td>{{ $page->title }}</td>
                    <td><code>{{ $page->slug }}</code></td>
                    <td>@if($page->is_published)<span class="badge text-bg-success">да</span>@else<span class="badge text-bg-secondary">нет</span>@endif</td>
                    <td class="text-end text-nowrap">
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('page.show', $page) }}" target="_blank">Просмотр</a>
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.pages.edit', $page) }}">Изменить</a>
                        <form action="{{ route('admin.pages.destroy', $page) }}" method="post" class="d-inline" onsubmit="return confirm('Удалить страницу?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Удалить</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-body-secondary text-center py-4">Пока нет страниц.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $pages->links() }}</div>
@endsection
