<?php

declare(strict_types=1);

use App\Handler;
use App\Http\Request;

require(__DIR__ . '/vendor/autoload.php');

$request = Request::create();
$response = new Handler()->handle($request);
$response->send();
