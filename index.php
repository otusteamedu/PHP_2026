<?php
require 'vendor/autoload.php';

use AErmolenko\Calculator\Calculator;

$calculator = new Calculator();

echo $calculator->add(10, 5);
echo $calculator->subtract(10, 5);
echo $calculator->multiply(10, 5);
echo $calculator->divide(10, 5);