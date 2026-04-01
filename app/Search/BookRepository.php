<?php

declare(strict_types=1);

namespace App\Search;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;

class BookRepository
{
    private Client $client;
    private string $index;

    /**
     * @throws AuthenticationException
     */
    public function __construct(string $host, string $index = 'books')
    {
        $this->client = ClientBuilder::create()->setHosts([$host])->build();
        $this->index = $index;
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     */
    public function createIndex(): void
    {
        if ($this->client->indices()->exists(['index' => $this->index])->asBool()) {
            $this->client->indices()->delete(['index' => $this->index]);
        }

        $this->client->indices()->create([
            'index' => $this->index,
            'body'  => [
                'settings' => [
                    'analysis' => [
                        'analyzer' => [
                            'russian_text' => [
                                'type'      => 'custom',
                                'tokenizer' => 'standard',
                                'filter'    => ['lowercase', 'russian_stop', 'russian_stemmer'],
                            ],
                        ],
                        'filter' => [
                            'russian_stop' => [
                                'type'      => 'stop',
                                'stopwords' => '_russian_',
                            ],
                            'russian_stemmer' => [
                                'type'     => 'stemmer',
                                'language' => 'russian',
                            ],
                        ],
                    ],
                ],
                'mappings' => [
                    'properties' => [
                        'title'    => ['type' => 'text',    'analyzer' => 'russian_text'],
                        'category' => ['type' => 'keyword'],
                        'price'    => ['type' => 'float'],
                        'stock'    => ['type' => 'integer'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function bulkIndex(array $books): void
    {
        $params = ['body' => []];

        foreach ($books as $book) {
            $params['body'][] = ['index' => ['_index' => $this->index]];
            $params['body'][] = $book;
        }

        $this->client->bulk($params);
        $this->client->indices()->refresh(['index' => $this->index]);
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     */
    public function search(string $query, float $maxPrice, int $minStock = 1): array
    {
        $response = $this->client->search([
            'index' => $this->index,
            'body'  => [
                'query' => [
                    'bool' => [
                        'must' => [
                            [
                                'multi_match' => [
                                    'query'     => $query,
                                    'fields'    => ['title', 'category'],
                                    'fuzziness' => 'AUTO',
                                ],
                            ],
                        ],
                        'filter' => [
                            ['range' => ['price' => ['lte' => $maxPrice]]],
                            ['range' => ['stock' => ['gte' => $minStock]]],
                        ],
                    ],
                ],
            ],
        ]);

        $hits = $response->asArray()['hits']['hits'] ?? [];

        return array_map(static fn(array $hit) => $hit['_source'], $hits);
    }
}
