<?php

declare(strict_types=1);

namespace App;

use AleksandKrasnyatov\BracketsChecker\BracketsChecker;
use App\Http\StringRequest;
use App\Http\Response;
use Exception;
use Throwable;

final class Handler
{
    public function handle(StringRequest $request): Response
    {
        try {
            return $this->dispatch($request);
        } catch (Exception $exception) {
            return new Response($exception->getCode(), $exception->getMessage());
        } catch (Throwable) {
            return new Response(500, 'Internal Server Error');
        }
    }

    private function dispatch(StringRequest $request): Response
    {
        return $request
            |> $this->checkMethod(...)
            |> $this->checkPost(...)
            |> $this->checkLogic(...);
    }

    /**
     * @throws Exception
     */
    private function checkMethod(StringRequest $request): StringRequest
    {
        if (!$request->isPost()) {
            throw new Exception('Method Not Allowed', 405);
        }

        return $request;
    }

    /**
     * @throws Exception
     */
    private function checkPost(StringRequest $request): StringRequest
    {
        if (empty($request->string)) {
            throw new Exception('There is no string in POST', 400);
        }

        return $request;
    }

    private function checkLogic(StringRequest $request): Response
    {
        $isValid = new BracketsChecker()->check($request->string);

        return $isValid
            ? new Response(200, 'Everything is ok')
            : new Response(400, 'String contains unbalanced brackets');
    }
}
