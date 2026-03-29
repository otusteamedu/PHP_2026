<?php

declare(strict_types=1);

namespace App\Services;

class RequestParser
{
    public function __construct(private readonly int $maxEmails) {}

    public function parse(string $request): array
    {
        $input = json_decode($request, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Ошибка при парсинге объекта JSON в запросе', 400);
        }

        if (!is_array($input) || !isset($input['emails']) || !is_array($input['emails'])) {
            throw new \Exception('Ошибка в теле запроса', 422);
        }

        $emails = array_filter($input['emails'], 'is_string');
        $emails = array_map('trim', $emails);
        $emails = array_filter($emails);
        $emails = array_values(array_unique($emails));

        if (empty($emails)) {
            throw new \Exception('В запросе отсутствует электронный адрес', 422);
        }

        if (count($emails) > $this->maxEmails) {
            throw new \Exception('Ошибка в количестве электронных адресов', 422);
        }

        return $emails;
    }
}