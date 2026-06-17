<?php

require_once 'ListNode.php';
require_once 'ListMerge.php';

$list1 = [1,2,3];
$list2 = [1,3,4];

$m = new ListMerge();

$r1 = $m->merge($list1, $list2);
var_dump($r1);

echo '<br>';

$r2 = $m->merge([], []);
var_dump($r2);

echo '<br>';

$r3 = $m->merge([], [0]);
var_dump($r3);