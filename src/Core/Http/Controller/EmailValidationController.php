<?php

declare(strict_types=1);

namespace App\Core\Http\Controller;

use App\Core\Http\JsonResponse;
use App\Core\Http\Request;
use App\Service\EmailValidator;
use JsonException;

final readonly class EmailValidationController
{
    public function __construct(
        private EmailValidator $validator
    ) {
    }

    public function emailVerify(Request $request): JsonResponse
    {
        if ($request->getMethod() !== 'POST') {
            return new JsonResponse(['success' => false, 'error' => 'Only POST allowed'], 405);
        }

        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

            if (isset($data['email'])) {
                $email = (string)$data['email'];
                $isValid = $this->validator->isValid($email);

                return new JsonResponse([
                    'email' => $email,
                    'valid' => $isValid,
                ]);
            }

            if (isset($data['emails']) && is_array($data['emails'])) {
                $emails = array_map('strval', $data['emails']);
                $valid = $this->validator->filterValid($emails);

                return new JsonResponse([
                    'valid' => $valid,
                    'invalid' => array_values(array_diff($emails, $valid)),
                ]);
            }

            return new JsonResponse(['error' => 'Missing "email" or "emails" field'], 400);
        } catch (JsonException $e) {
            return new JsonResponse(['error' => 'Invalid JSON'], 400);
        }
    }
}
