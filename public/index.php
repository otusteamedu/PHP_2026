<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Aharutyunyan\Hw\Infrastructure\Validator\CompositeValidator;
use Aharutyunyan\Hw\Infrastructure\Validator\Strategy\SyntaxValidator;
use Aharutyunyan\Hw\Infrastructure\Validator\Strategy\DisposableEmailValidator;
use Aharutyunyan\Hw\Domain\Email\ValueObject\Email;

$composite = new CompositeValidator();

$composite->add(
    new SyntaxValidator()
);

$composite->add(
    new DisposableEmailValidator()
);

$result = $composite->validate(new Email('test@mailinator.com'));

echo '<pre>';
print_r($result);
echo '</pre>';