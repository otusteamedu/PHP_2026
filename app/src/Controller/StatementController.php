<?php

declare(strict_types=1);

namespace App\Controller;

use App\Amqp\Connection;
use App\Database\Connection as DatabaseConnection;
use App\Enum\StatementStatus;
use App\Mail\MailerFactory;
use App\Mail\StatementNotificationService;
use App\Repository\StatementRequestRepository;
use App\Worker\Publisher;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class StatementController
{
    private StatementRequestRepository $repository;
    private StatementNotificationService $notifications;

    public function __construct()
    {
        $this->repository = new StatementRequestRepository(DatabaseConnection::fromEnv());
        $this->notifications = new StatementNotificationService(MailerFactory::create());
    }

    public function create(Request $request, Response $response): Response
    {
        $data = (array)$request->getParsedBody();

        $email = trim((string)($data['email'] ?? ''));
        $dateFrom = trim((string)($data['date_from'] ?? ''));
        $dateTo = trim((string)($data['date_to'] ?? ''));

        $errors = $this->validate($email, $dateFrom, $dateTo);
        if ($errors !== []) {
            $response->getBody()->write(json_encode(['errors' => $errors], JSON_UNESCAPED_UNICODE));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(422);
        }

        $id = $this->repository->create($email, $dateFrom, $dateTo);

        $this->notifications->sendAccepted($email, $dateFrom, $dateTo, $id);

        $publisher = new Publisher(
            connection: Connection::fromEnv(),
            exchange: $_ENV['RABBITMQ_EXCHANGE'] ?: 'statements',
            queue: $_ENV['RABBITMQ_QUEUE_NEW'] ?: 'statement_new',
        );

        $publisher->publish(json_encode(['id' => $id], JSON_UNESCAPED_UNICODE));

        $response->getBody()->write(json_encode([
            'id' => $id,
            'status' => StatementStatus::New->value,
            'status_label' => StatementStatus::New->label(),
            'message' => 'Запрос принят и поставлен в очередь на обработку',
        ], JSON_UNESCAPED_UNICODE));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(201);
    }

    public function getStatus(Request $request, Response $response, array $args): Response
    {
        $id = trim((string)($args['id'] ?? ''));
        if ($id === '') {
            $response->getBody()->write(json_encode(['error' => 'ID обязателен'], JSON_UNESCAPED_UNICODE));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        $statement = $this->repository->findById($id);
        if ($statement === null) {
            $response->getBody()->write(json_encode(['error' => 'Запрос не найден'], JSON_UNESCAPED_UNICODE));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);
        }

        $status = StatementStatus::tryFromString($statement['status']) ?? StatementStatus::New;

        $response->getBody()->write(json_encode([
            'id' => $statement['id'],
            'email' => $statement['email'],
            'date_from' => $statement['date_from'],
            'date_to' => $statement['date_to'],
            'status' => $status->value,
            'status_label' => $status->label(),
            'created_at' => $statement['created_at'],
            'updated_at' => $statement['updated_at'],
        ], JSON_UNESCAPED_UNICODE));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    /**
     * @return list<string>
     */
    private function validate(string $email, string $dateFrom, string $dateTo): array
    {
        $errors = [];

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Поле email обязательно и должно быть корректным адресом';
        }

        if (!$this->isValidDate($dateFrom)) {
            $errors[] = 'Поле date_from обязательно и должно быть датой в формате Y-m-d';
        }

        if (!$this->isValidDate($dateTo)) {
            $errors[] = 'Поле date_to обязательно и должно быть датой в формате Y-m-d';
        }

        if ($this->isValidDate($dateFrom) && $this->isValidDate($dateTo) && $dateFrom > $dateTo) {
            $errors[] = 'Дата date_from не может быть позже даты date_to';
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
