<?php

declare (strict_types = 1);

namespace App\Shared;

use App\Domain\ValidationRequest;

class RequestParser
{    
    public function parse(string $request): ValidationRequest
    {
        $input = json_decode($request, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception(RequestMessages::JSON_ERROR->value, 400);
        }

        if (! is_array($input) || ! isset($input['emails']) || ! is_array($input['emails'])) {
            throw new \Exception(RequestMessages::EMPTY_REQUEST_BODY->value, 422);
        }

        $emails = [];

        foreach ($input['emails'] as $email) {
            if (! is_string($email)) {
                continue;
            }

            $email = strtolower(trim($email));

            if ($email === '') {
                continue;
            }

            $emails[$email] = true;
        }        

        $reportEmail = '';

        if (isset($input['report_email']) && is_string($input['report_email'])) {
            $reportEmail = strtolower(trim($input['report_email']));
        }

        return new ValidationRequest(
            array_keys($emails),
            $reportEmail
        );
    }
}
