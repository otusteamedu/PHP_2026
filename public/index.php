<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$handler = new \Aharutyunyan\Hw\Interfaces\Handler\EmailValidationHandler();
$result = $handler->handle();

echo '<pre>';
print_r($result);
echo '</pre>';

//$composite = new CompositeValidator();
//
//$composite->add(
//    new SyntaxValidator()
//);
//
//$composite->add(
//    new DomainValidator()
//);
//
//$composite->add(
//    new DisposableEmailValidator()
//);
//
//$result = $composite->validate(new Email('test@mailinator.com'));
//
//echo '<pre>';
//print_r($result);
//echo '</pre>';