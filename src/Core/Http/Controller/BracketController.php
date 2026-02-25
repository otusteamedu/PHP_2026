<?php

declare(strict_types=1);

namespace App\Core\Http\Controller;

use App\Core\Exception\ValidationException;
use App\Core\Http\JsonResponse;
use App\Core\Http\Request;
use App\Core\Service\BracketValidator;
use InvalidArgumentException;

final class BracketController
{
    public function __construct(
        private BracketValidator $validator,
    ) {
    }

    public function validate(Request $request): JsonResponse
    {
        if ($request->getMethod() !== 'POST') {
            return new JsonResponse(['success' => false, 'error' => 'Only POST allowed'], 405);
        }

        $string = $request->getPost('string', '');

        try {
            $this->validator->validate($string);

            return new JsonResponse([
                'success' => true,
                'message' => 'Everything is correct'
            ]);

        } catch (ValidationException | InvalidArgumentException $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
