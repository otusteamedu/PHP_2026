<?php

require __DIR__ . '/vendor/autoload.php';

use IvanSorochinskiy\Hw3\Str;

$str = new Str();

echo $str->toLower('ПрИвЕт!');
echo $str->toUpper('ПрИвЕт!');
echo $str->toLatin('Иван Сорочинский');
