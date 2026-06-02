<?php

declare(strict_types=1);

class XMLImporter extends AbstractImporter
{
    protected function load(string $loaded): string
    {
        return file_get_contents($loaded);
    }

    protected function parse(string $loadedData): array
    {
        try {
            $dataXml = new SimpleXMLElement($loadedData);
        } catch (\Exception $e) {
            return [];
        }

        return json_decode(json_encode($dataXml), true) ?? [];
    }

    protected function validate(array $parsedData): array
    {
        $requiredFields = ['userId', 'email', 'phone'];

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
            'userId'  => $validData['userId'],
            'email'   => $validData['email'],
            'phone' => $validData['phone'],
        ];
    }
}