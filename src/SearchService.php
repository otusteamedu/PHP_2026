<?php
declare(strict_types=1);

class SearchService {
    private string $host = "http://evgeny87-elasticsearch:9200";

    public function initIndex(): void {
        // 1. Удаляем индекс
        $this->request("/books", "DELETE");

        // 2. Создаем с жестко заданным JSON (чтобы исключить ошибки массивов)
        $json = '{
          "settings": {
            "analysis": {
              "filter": {
                "russian_stop": { "type": "stop", "stopwords": "_russian_" },
                "russian_stemmer": { "type": "stemmer", "language": "russian" }
              },
              "analyzer": {
                "ru_analyzer": {
                  "tokenizer": "standard",
                  "filter": [ "lowercase", "russian_stop", "russian_stemmer" ]
                }
              }
            }
          },
          "mappings": {
            "properties": {
              "title": { "type": "text", "analyzer": "ru_analyzer" },
              "category": { "type": "keyword" },
              "price": { "type": "float" },
              "stock": { "type": "integer" }
            }
          }
        }';

        $response = $this->request("/books", "PUT", $json);
        
        if (isset($response['error'])) {
            throw new Exception("Ошибка ES: " . ($response['error']['reason'] ?? json_encode($response['error'])));
        }
    }

    public function search(string $query, float $maxPrice, ?string $category = null): array {
        $filter = [
            ["range" => ["price" => ["lte" => $maxPrice]]],
            ["range" => ["stock" => ["gt" => 0]]]
        ];
        if ($category) {
            $filter[] = ["term" => ["category" => $category]];
        }

        $body = [
            "query" => [
                "bool" => [
                    "must" => [
                        ["match" => ["title" => ["query" => $query, "fuzziness" => "AUTO"]]]
                    ],
                    "filter" => $filter
                ]
            ]
        ];

        return $this->request("/books/_search", "POST", json_encode($body));
    }

    public function importBook(array $book): void {
        $this->request("/books/_doc?refresh=wait_for", "POST", json_encode($book));
    }

    private function request(string $path, string $method, $payload = null): array {
        $ch = curl_init($this->host . $path);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        if ($payload) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        }
        $res = curl_exec($ch);
        curl_close($ch);
        return json_decode((string)$res, true) ?? [];
    }
}
