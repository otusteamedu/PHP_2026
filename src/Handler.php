<?php

declare(strict_types=1);

namespace App;

use AleksandKrasnyatov\BracketsChecker\BracketsChecker;
use App\Http\Request;
use App\Http\Response;
use Exception;
use Throwable;

final class Handler
{
    public function handle(Request $request): Response
    {
        try {
            return $this->dispatch($request);
        } catch (Exception $exception) {
            return new Response($exception->getCode(), $exception->getMessage());
        } catch (Throwable) {
            return new Response(500, 'Internal Server Error');
        }
    }

    private function dispatch(Request $request): Response
    {
        return $request
            |> $this->checkMethod(...)
            |> $this->checkPost(...)
            |> $this->checkLogic(...);
    }

    /**
     * @throws Exception
     */
    private function checkMethod(Request $request): Request
    {
        if (!$request->isPost()) {
            throw new Exception('Method Not Allowed', 405);
        }

        return $request;
    }

    /**
     * @throws Exception
     */
    private function checkPost(Request $request): Request
    {
        $string = $request->post('string');
        if (!is_string($string)) {
            throw new Exception('There is no string in POST', 400);
        }

        return $request;
    }

    private function checkLogic(Request $request): Response
    {
        $isValid = new BracketsChecker()->check((string) $request->post('string'));

        return $isValid
            ? new Response(200, 'Everything is ok')
            : new Response(400, 'String contains unbalanced brackets');
    }
}
