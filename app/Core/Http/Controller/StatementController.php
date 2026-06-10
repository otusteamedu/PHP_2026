<?php

declare(strict_types=1);

namespace App\Core\Http\Controller;

use App\Core\Http\HtmlResponse;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\View;
use App\Core\Queue\KafkaProducer;
use App\Statement\StatementRequest;
use Throwable;

final readonly class StatementController
{
    public function __construct(
        private KafkaProducer $producer,
    ) {
    }

    /** GET / - форма заказа выписки */
    public function form(): Response
    {
        return new HtmlResponse(View::render('form', ['error' => '']));
    }

    /** POST /statements - принять запрос и поставить в очередь */
    public function submit(Request $request): Response
    {
        $account = $request->getPostParam('account');
        $dateFrom = $request->getPostParam('date_from');
        $dateTo = $request->getPostParam('date_to');
        $email = $request->getPostParam('email');

        if ($account === '' || $dateFrom === '' || $dateTo === '' || $email === '') {
            return new HtmlResponse(
                View::render('form', ['error' => 'Заполните все поля формы.']),
                422,
            );
        }

        $statementRequest = new StatementRequest(
            id:          uniqid('stmt_', true),
            account:     $account,
            dateFrom:    $dateFrom,
            dateTo:      $dateTo,
            email:       $email,
            requestedAt: date(DATE_ATOM),
        );

        try {
            $this->producer->publish($statementRequest->toJson(), $statementRequest->id);
        } catch (Throwable $e) {
            return new HtmlResponse(
                View::render('form', ['error' => 'Не удалось поставить запрос в очередь: ' . $e->getMessage()]),
                503,
            );
        }

        return new HtmlResponse(View::render('accepted', [
            'id' => $statementRequest->id,
            'email' => $statementRequest->email,
        ]));
    }
}