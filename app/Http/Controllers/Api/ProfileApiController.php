<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Profile')]
class ProfileApiController extends Controller
{
    #[OA\Get(
        path: '/api/v1/me',
        operationId: 'profileMe',
        description: 'Данные пользователя для мобильного личного кабинета',
        tags: ['Profile'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Профиль',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer'),
                        new OA\Property(property: 'name', type: 'string'),
                        new OA\Property(property: 'email', type: 'string'),
                        new OA\Property(
                            property: 'email_verified_at',
                            type: 'string',
                            format: 'date-time',
                            nullable: true
                        ),
                        new OA\Property(
                            property: 'roles',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer'),
                                    new OA\Property(property: 'name', type: 'string'),
                                    new OA\Property(property: 'slug', type: 'string'),
                                ],
                                type: 'object'
                            )
                        ),
                        new OA\Property(
                            property: 'profile',
                            nullable: true,
                            properties: [
                                new OA\Property(property: 'bio', type: 'string', nullable: true),
                                new OA\Property(property: 'headline', type: 'string', nullable: true),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load(['roles', 'profile']);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at,
            'roles' => $user->roles->map(static fn (Role $role) => [
                'id' => $role->id,
                'name' => $role->name,
                'slug' => $role->slug,
            ])->values()->all(),
            'profile' => $user->profile !== null ? [
                'bio' => $user->profile->bio,
                'headline' => $user->profile->headline,
            ] : null,
        ]);
    }
}
