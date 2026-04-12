<?php

declare(strict_types=1);

namespace App;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;

readonly class OtusShopRepository
{
    private const INDEX_NAME = 'otus-shop';
    public function __construct(private Client $client)
    {}

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
     * @throws MissingParameterException
     */
    public function deleteIndex(): void
    {
        $this->client->indices()->delete(['index' => self::INDEX_NAME]);
    }
}
