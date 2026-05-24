<?php

namespace App\ObjectValues;

use InvalidArgumentException;

class Email
{

    public function __construct(private string $email)
    {
        if (strlen($email) > 254) {
            throw new InvalidArgumentException('Email cannot be longer than 254 characters');
        }
        if (strpos($email, '@') === false) {
            throw new InvalidArgumentException('Email must contain at least one @ symbol');
        }
        if (strpos($email, '@') !== strrpos($email, '@')) {
            throw new InvalidArgumentException('Email must contain only one @ symbol');
        }
        if (strpos(explode('@', $email)[1], '.') !== strrpos(explode('@', $email)[1], '.')) {
            throw new InvalidArgumentException('Email must contain only one dot after the @ symbol');
        }
        if (strpos($email, ' ') !== false) {
            throw new InvalidArgumentException('Email cannot contain spaces');
        }
        if (strpos($email, '.') === false) {
            throw new InvalidArgumentException('Email must contain at least one dot');
        }
        if (strpos($email, '..') !== false) {
            throw new InvalidArgumentException('Email cannot contain consecutive dots');
        }
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
