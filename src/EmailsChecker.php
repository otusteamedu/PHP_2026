<?php

declare(strict_types=1);

namespace App;
final class EmailsChecker
{
    public function __construct(
        private readonly EmailValidator $emailValidator,
    ) {
    }

    /**
     * @param string[] $emails
     */
    public function check(array $emails): bool
    {
        if (empty($emails)) {
            return false;
        }
        return array_all($emails, fn($email) => $this->emailValidator->validate($email));
    }
}
