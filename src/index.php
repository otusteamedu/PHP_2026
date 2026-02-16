<?php

declare(strict_types=1);

session_start();

if (!function_exists('badResponse')) {
    function badResponse (string $msg = 'всё плохо (') {
        http_response_code(400);

        header('Content-Type: application/json');

        echo json_encode([
            'message' => $msg
        ]);
        exit;
    }
}

$input = file_get_contents('php://input');

$data = json_decode($input, true);

$string = $_POST['string'] ?? $data['string'] ?? '';

if (empty($string)) {
    badResponse('Строка пустая');
}

$countOpen = substr_count($string, '(');
$countClose = substr_count($string, ')');

if (!isset($_SESSION['checks'])) {
    $_SESSION['checks'] = 0;
}
$_SESSION['checks']++;

if ($countOpen === $countClose) {
    http_response_code(200);

    header('Content-Type: application/json');

    echo json_encode([
        'message' => 'всё хорошо )',
        'checks_done' => $_SESSION['checks']
    ]);
    exit;
}

badResponse();

