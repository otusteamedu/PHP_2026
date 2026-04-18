<?php

declare(strict_types=1);

namespace App;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;

final readonly class OtusShopRepository
{
    private const string INDEX_NAME = 'otus-shop';

    public function __construct(private Client $client)
    {}

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     */
    public function search(SearchInput $input): array
    {
        $must = [];
        $filter = [];

        if ($input->query !== null) {
            $must[] = [
                'match' => [
                    'title' => [
                        'query' => $input->query,
                        'fuzziness' => 'AUTO',
                    ],
                ],
            ];
        }

        if ($input->category !== null) {
            $filter[] = [
                'term' => [
                    'category' => $input->category,
                ],
            ];
        }

        if ($input->maxPrice !== null) {
            $filter[] = [
                'range' => [
                    'price' => [
                        'lte' => $input->maxPrice,
                    ],
                ],
            ];
        }

        if ($input->minPrice !== null) {
            $filter[] = [
                'range' => [
                    'price' => [
                        'gte' => $input->minPrice,
                    ],
                ],
            ];
        }

        if ($input->inStock) {
            $filter[] = [
                'nested' => [
                    'path' => 'stock',
                    'query' => [
                        'range' => [
                            'stock.stock' => [
                                'gt' => 0,
                            ],
                        ],
                    ],
                ],
            ];
        }

        $params = [
            'index' => self::INDEX_NAME,
            'body'  => [
                'size' => $input->limit ?? 10,
                'query' => empty($must) && empty($filter)
                    ? ['match_all' => (object) []]
                    : [
                        'bool' => array_filter([
                            'must' => $must,
                            'filter' => $filter,
                        ]),
                    ],
            ],
        ];

        $response = $this->client->search($params);
        $hits = $response->asArray()['hits']['hits'] ?? [];
        return array_map(fn (array $book) => Book::fromArray($book['_source']), $hits);
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     * @throws MissingParameterException
     */
    public function createIndex(): void
    {
        $response = $this->client->indices()->create([
            'index' => self::INDEX_NAME,
            'body'  => [
                'settings' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 0,
                ],
                'mappings' => [
                    'properties' => [
                        'title' => ['type' => 'text', 'analyzer' => 'russian'],
                        'sku' => ['type' => 'keyword'],
                        'category' => ['type' => 'keyword'],
                        'price' => ['type' => 'float'],
                        'stock' => [
                            'type' => 'nested',
                            'properties' => [
                                'shop' => ['type' => 'keyword'],
                                'stock' => ['type' => 'integer'],
                            ],
                        ],
                    ],
                ],
            ]
        ]);

        if ($response->getReasonPhrase() !== 'OK') {
            throw new ClientResponseException($response->getReasonPhrase());
        }
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     */
    public function fillIndex(): void
    {
        $data = file_get_contents(__DIR__ . '/../books.json');
        $response = $this->client->bulk(['body' => $data]);

        if ($response->getReasonPhrase() !== 'OK' && $response->getReasonPhrase() !== 'Continue') {
            throw new ClientResponseException($response->getReasonPhrase());
        }
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     * @throws MissingParameterException
     */
    public function deleteIndex(): void
    {
        $response = $this->client->indices()->delete(['index' => self::INDEX_NAME]);

        if ($response->getReasonPhrase() !== 'OK') {
            throw new ClientResponseException($response->getReasonPhrase());
        }
    }
}
