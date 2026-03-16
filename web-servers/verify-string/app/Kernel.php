<?php

declare(strict_types=1);

namespace App;

use Emelyanenko\BracketValidator\Service\BracketValidatorService;
use Emelyanenko\BracketValidator\Exceptions\InvalidCountException;

class Kernel
{
    private BracketValidatorService $validator;

    public function __construct()
    {
        $this->validator = new BracketValidatorService();
    }

    public function run(): string
    {
        header('Content-Type: text/plain; charset=utf-8');
        
        try {
            $input = $_POST['string'] ?? '';
            $this->validator->validate($input);
            http_response_code(200);
            return "200 OK: Всё хорошо";
        } catch (InvalidCountException $e) {
            http_response_code(400);
            return "400 Bad Request: " . $e->getMessage();
        } catch (\Throwable $e) {
            http_response_code(500);
            return "Ошибка на стороне сервера." . $e->getMessage();
        }
    }
}
