<?php

declare(strict_types=1);

use App\Domain\ValueObject\Email;
use App\Domain\ValueObject\TelegramChatId;
use App\Infrastructure\RabbitMq\Worker\Producer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(true, true, true);


$app->get('/', function (Request $request, Response $response): Response {
    $html = <<<'HTML'
        <!DOCTYPE html>
        <html lang="ru">
        <head>
            <meta charset="UTF-8">
            <title>Запрос на что-то долгое по email</title>
        </head>
        <body>
            <h1>Запрос на что-то долгое по email</h1>
            <form method="post" action="/email">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
                <label for="chatId">TelegramChatId <small>(числовой Id из Telegram бота @userinfobot)</small></label>
                <input type="number" id="chatId" name="chatId">
                <button type="submit">Отправить</button>
            </form>
        </body>
        </html>
    HTML;

    $response->getBody()->write($html);
    return $response;
});

$app->post('/email', function (Request $request, Response $response): Response {
    $data = $request->getParsedBody();
    $statusClass = 'success';
    $message = 'Запрос успешно отправлен!';

    try {
        $email = new Email($data['email'] ?? '');
        $chatId = empty($data['chatId'] ) ? null : new TelegramChatId((int) $data['chatId']);
        Producer::create()->publish($email, $chatId);
    } catch (InvalidArgumentException $exception) {
        $statusClass = 'error';
        $message = 'Указаны не корректные данные: ' . $exception->getMessage();
    }

    $html = <<<HTML
        <!DOCTYPE html>
        <html lang="ru">
        <head>
            <meta charset="UTF-8">
            <title>Ответ по запросу</title>
            <style>
                .success { color: green; }
                .error { color: crimson; }
            </style>
        </head>
        <body>
            <h1>Ответ по запросу</h1>
            <p class="{$statusClass}">{$message}</p>
            <p><a href="/">Вернуться к форме</a></p>
        </body>
        </html>
    HTML;

    $response->getBody()->write($html);
    return $response;
});

$app->run();
