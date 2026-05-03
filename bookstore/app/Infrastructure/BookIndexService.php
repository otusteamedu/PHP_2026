<?php

declare(strict_types=1);

namespace App\Infrastructure;

use Elastic\Elasticsearch\Client;

class BookIndexService
{
    public function __construct(
        private readonly Client $client,
        private readonly string $indexName,
    ) {}

    public function createIndex(): void
    {
        $exists = $this->client->indices()->exists([
            'index' => $this->indexName,
        ])->asBool();

        if ($exists) {
            return;
        }

        $this->client->indices()->create([
            'index' => $this->indexName,
            'body' => [
                'settings' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 0,
                    'analysis' => [
                        'normalizer' => [
                            'lowercase_normalizer' => [
                                'type' => 'custom',
                                'filter' => ['lowercase']
                            ]
                        ]
                    ]
                ],
                'mappings' => [
                    'properties' => [
                        'sku' => [
                            'type' => 'keyword'
                        ],
                        'title' => [
                            'type' => 'text',
                            'analyzer' => 'russian',
                        ],
                        'category' => [
                            'type' => 'text',
                            'analyzer' => 'russian',
                            'fields' => [
                                'keyword' => [
                                    'type' => 'keyword',
                                    'normalizer' => 'lowercase_normalizer',                                    
                                ],
                            ],
                        ],                       
                        'price' => [
                            'type' => 'integer'
                        ],
                        'stock_total' => [
                            'type' => 'integer'
                        ],
                    ],
                ],
            ],
        ]);
    }
}
