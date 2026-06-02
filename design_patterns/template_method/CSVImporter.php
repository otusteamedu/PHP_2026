<?php

declare(strict_types=1);

class CSVImporter extends AbstractImporter
{
    protected function load(string $loaded): string
    {
        return file_get_contents($loaded);
    }

    protected function parse(string $loadedData): array
    {
        $rows = explode("\n", trim($loadedData));
        $headers = str_getcsv(array_shift($rows));

        if (empty($rows)) {
            return [];
        }
       
        $firstRow = str_getcsv($rows[0]);
        return array_combine($headers, $firstRow) ?: [];
    }

    protected function validate(array $parsedData): array
    {
        $requiredFields = ['fio', 'pasport', 'phone'];

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
            'fio'  => $validData['fio'],
            'pasport'   => $validData['pasport'],
            'phone' => $validData['phone'],
        ];
    }

    protected function beforeSave(array $entities): void
    {
        $path = __DIR__ . "/csv_data_{$entities['orderId']}.txt";
        file_put_contents($path, $entities);
    }
}