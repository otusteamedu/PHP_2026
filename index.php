<?php

declare(strict_types=1);

use App\Handler;
use App\Http\StringRequest;

require(__DIR__ . '/vendor/autoload.php');

$request = StringRequest::fromGlobals();
$response = new Handler()->handle($request);
$response->send();
