<?php

namespace App\Infrastructure;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;

class ElasticsearchConnection
{
    public function create(): Client
    {
        $host = getenv('ES_HOST') ?: 'localhost:9200';

         if (!str_starts_with($host, 'http')) {
            $host = 'http://' . $host;
        }

        return ClientBuilder::create()->setHosts([$host])->build();
    }
}
