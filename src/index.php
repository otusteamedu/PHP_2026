<?php
declare(strict_types=1);

require_once __DIR__ . '/SearchService.php';

$service = new SearchService();

// 1. Логика инициализации (если запуск без аргументов)
if ($argc < 2) {
    echo "--- Инициализация системы поиска ---\n";
    
    // Создаем индекс с маппингом
    $service->initIndex();
    
    // Загружаем данные из JSON
    $jsonPath = __DIR__ . '/books.json';
    if (!file_exists($jsonPath)) {
        die("Ошибка: Файл books.json не найден.\n");
    }

    $books = json_decode(file_get_contents($jsonPath), true);
    $count = 0;
    foreach ($books as $book) {
        // Простой POST запрос для добавления документа через тот же SearchService
        // Можно вынести в отдельный метод импорта, если нужно
        $service->importBook($book); 
        $count++;
    }

    echo "Успешно: Индекс создан, импортировано книг: $count\n";
    echo "Пример поиска: php index.php 'рыцОри' 2000\n";
    exit;
}

// 2. Логика поиска
$queryText = (string)$argv[1];
$maxPrice = isset($argv[2]) ? (float)$argv[2] : 2000.0;

echo "\nРезультаты поиска для: '$queryText' (до $maxPrice руб.)\n";

$response = $service->search($queryText, $maxPrice);
$hits = $response['hits']['hits'] ?? [];

if (empty($hits)) {
    echo "К сожалению, ничего не найдено.\n";
    exit;
}

// 3. Форматированный вывод таблицы
$mask = "| %-30.30s | %-10s | %-8s |\n";
$line = "+" . str_repeat("-", 32) . "+" . str_repeat("-", 12) . "+" . str_repeat("-", 10) . "+\n";

echo $line;
printf($mask, "Название", "Цена", "Склад");
echo $line;

foreach ($hits as $hit) {
    $book = $hit['_source'];
    printf($mask, 
        $book['title'], 
        number_format((float)$book['price'], 2) . "р", 
        $book['stock']
    );
}
echo $line;

