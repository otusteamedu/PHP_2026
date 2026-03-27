<?php
declare(strict_types=1);

class BookstoreApp {
    private string $esUrl = "http://evgeny87-elasticsearch:9200";
    private string $index = "books";

    public function run(int $argc, array $argv): void {
        if ($argc < 2) {
            $this->init();
            echo "Инициализация завершена. Пример поиска: php index.php 'рыцОри' 2000\n";
            return;
        }
        $query = (string)($argv[1] ?? "рыцОри");
        $maxPrice = (float)($argv[2] ?? 2000.0);
        $this->search($query, $maxPrice);
    }

    private function init(): void {
        echo "--- Настройка индекса ---" . PHP_EOL;
        
        $ch = curl_init($this->esUrl . "/" . $this->index);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "HEAD");
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $exists = (curl_getinfo($ch, CURLINFO_HTTP_CODE) === 200);
        curl_close($ch);

        if ($exists) {
            $this->request("/" . $this->index, "DELETE");
            echo "Старый индекс удален." . PHP_EOL;
        }

        $settings = [
            "settings" => [
                "analysis" => [
                    "analyzer" => [
                        "ru_analyzer" => ["type" => "russian"]
                    ]
                ]
            ],
            "mappings" => [
                    "category" => ["type" => "keyword"],
                    "price" => ["type" => "float"],
                    "stock" => ["type" => "integer"]
                ]
            ];

        $res = $this->request("/" . $this->index, "PUT", $settings);
        
        if (isset($res["acknowledged"])) {
            echo "Индекс создан успешно." . PHP_EOL;
        }

        $jsonFile = __DIR__ . "/books.json";
        if (file_exists($jsonFile)) {
            $books = json_decode((string)file_get_contents($jsonFile), true);
            if (is_array($books)) {
                $count = 0;
                foreach ($books as $book) {
                    $this->request("/" . $this->index . "/_doc", "POST", $book);
                    $count++;
                }
                echo "Импортировано книг: " . $count . PHP_EOL;
            }
        }
    }

    private function search(string $query, float $maxPrice): void {
        $body = [
            "query" => [
                "bool" => [
                    "must" => [
                        ["match" => ["title" => ["query" => $query, "fuzziness" => "AUTO"]]]
                    ],
                    "filter" => [
                        ["range" => ["price" => ["lte" => $maxPrice]]],
                        ["range" => ["stock" => ["gt" => 0]]]
                    ]
                ]
            ]
        ];

        $res = $this->request("/" . $this->index . "/_search", "POST", $body);
        
        echo "\nРезультаты для: " . $query . " (до " . $maxPrice . " руб.)\n";
        echo str_repeat("=", 60) . "\n";
        printf("%-35s | %-10s | %s\n", "Название", "Цена", "Склад");
        echo str_repeat("-", 60) . "\n";

        $hits = $res["hits"]["hits"] ?? [];
        if (empty($hits)) {
            echo "Ничего не найдено.\n";
        }

        foreach ($hits as $hit) {
            $b = $hit["_source"];
            $title = mb_strimwidth((string)$b["title"], 0, 33, "...");
            printf("%-35s | %-10.2f | %d\n", $title, (float)$b["price"], (int)$b["stock"]);
        }
    }

    private function request(string $path, string $method, array $data = []): array {
        $ch = curl_init($this->esUrl . $path);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);
        return json_decode((string)$res, true) ?: [];
    }
}

$app = new BookstoreApp();
$app->run($argc, $argv);
