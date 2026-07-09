<?php

declare(strict_types=1);

namespace App\Controller;

use App\Amqp\Connection;
use App\Worker\Publisher;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class ReportRequestController
{
    public function create(Request $request, Response $response): Response
    {
        $data = (array)$request->getParsedBody();

        $email = trim((string)($data['email'] ?? ''));
        $from = trim((string)($data['from'] ?? ''));
        $to = trim((string)($data['to'] ?? ''));

        $errors = $this->validate($email, $from, $to);
        if ($errors !== []) {
            $response->getBody()->write(json_encode(['errors' => $errors], JSON_UNESCAPED_UNICODE));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(422);
        }

        $publisher = new Publisher(
            connection: Connection::fromEnv(),
            exchange: $_ENV['RABBITMQ_EXCHANGE'] ?? 'notifications',
            queue: $_ENV['RABBITMQ_QUEUE'] ?? 'mail_notifications',
        );

        $publisher->publish(json_encode([
            'email' => $email,
            'from' => $from,
            'to' => $to,
        ], JSON_UNESCAPED_UNICODE));

        $response->getBody()->write(json_encode([
            'status' => 'queued',
            'message' => 'Запрос принят и поставлен в очередь на обработку',
        ], JSON_UNESCAPED_UNICODE));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(202);
    }

    /**
     * @return list<string>
     */
    private function validate(string $email, string $from, string $to): array
    {
        $errors = [];

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Поле email обязательно и должно быть корректным адресом';
        }

        if (!$this->isValidDate($from)) {
            $errors[] = 'Поле from обязательно и должно быть датой в формате Y-m-d';
        }

        if (!$this->isValidDate($to)) {
            $errors[] = 'Поле to обязательно и должно быть датой в формате Y-m-d';
        }

        if ($this->isValidDate($from) && $this->isValidDate($to) && $from > $to) {
            $errors[] = 'Дата from не может быть позже даты to';
        }

        return $errors;
    }

    private function isValidDate(string $value): bool
    {
        if ($value === '') {
            return false;
        }

        $date = \DateTimeImmutable::createFromFormat('Y-m-d', $value);

        return $date !== false && $date->format('Y-m-d') === $value;
    }
}
