<?php

declare(strict_types=1);


class ElasticClient
{
    private const string BASE_SQL_URL = 'http://elastic:9200/_sql?format=json';
    public array $headers = [
        'Content-Type: application/json',
    ];

    /**
     * @return array{
     *     code : int,
     *     data : mixed
     * }
     * @throws Exception
     */
    public function post(): array
    {
        $data = ['query' => 'SELECT title, author, published_at FROM articles WHERE is_public = true LIMIT 5'];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => self::BASE_SQL_URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($data),
            CURLOPT_HTTPHEADER     => $this->headers,
            CURLOPT_TIMEOUT        => 10
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            throw new Exception('Ошибка сети: ' . curl_error($ch));
        }

        return [
            'code' => $httpCode,
            'data' => json_decode($response, true)
        ];
    }
}
