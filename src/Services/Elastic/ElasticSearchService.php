<?php

declare(strict_types=1);

namespace App\Services\Elastic;

use Elastic\Elasticsearch\Client;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Utils;

class ElasticSearchService
{
    public function __construct(
        private readonly Client $client,
    ) {
    }

    private const string DATA_DIR = __DIR__ . '/../../Data';

    public function createBookIndex(): void
    {
        $this->ingestData(self::DATA_DIR . '/books.json');
    }

    public function createWineIndex(): void
    {
        $this->ingestData(self::DATA_DIR . '/wines.json');
    }

    /**
     * @throws GuzzleException
     */
    private function ingestData(string $path): void
    {
        $guzzleClient = new GuzzleClient([
            'timeout' => 300, // 5 minutes for large files
        ]);

        $response = $guzzleClient->post('http://localhost:9200/_bulk', [
            'headers' => [
                'Content-Type' => 'application/x-ndjson',
            ],
            'body' => Utils::tryFopen($path, 'rb'),
            'query' => [
                'pretty' => 'true',
            ],
        ]);

        echo $response->getBody();
    }

    public function search(
        string $searchTerm,
        int $limit = 20,
        ?string $sort = null,
        ?int $stockGt = null,
        bool $strict = true,
    ): array
    {
        $searchTerm = trim($searchTerm);
        if ($searchTerm === '') {
            return [];
        }

        $query = [
            'multi_match' => [
                'query' => $searchTerm,
                'fields' => ['title^2', 'category', 'sku'],
                'operator' => 'and',
            ],
        ];

        if (!$strict) {
            $query = [
                'multi_match' => [
                    'query' => $searchTerm,
                    'fields' => ['title^2', 'category', 'sku'],
                    'operator' => 'or',
                    'minimum_should_match' => '75%',
                    'fuzziness' => 'AUTO',
                ],
            ];
        }

        $params = [
            'index' => 'otus-shop',
            'body' => [
                'size' => $limit,
                'query' => $query,
            ],
        ];

        if ($stockGt !== null) {
            $params['body']['query'] = [
                'bool' => [
                    'must' => $params['body']['query'],
                    'filter' => [
                        [
                            'range' => [
                                'stock.stock' => ['gt' => $stockGt],
                            ],
                        ],
                    ],
                ],
            ];
        }

        $params['body']['sort'] = match ($sort) {
            'min', 'price_asc' => [['price' => ['order' => 'asc']]],
            'max', 'price_desc' => [['price' => ['order' => 'desc']]],
            default => [['_score' => 'desc']],
        };

        $response = $this->client->search($params)->asArray();
        $hits = $response['hits']['hits'] ?? [];

        return array_map(
            static function (array $hit): array {
                $source = $hit['_source'] ?? [];

                return [
                    'title' => (string) ($source['title'] ?? ''),
                    'sku' => (string) ($source['sku'] ?? ''),
                    'category' => (string) ($source['category'] ?? ''),
                    'price' => $source['price'] ?? null,
                    'score' => $hit['_score'] ?? null,
                ];
            },
            $hits,
        );
    }
}
