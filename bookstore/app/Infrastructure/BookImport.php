<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Domain\Book;
use Elastic\Elasticsearch\Client;
use JsonException;
use RuntimeException;

class BookImport
{
    public function __construct(
        private readonly Client $client,
        private readonly string $indexName,
    ) {}

    public function import(string $filePath): int
    {      
        if (!is_file($filePath)) {
            throw new RuntimeException("Файл не найден: {$filePath}");
        }

         $content = file_get_contents($filePath);
        $lines = explode("\n", trim($content));

        $indexBody = [];
        $count = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            try {
                $decoded = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $e) {
                throw new RuntimeException("Ошибка в JSON: {$line}", 0, $e);
            }

            if (isset($decoded['create']) || isset($decoded['index'])) {
                continue;
            }

            $book = Book::fromArray($decoded);

            $indexBody[] = [
                'index' => [
                    '_index' => $this->indexName,
                    '_id' => $book->sku,
                ],
            ];

            $indexBody[] = $book->toElastic();
            $count++;
        }

        if (empty($indexBody)) {
            return 0;
        }

        $response = $this->client->bulk(['body' => $indexBody])->asArray();

        if (!empty($response['errors'])) {
            throw new RuntimeException('Ошибка в импорте данных в Elasticsearch');
        }

        $this->client->indices()->refresh(['index' => $this->indexName]);


        return $count;
    }
}
