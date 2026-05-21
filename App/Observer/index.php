<?php

require __DIR__ . '/../../vendor/autoload.php';

use App\Observer\Publishers\Publisher;
use App\Observer\Subscribers\EmailSender;
use App\Observer\Subscribers\Logger;
use App\Observer\Subscribers\UserBonus;
use App\Observer\UseCase\CreateUserUseCase;

$publisher = new Publisher;
$publisher->subscribe(new EmailSender);
$publisher->subscribe(new Logger);
$publisher->subscribe(new UserBonus);

if (rand(0, 1) === 1) {
    $publisher->unsubscribe(new UserBonus);
}
$useCase = new CreateUserUseCase($publisher);
$useCase();
