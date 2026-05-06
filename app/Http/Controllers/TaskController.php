<?php

namespace App\Http\Controllers;

use App\Domain\Task\TaskAggregate;
use App\Domain\Task\TaskRepository;
use App\Domain\Task\ValueObjects\TaskDescription;
use App\Domain\Task\ValueObjects\TaskTitle;
use App\Events\TaskCreated;
use App\Models\Task;
use DateTimeImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function __construct(
        private readonly TaskRepository $tasks
    ) {}

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

        $due = isset($validated['due_at']) && $validated['due_at'] !== ''
            ? new DateTimeImmutable($validated['due_at'])
            : null;

        $aggregate = TaskAggregate::begin(
            $request->user()->id,
            TaskTitle::fromString($validated['title']),
            TaskDescription::fromNullable($validated['description'] ?? null),
            $request->boolean('is_done'),
            $due,
        );

        $persisted = $this->tasks->save($aggregate);

        event(new TaskCreated($persisted));

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

        $aggregate = $this->tasks->findForOwner((int) $task->getKey(), (int) $request->user()->id);
        if ($aggregate === null) {
            abort(404);
        }

        $due = isset($validated['due_at']) && $validated['due_at'] !== ''
            ? new DateTimeImmutable($validated['due_at'])
            : null;

        $aggregate->recordTitleNotesAndSchedule(
            TaskTitle::fromString($validated['title']),
            TaskDescription::fromNullable($validated['description'] ?? null),
            $request->boolean('is_done'),
            $due,
        );

        $this->tasks->save($aggregate);

        return redirect()->route('tasks.index')->with('ok', 'Сохранено.');
    }

    public function destroy(string $locale, Task $task): RedirectResponse
    {
        Gate::authorize('delete', $task);

        $task->delete();

        return redirect()->route('tasks.index')->with('ok', 'Удалено.');
    }
}
