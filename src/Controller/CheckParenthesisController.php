<?php

declare(strict_types=1);

namespace AHarutyunyan\Hw4\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class CheckParenthesisController {

    public function check(Request $request, Response $response): Response
    {
        session_start();

        $parsedBody = $request->getParsedBody() ?? [];

        $string = $parsedBody['string'] ?? '';

        if (empty($string)) {
            return $this->badResponse($response, 'Строка пустая');
        }

        $countOpen = substr_count($string, '(');
        $countClose = substr_count($string, ')');

        if (!isset($_SESSION['checks'])) {
            $_SESSION['checks'] = 0;
        }
        $_SESSION['checks']++;

        if ($countOpen === $countClose) {
            $data = [
                'message' => 'всё хорошо )',
                'checks_done' => $_SESSION['checks']
            ];
            $response->getBody()->write(json_encode($data));

            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        }

        return $this->badResponse($response);
    }

    private function badResponse(Response $response, string $msg = 'всё плохо ('): Response
    {
        $data = ['message' => $msg];
        $response->getBody()->write(json_encode($data));

        return $response->withHeader('Content-Type', 'application/json')
            ->withStatus(400);
    }
}