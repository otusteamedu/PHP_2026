<?php
declare(strict_types=1);

class SearchService {
    private string $host = "http://evgeny87-elasticsearch:9200";

    public function initIndex(): void {
        $params = [
            "settings" => [
                "analysis" => [
                    "analyzer" => [
                        "ru_analyzer" => [
                            "tokenizer" => "standard",
                            "filter" => ["lowercase", "russian_stop", "russian_stemmer"]
                        ]
                    ]
                ]
            ],
            "mappings" =>,
                    "category" => ["type" => "keyword"],
                    "price" => ["type" => "float"],
                    "stock" => ["type" => "integer"]
                ]
            ]
        ];
        $this->request("/books", "PUT", $params);
    }

    public function search(string $query, float $maxPrice): array {
        $body = [
            "query" => [
                "bool" => [
                    "must" => [
                        ["match" => [
                            "title" => [
                                "query" => $query,
                                "fuzziness" => "AUTO" // Опечатки "рыцОри" -> "рыцари"
                            ]
                        ]]
                    ],
                    "filter" => [
                        ["range" => ["price" => ["lte" => $maxPrice]]],
                        ["range" => ["stock" => ["gt" => 0]]]
                    ]
                ]
            ]
        ];
        return $this->request("/books/_search", "POST", $body);
    }

    private function request(string $path, string $method, array $data = []): array {
        $ch = curl_init($this->host . $path);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);
        return json_decode($res, true) ?? [];
    }
}

