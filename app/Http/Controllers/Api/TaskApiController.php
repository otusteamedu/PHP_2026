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
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Tasks')]
class TaskApiController extends Controller
{
    public function __construct(
        private readonly TaskRepository $tasks
    ) {}

    #[OA\Get(
        path: '/api/v1/tasks',
        operationId: 'tasksList',
        tags: ['Tasks'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Task'))
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function list(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Task::class);

        $tasks = $request->user()->tasks()->latest()->get();

        return response()->json($tasks);
    }

    #[OA\Get(
        path: '/api/v1/tasks/{task}',
        operationId: 'tasksShow',
        tags: ['Tasks'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'task', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Task',
                content: new OA\JsonContent(ref: '#/components/schemas/Task')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function show(Request $request, Task $task): JsonResponse
    {
        Gate::authorize('view', $task);

        return response()->json($task);
    }

    #[OA\Post(
        path: '/api/v1/tasks',
        operationId: 'tasksStore',
        tags: ['Tasks'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['title'],
                properties: [
                    new OA\Property(property: 'title', type: 'string', maxLength: 255),
                    new OA\Property(property: 'description', type: 'string', maxLength: 10000, nullable: true),
                    new OA\Property(property: 'is_done', type: 'boolean'),
                    new OA\Property(property: 'due_at', type: 'string', format: 'date-time', nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Created',
                content: new OA\JsonContent(ref: '#/components/schemas/Task')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
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

    #[OA\Put(
        path: '/api/v1/tasks/{task}',
        operationId: 'tasksUpdate',
        tags: ['Tasks'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'task', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['title'],
                properties: [
                    new OA\Property(property: 'title', type: 'string', maxLength: 255),
                    new OA\Property(property: 'description', type: 'string', maxLength: 10000, nullable: true),
                    new OA\Property(property: 'is_done', type: 'boolean'),
                    new OA\Property(property: 'due_at', type: 'string', format: 'date-time', nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated',
                content: new OA\JsonContent(ref: '#/components/schemas/Task')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
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

    #[OA\Delete(
        path: '/api/v1/tasks/{task}',
        operationId: 'tasksDestroy',
        tags: ['Tasks'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'task', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Deleted'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function destroy(Request $request, Task $task): JsonResponse
    {
        Gate::authorize('delete', $task);

        $task->delete();

        return response()->noContent();
    }
}
