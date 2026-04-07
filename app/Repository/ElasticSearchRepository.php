<?php

namespace App\Repository;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;

class ElasticSearchRepository
{
    private Client $eClient;

    public function __construct()
    {
        $this->eClient = ClientBuilder::create()
            ->setHosts(['http://hw10-elasticsearch:9200'])
            ->build();
    }

    public function createIndex(string $indexName)
    {
        $this->eClient->indices()->create([
            'index' => $indexName
        ]);
    }

    public function deleteIndex(string $indexName)
    {
        $this->eClient->indices()->delete([
            'index' => $indexName
        ]);
    }

    public function bulk(string $indexName, array $books)
    {
        $params = ['body' => []];
        foreach ($books as $book) {
            $params['body'][] = ['index' => ['_index' => $indexName]];
            $params['body'][] = $book;
        }

        if (!empty($params['body'])) {
            $this->eClient->bulk($params);
        }
    }

    public function search(string $indexName, string $search)
    {
        $params = [
            'index' => $indexName,
            'body'  => [
                'query' => [
                    'multi_match' => [
                        'query' => $search,
                        'fields' => ['title', 'category'],
                        'fuzziness' => 'AUTO',
                    ]
                ]
            ]
        ];

        $response = $this->eClient->search($params);

        return $response->asArray()['hits']['hits'] ?? [];
    }
}
