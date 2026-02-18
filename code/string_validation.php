<?php
header('Content-Type: application/json');

// Проверка на наличие параметра string в запросе
if (!isset($_POST['string'])) {
    http_response_code(422);
    echo json_encode([
        'error' => 'Параметр string не передан',
    ]);
    exit;
}

$input = $_POST['string'];

// Проверка на пустоту
if (empty($input)) {
    http_response_code(400);
    echo json_encode([
        'error' => 'Строка пуста',
    ]);
    exit;
}

// Проверка наличия сторонних символов
if (preg_match('/[^()]/', $input)) {
    http_response_code(400);
    echo json_encode([
        'message' => 'Строка содержит символы кроме ( и )',
    ]);
    exit;
}

$countLeftBracket = 0;
$countRigthBracket = 0;
for ($i = 0; $i < strlen($input); $i++) {
    $char = $input[$i];
    if ($char === '(') {
        $countLeftBracket++;
    } else {
        $countRigthBracket++;
    }

    // Проверка чтобы закрытых скобок не стало больше чем открытых
    if ($countLeftBracket < $countRigthBracket) {
        http_response_code(400);
        echo json_encode([
            'message' => 'Скобка закрылась, но не открылась!',
        ]);
        exit;
    }
}

// Проверка чтобы открытых скобок не было больше чем закрытых
if ($countLeftBracket > $countRigthBracket) {
    http_response_code(400);
    echo json_encode([
        'message' => 'Скобка открылась, но не закрылась!',
    ]);
    exit;
}

// Если кол-во скобок равно, то строка корректна
if ($countLeftBracket === $countRigthBracket) {
    http_response_code(200);
    echo json_encode([
        'message' => 'Строка корректна',
    ]);
    exit;
}

http_response_code(400);
echo json_encode([
    'message' => 'Упс, нам не удалось опеределить корректность строки.',
]);
