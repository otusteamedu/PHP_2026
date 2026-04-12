<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Domain\SearchDTO;
use Elastic\Elasticsearch\Client;

class BookSearch
{
    public function __construct(
        private readonly Client $client,
        private readonly string $indexName,
    ) {}

    public function search(SearchDTO $dto): array
    {
        $must = [];
        $filter = [];

        if (!empty(trim($dto->query ?? ''))) {
            $must[] = [
                'multi_match' => [
                    'query' => $dto->query,
                    'fields' => ['title^3', 'category^1.5'],
                    'type' => 'best_fields',
                    'fuzziness' => 'AUTO',
                    'operator' => 'and',
                ],
            ];
        }

        if (!empty(trim($dto->category ?? ''))) {
            $filter[] = [
                'term' => [
                    'category.keyword' => $dto->category,
                ],
            ];
        }

        if ($dto->maxPrice !== null) {
            $filter[] = [
                'range' => [
                    'price' => ['lte' => $dto->maxPrice],
                ],
            ];
        }

        if ($dto->inStock) {
            $filter[] = [
                'range' => [
                    'stock_total' => ['gt' => 0],
                ],
            ];
        }

        $query = ['match_all' => new \stdClass()]; 
        
        if ($must !== [] || $filter !== []) {
            $query = ['bool' => []];
            if ($must !== [])   $query['bool']['must'] = $must;
            if ($filter !== []) $query['bool']['filter'] = $filter;
        }

        $params = [
            'index' => $this->indexName,
            'body' => [
                'size' => $dto->limit,
                'track_total_hits' => true,
                'query' => $query,
                'sort' => $this->buildSort($must !== []),
            ],
        ];

        return $this->client->search($params)->asArray();
    }

    private function buildSort(bool $hasTextQuery): array
    {
        $sort = [];
        if ($hasTextQuery) {
            $sort[] = ['_score' => ['order' => 'desc']];
        }

        $sort[] = ['price' => ['order' => 'asc']];
        return $sort;
    }
}
