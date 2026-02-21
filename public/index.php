<?php

require __DIR__ . '/../vendor/autoload.php';

use App\BracketValidator;

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new \RuntimeException('Only POST allowed');
    }

    $string = $_POST['string'] ?? '';

    $validator = new BracketValidator();
    $result = $validator->validate($string);

    if ($result) {
        http_response_code(200);
        echo "Everything is correct";
    } else {
        http_response_code(400);
        echo "Brackets is invalid";
    }

} catch (Throwable $e) {
    http_response_code(400);
    echo $e->getMessage();
}
