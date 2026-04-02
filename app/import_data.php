<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Search\BookRepository;

$host = getenv('ES_HOST');
$index = getenv('ES_INDEX');
$file = __DIR__ . '/../data/books.json';

if (!file_exists($file)) {
    fwrite(STDERR, "Файл данных не найден: $file\n");
    exit(1);
}

try {
    $books = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
} catch (JsonException $e) {
    fwrite(STDERR, "Ошибка декодирования JSON из $file: " . $e->getMessage() . "\n");
    exit(1);
}

if (!is_array($books)) {
    fwrite(STDERR, "Ошибка чтения JSON из $file\n");
    exit(1);
}

try {
    $repo = new BookRepository($host, $index);

    echo "Создание индекса...\n";
    $repo->createIndex();

    echo "Загрузка " . count($books) . " книг...\n";
    $repo->bulkIndex($books);
} catch (Exception $e) {
    throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
}

echo "Готово!";
