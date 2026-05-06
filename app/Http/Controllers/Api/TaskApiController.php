<?php

namespace App\Http\Controllers\Api;

use App\Domain\Task\TaskAggregate;
use App\Domain\Task\TaskRepository;
use App\Domain\Task\ValueObjects\TaskDescription;
use App\Domain\Task\ValueObjects\TaskTitle;
use App\Events\TaskCreated;
use App\Http\Controllers\Controller;
use App\Models\Task;
use DateTimeImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TaskApiController extends Controller
{
    public function __construct(
        private readonly TaskRepository $tasks
    ) {}

    public function list(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Task::class);

        $tasks = $request->user()->tasks()->latest()->get();

        return response()->json($tasks);
    }

    public function show(Request $request, Task $task): JsonResponse
    {
        Gate::authorize('view', $task);

        return response()->json($task);
    }

    public function store(Request $request): JsonResponse
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

        return response()->json($persisted, 201);
    }

    public function update(Request $request, Task $task): JsonResponse
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

        $task->refresh();

        return response()->json($task);
    }

    public function destroy(Request $request, Task $task): JsonResponse
    {
        Gate::authorize('delete', $task);

        $task->delete();

        return response()->noContent();
    }
}
