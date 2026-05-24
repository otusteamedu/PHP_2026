<?php

namespace App\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Services\EmailVerificationService;

class EmailVerificationController
{
    private EmailVerificationService $service;

    public function __construct()
    {
        $this->service = new EmailVerificationService;
    }

    public function __invoke(Request $request)
    {
        $data = $request->getJsonBody();

        if (isset($data['email'])) {
            return new Response(['data' => ['email' => $data['email'], 'is_valid' => $this->service->checkEmail($data['email'])]]);
        } elseif (isset($data['emails'])) {
            return new Response(['data' => $this->service->checkEmailList($data['emails'])]);
        } else {
            return new Response(['error' => 'Параметры email или emails не найдены.'], 422);
        }
    }
}
