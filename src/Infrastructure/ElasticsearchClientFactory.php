<?php

declare(strict_types=1);

namespace App\Infrastructure;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;

final class ElasticsearchClientFactory
{
    public static function create(): Client
    {
        return ClientBuilder::create()
            ->setHosts(['http://elastic:9200'])
            ->build();
    }
}
