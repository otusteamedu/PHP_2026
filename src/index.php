<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use evgeny87\BracketValidator\Validator;

// Архитектурно верный заголовок
header('Content-Type: text/plain; charset=utf-8');

try {
    // Явное приведение к строке для соблюдения strict_types
    $input = (string)($_POST['string'] ?? '');

    if ($input === '') {
        throw new \InvalidArgumentException("String parameter is missing or empty.");
    }

    $validator = new Validator();

    // Получаем имя текущего контейнера (php-1, php-2 или php-3)
    $nodeName = gethostname();

    if ($validator->isValid($input)) {
        http_response_code(200);
        echo "200 OK: Sequence is valid. Everything is good. [Processed by: {$nodeName}]";
    } else {
        http_response_code(400);
        echo "400 Bad Request: Sequence is invalid. Everything is bad. [Processed by: {$nodeName}]";
    }

} catch (\Throwable $e) {
    // В ТЗ сказано: 400 - валидное завершение при ошибке. Никаких die().
    http_response_code(400);
    echo "400 Bad Request: " . $e->getMessage() . " [Processed by: " . gethostname() . "]";
}
