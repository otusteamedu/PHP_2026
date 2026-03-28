<?php
declare(strict_types=1);

require_once __DIR__ . '/SearchService.php';

$service = new SearchService();

try {
    if ($argc < 2) {
        echo "--- Инициализация системы поиска ---\n";
        $service->initIndex();

        $jsonPath = __DIR__ . '/books.json';
        if (!file_exists($jsonPath)) {
            throw new Exception("Файл books.json не найден.");
        }

        $books = json_decode(file_get_contents($jsonPath), true);
        foreach ($books as $book) {
            $service->importBook($book);
        }

        echo "Успешно: Индекс создан и наполнен.\n";
        exit;
    }

    // Параметры из CLI
    $queryText = (string)$argv[1];
    $maxPrice = isset($argv[2]) ? (float)$argv[2] : 2000.0;
    // Можно добавить 3-й аргумент для категории, если нужно:
    $category = isset($argv[3]) ? (string)$argv[3] : 'historical_novel'; 

    $response = $service->search($queryText, $maxPrice, $category);
    $hits = $response['hits']['hits'] ?? [];

    if (empty($hits)) {
        echo "Ничего не найдено.\n";
        exit;
    }

    // Вывод таблицы (оставляем старый printf-код...)
    echo "+--------------------------------+------------+----------+\n";
    printf("| %-30s | %-10s | %-8s |\n", "Название", "Цена", "Склад");
    echo "+--------------------------------+------------+----------+\n";

    foreach ($hits as $hit) {
        $book = $hit['_source'];
        printf("| %-30.30s | %-10s | %-8s |\n", 
            $book['title'], 
            number_format((float)$book['price'], 2) . "р", 
            $book['stock']
        );
    }
    echo "+--------------------------------+------------+----------+\n";

} catch (Exception $e) {
    fwrite(STDERR, "КРИТИЧЕСКАЯ ОШИБКА: " . $e->getMessage() . "\n");
    exit(1);
}

