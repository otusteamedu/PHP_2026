<?php

declare(strict_types=1);

namespace App;

use Exception;

class ElasticClient
{
    public const string ACTION_SQL = '_sql';
    public const string ACTION_OTUS_SHOP = 'otus-shop';
    private const string BASE_URL = 'http://localhost:9200/';


    /**
     * @return array{
     *     code : int,
     *     data : mixed
     * }
     * @throws Exception
     */
    public function put(string $action, string $data): array
    {
        $options = [
            CURLOPT_URL => self::BASE_URL . $action,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => $data,
        ];

        return $this->send($options);
    }

    /**
     * @return array{
     *     code : int,
     *     data : mixed
     * }
     * @throws Exception
     */
    public function post(string $action, string $data): array
    {
        $options = [
            CURLOPT_URL => self::BASE_URL . $action,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
        ];

        return $this->send($options);
    }

    /**
     * @return array{
     *     code : int,
     *     data : mixed
     * }
     * @throws Exception
     */
    private function send(array $options = []): array
    {
        $commonOptions = [
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT => 10,
            CURLOPT_RETURNTRANSFER => true,
        ];

        $options += $commonOptions;

        $ch = curl_init();
        curl_setopt_array($ch, $options);

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
