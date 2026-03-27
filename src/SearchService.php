<?php
declare(strict_types=1);

class SearchService {
    private string $host = "http://evgeny87-elasticsearch:9200";

    /**
     * Создание индекса с правильным маппингом и анализатором
     */
    public function initIndex(): void {
        // Удаляем старый индекс перед созданием нового
        $this->request("/books", "DELETE");

        $json = <<<JSON
        {
          "settings": {
            "analysis": {
              "analyzer": {
                "ru_analyzer": {
                  "tokenizer": "standard",
                  "filter": ["lowercase", "russian_stop", "russian_stemmer"]
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
        }
        JSON;

        $this->request("/books", "PUT", $json);
    }

    /**
     * Полнотекстовый поиск с фильтрами и опечатками
     */
    public function search(string $query, float $maxPrice): array {
        $json = <<<JSON
        {
          "query": {
            "bool": {
              "must": [
                {
                  "match": {
                    "title": {
                      "query": "$query",
                      "fuzziness": "AUTO"
                    }
                  }
                }
              ],
              "filter": [
                { "term":  { "category": "historical_novel" } },
                { "range": { "price": { "lte": $maxPrice } } },
                { "range": { "stock": { "gt": 0 } } }
              ]
            }
          }
        }
        JSON;

        return $this->request("/books/_search", "POST", $json);
    }

    /**
     * Метод для импорта одной книги
     */
    public function importBook(array $book): void {
        // Мы преобразуем массив в JSON-строку и отправляем в request
        $this->request("/books/_doc", "POST", json_encode($book));
    }

    /**
     * Универсальный метод для отправки запросов
     */
    private function request(string $path, string $method, string $jsonData = ""): array {
        $ch = curl_init($this->host . $path);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($jsonData) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);
        
        return json_decode((string)$res, true) ?? [];
    }
}
