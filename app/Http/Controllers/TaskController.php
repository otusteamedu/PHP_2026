<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Task::class, 'task', ['except' => ['show']]);
    }

    public function index(Request $request): View
    {
        $tasks = $request->user()->tasks()->latest()->paginate(15);

        return view('tasks.index', compact('tasks'));
    }

    public function create(): View
    {
        return view('tasks.create');
    }

    public function store(Request $request): RedirectResponse
    {
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

    public function edit(Task $task): View
    {
        return view('tasks.edit', compact('task'));
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
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

    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();

        return redirect()->route('tasks.index')->with('ok', 'Удалено.');
    }
}
