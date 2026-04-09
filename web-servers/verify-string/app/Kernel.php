<?php

declare(strict_types=1);

namespace App;

use Emelyanenko\BracketValidator\Service\BracketValidatorService;
use Emelyanenko\BracketValidator\Exceptions\InvalidCountException;
use App\Handlers\RequestHandler;
use App\Handlers\ResponseHandler;

class Kernel
{
    private BracketValidatorService $validator;
    private RequestHandler $request;
    private ResponseHandler $response;

    public function __construct()
    {
        $this->validator = new BracketValidatorService();
        $this->request = new RequestHandler();
        $this->response = new ResponseHandler();
    }

    public function run(): void
    {
        try {
            $data = $this->request->getString();
            $this->validator->validate($data);
            $this->response->send(200, "200 OK: Всё хорошо");
        } catch (InvalidCountException $e) {
            $this->response->send(400, "400 Bad Request: " . $e->getMessage());
        } catch (\InvalidArgumentException $e) {
            $this->response->send(400, $e->getMessage());
        } catch (\Throwable $e) {
            $this->response->send(500, "Ошибка сервера");
        }
    }
}
