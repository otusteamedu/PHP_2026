<?php

header('Content-Type: application/json');

session_start();

if (!isset($_SESSION['visits'])) {
    $_SESSION['visits'] = 1;
    $_SESSION['HOSTNAME'] = $_SERVER['HOSTNAME'];
    $message = "Welcome! This is your first visit. "
        . "Session started at container: " . $_SESSION['HOSTNAME'] . ". "
        . "Current container is: " . $_SERVER['HOSTNAME'] . ".";
} else {
    $_SESSION['visits']++;
    $message = "Welcome back! This is visit #" . $_SESSION['visits'] . ". "
        . "Session started at container: " . ($_SESSION['HOSTNAME'] ?? 'unknown') . ". "
        . "Current container is: " . $_SERVER['HOSTNAME'] . ".";
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 405,
        'error' => 'Method not allowed. Please use POST.',
        'session_data' => $message,
    ]);
    exit;
}

if (!isset($_POST['string'])) {
    http_response_code(422);
    echo json_encode([
        'status' => 422,
        'error' => 'Missing required parameter: string',
        'session_data' => $message,
    ]);
    exit;
}

$string = $_POST['string'];

function checkString(string $string): array
{
    if (!preg_match('/^[()]+$/', $string)) {
        return [
            'valid' => false,
            'status' => 422,
            'error' => 'String can only contain parentheses characters: ( and )'
        ];
    }

    $stack = [];

    for ($i = 0; $i < strlen($string); $i++) {
        if ($string[$i] === '(') {
            $stack[] = $string[$i];
        } elseif ($string[$i] === ')') {
            if (empty($stack)) {
                return [
                    'valid' => false,
                    'status' => 400,
                    'error' => 'Unmatched closing parenthesis at ' . $i
                ];
            }

            if ($stack[count($stack) - 1] === '(') {
                array_pop($stack);
            } else {
                $stack[] = $string[$i];
            }
        }
    }

    if (count($stack) > 0) {
        return [
            'valid' => false,
            'status' => 400,
            'error' => 'Unmatched parentheses: ' . count($stack) . ' remaining'
        ];
    }

    return [
        'valid' => true,
        'status' => 200,
        'message' => 'String check was passed'
    ];
}


$result = checkString($string);

http_response_code($result['status']);

if ($result['valid']) {
    echo json_encode([
        'status' => $result['status'],
        'info' => $result['message'],
        'session_data' => $message ?? $_SERVER['HOSTNAME'],
    ]);
} else {
    echo json_encode([
        'status' => $result['status'],
        'error' => $result['error'],
        'session_data' => $message ?? $_SERVER['HOSTNAME'],
    ]);
}

