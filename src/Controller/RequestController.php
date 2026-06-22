<?php

namespace App\Controller;

use App\Entity\ProcessingRequest;
use App\Message\ProcessRequestMessage;
use App\Repository\ProcessingRequestRepository;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/requests')]
final class RequestController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    #[OA\Get(
        summary: 'List processing requests',
        parameters: [
            new OA\Parameter(name: 'limit', in: 'query', schema: new OA\Schema(type: 'integer', default: 50, maximum: 200)),
            new OA\Parameter(name: 'offset', in: 'query', schema: new OA\Schema(type: 'integer', default: 0)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of requests, newest first',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'status', type: 'string', enum: ['pending', 'processing', 'done', 'failed']),
                        new OA\Property(property: 'result', type: 'object', nullable: true),
                        new OA\Property(property: 'createdAt', type: 'string', format: 'date-time'),
                        new OA\Property(property: 'updatedAt', type: 'string', format: 'date-time'),
                    ],
                    type: 'object'
                ))
            ),
        ]
    )]
    public function list(Request $request, ProcessingRequestRepository $repository): JsonResponse
    {
        $limit = min(max((int) $request->query->get('limit', 50), 1), 200);
        $offset = max((int) $request->query->get('offset', 0), 0);

        $items = $repository->findBy([], ['id' => 'DESC'], $limit, $offset);

        return $this->json(array_map(static fn (ProcessingRequest $r) => $r->toArray(), $items));
    }

    /**
     * @throws \JsonException
     */
    #[Route('', methods: ['POST'])]
    #[OA\Post(
        description: 'Stores the payload, enqueues it for background processing and returns the request id.',
        summary: 'Create a processing request',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'payload', type: 'object', example: ['a' => 2, 'b' => 3]),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Request accepted',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 1),
                    new OA\Property(property: 'status', type: 'string', example: 'pending'),
                ])
            ),
            new OA\Response(response: 400, description: 'Invalid JSON body'),
        ]
    )]
    public function create(
        Request $request,
        ProcessingRequestRepository $repository,
        MessageBusInterface $bus,
    ): JsonResponse {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($data) || !isset($data['payload']) || !is_array($data['payload'])) {
            return $this->json(['error' => 'invalid json: object with "payload" object expected'], 400);
        }

        $entity = new ProcessingRequest($data['payload']);
        $repository->save($entity);

        $bus->dispatch(new ProcessRequestMessage($entity->getId()));

        return $this->json(
            ['id' => $entity->getId(), 'status' => $entity->getStatus()->value],
            201
        );
    }

    #[Route('/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[OA\Get(
        summary: 'Get request status and result',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Request state',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 1),
                    new OA\Property(property: 'status', type: 'string', enum: ['pending', 'processing', 'done', 'failed']),
                    new OA\Property(property: 'result', type: 'object', nullable: true),
                    new OA\Property(property: 'createdAt', type: 'string', format: 'date-time'),
                    new OA\Property(property: 'updatedAt', type: 'string', format: 'date-time'),
                ])
            ),
            new OA\Response(response: 404, description: 'Request not found'),
        ]
    )]
    public function show(int $id, ProcessingRequestRepository $repository): JsonResponse
    {
        $entity = $repository->find($id);
        if ($entity === null) {
            return $this->json(['error' => 'not found'], 404);
        }

        return $this->json($entity->toArray());
    }

    #[Route('/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    #[OA\Delete(
        summary: 'Delete a processing request',
        responses: [
            new OA\Response(response: 204, description: 'Request deleted'),
            new OA\Response(response: 404, description: 'Request not found'),
        ]
    )]
    public function delete(int $id, ProcessingRequestRepository $repository): Response
    {
        $entity = $repository->find($id);
        if ($entity === null) {
            return $this->json(['error' => 'not found'], 404);
        }

        $repository->remove($entity);

        return new Response(status: 204);
    }
}
