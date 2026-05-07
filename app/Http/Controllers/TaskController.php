<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Task::class);

        $tasks = $request->user()->tasks()->latest()->paginate(15);

        return view('tasks.index', compact('tasks'));
    }

    public function create(): View
    {
        Gate::authorize('create', Task::class);

        return view('tasks.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Task::class);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'is_done' => ['sometimes', 'boolean'],
            'due_at' => ['nullable', 'date'],
        ]);
        $validated['user_id'] = $request->user()->id;
        $validated['is_done'] = $request->boolean('is_done');

        Task::query()->create($validated);

        return redirect()->route('tasks.index')->with('ok', 'Задача создана.');
    }

    public function edit(string $locale, Task $task): View
    {
        Gate::authorize('update', $task);

        return view('tasks.edit', compact('task'));
    }

    public function update(string $locale, Task $task, Request $request): RedirectResponse
    {
        Gate::authorize('update', $task);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'is_done' => ['sometimes', 'boolean'],
            'due_at' => ['nullable', 'date'],
        ]);
        $validated['is_done'] = $request->boolean('is_done');
        $task->update($validated);

        return redirect()->route('tasks.index')->with('ok', 'Сохранено.');
    }

    public function destroy(string $locale, Task $task): RedirectResponse
    {
        Gate::authorize('delete', $task);

        $task->delete();

        return redirect()->route('tasks.index')->with('ok', 'Удалено.');
    }
}
