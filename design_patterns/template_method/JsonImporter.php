<?php

declare(strict_types=1);

class JsonImporter extends AbstractImporter
{
    protected function load(string $loaded): string
    {
        return file_get_contents($loaded);
    }

    protected function parse(string $loadedData): array
    {
        return json_decode($loadedData, true) ?? [];
    }

    protected function validate(array $parsedData): array
    {
        $requiredFields = ['orderId', 'amount', 'merchant'];

        foreach ($requiredFields as $field) {
            if (! isset($parsedData[$field])) {
                throw new Exception('Отсутствует ключ ' . $field);
            }
        }

        return $parsedData;
    }

    protected function transform(array $validData): array
    {

        return [
            'orderId'  => $validData['orderId'],
            'amount'   => $validData['amount'],
            'merchant' => $validData['merchant'],
        ];
    }
}