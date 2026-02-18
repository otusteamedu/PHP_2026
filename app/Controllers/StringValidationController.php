<?php

namespace App\Controllers;

use App\Responses\JsonResponse;
use App\Services\StringValidator;

class StringValidationController
{
    public function handleRequest(): string
    {
        $input = $_POST['string'] ?? null;

        $validator = new StringValidator();

        $result = $validator->validate($input);

        return (new JsonResponse($result['status'], $result['data']))->getJson();
    }
}
