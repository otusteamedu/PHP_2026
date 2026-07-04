<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Action\OpenApi;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final readonly class DocsAction
{
    public function __construct(
        private Twig $twig
    ) {
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function __invoke(Request $request, Response $response): Response
    {
        return $this->twig->render($response, 'docs/swagger.html.twig');
    }
}
