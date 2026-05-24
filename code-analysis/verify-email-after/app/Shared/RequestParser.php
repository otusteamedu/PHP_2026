<?php

declare(strict_types=1);

namespace App\Shared;

class RequestParser
{
    public function __construct(private readonly int $maxEmails) {}

    public function parse(string $request): array
    {
        $input = json_decode($request, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception(RequestMessages::JSON_ERROR->value, 400);
        }

        if (!is_array($input) || !isset($input['emails']) || !is_array($input['emails'])) {
            throw new \Exception(RequestMessages::EMPTY_REQUEST_BODY->value, 422);
        }

        $emails = array_filter($input['emails'], 'is_string');
        $emails = array_map('trim', $emails);
        $emails = array_filter($emails);
        $emails = array_values(array_unique($emails));

        if (empty($emails)) {
            throw new \Exception(RequestMessages::MISSING_EMAIL_ADDRESS->value, 422);
        }

        if (count($emails) > $this->maxEmails) {
            throw new \Exception(RequestMessages::ERROR_EADDRES_COUNT->value, 422);
        }

        return $emails;
    }
}