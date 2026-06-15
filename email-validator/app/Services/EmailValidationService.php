<?php

declare(strict_types=1);

namespace App\Services;

class EmailValidationService
{
    const MAX_EMAILS_PER_REQUEST = 10;

    private EmailValidatorFabric $validator;
    private RequestParser $request;
    private ResponseParser $response;

    public function __construct()
    {
        $this->validator = new EmailValidatorFabric([
            new EmailValidation(),
            new MxValidation(),
        ]);

        $this->request = new RequestParser(self::MAX_EMAILS_PER_REQUEST);
        $this->response = new ResponseParser();
    }

    public function validate(): void
    {
        try {
            $emails = $this->request->parse(file_get_contents('php://input'));

            $results = [];

            foreach ($emails as $email) {
                $validation = $this->validator->validate($email);

                $results[] = [
                    'email'  => $email,
                    'status' => $validation->isValid ? 'valid' : 'invalid',
                    'message' => $validation->message
                ];
            }

            $this->response->parse(200, [
                'success' => true,
                'results' => $results
            ]);
        } catch (\Throwable $e) {

            $this->response->parse(
                $e->getCode() ?: 400,
                [
                    'success' => false,
                    'error'   => $e->getMessage()
                ]
            );
        }
    }
}
