<?php

declare(strict_types=1);

// function findMax(array $nums): int
// {
//     // Базовый случай: в массиве нет элементов
//     if (empty($nums)) {
//         return -1;
//     }

//     // Рекурсивный случай
//     $head = array_shift($nums); // "Отрываем голову" у массива
//     $tail = $nums;              // В $tail остаётся "хвост"

//     // Ищем самый большой элемент в остатке массива...
//     $maxElementInTail = findMax($tail);

//     // Допишите недостающий код
//     return $head > $maxElementInTail ? $head : $maxElementInTail;
// }


// // $max = findMax([1, 4, 2, 6, 6]);
// // echo $max;

// function getLength(string $str): int
// {
//     // Базовый случай: строка пустая
//     if ($str === '') {
//         return 0;
//     }

//     // Рекурсивный случай
//     $head = $str[0];         // "Отрываем голову" у строки
//     $tail = substr($str, 1); // В $tail остаётся "хвост"

//     // Шаг рекурсии: вычисляем длину остатка строки...
//     $tailLength = getLength($tail);

//     // todo Допишите недостающий код
//     return 1 + $tailLength;
// }

// // $len = getLength('vavavav');
// // echo $len;


// function sumDigits(int $num): int
// {
//     $lastDigitOfNum      = $num % 10;         // 1024 -> 4      5 -> 5
//     $numWithoutLastDigit = intdiv($num, 10);  // 1024 -> 102    5 -> 0

//     // todo: Базовый случай
//     if ($numWithoutLastDigit === 0) return 0;

//     // todo: Шаг рекурсии
//     return $lastDigitOfNum + sumDigits($numWithoutLastDigit);
// }

// // $sum = sumDigits(1024);
// // echo $sum;


class TreeNode
{
    function __construct(
        public int $val = 0,
        public ?TreeNode $left = null,
        public ?TreeNode $right = null
    ) {
    }
}

function sum(?TreeNode $node): int
{
    // Базовый случай: узел равен NULL
    if ($node === null) {
        return 0;
    }

    // Шаг рекурсии: вычисляем суммы поддеревьев...
    $leftSum = sum($node->left);
    $rightSum = sum($node->right);

    // ...и складываем их с текущим узлом
    return $node->val
        + $leftSum
        + $rightSum;
}

function checkTree(?TreeNode $root): bool
{
    $sum = sum($root);

    // return $root->val === (sum($root->left) + sum($root->right));
    return $sum / $root->val === 2;
}       



// class TreeNode
// {
//     function __construct(
//         public int $val = 0,
//         public ?TreeNode $left = null,
//         public ?TreeNode $right = null
//     ) {
//     }
// }

// function maxDepth(?TreeNode $root): int
// {
//     // Базовый случай: узел равен NULL
//     if ($root === null) {
//         return 0;
//     }

//     // Шаг рекурсии: вычисляем глубину для каждого из поддеревьев...
//     $leftDepth = maxDepth($root->left);
//     $rightDepth = maxDepth($root->right);

//     // ...смотрим, какое из поддеревьев "глубже"...
//     $maxDepth = max($leftDepth, $rightDepth);

//     return $maxDepth + 1;
// }      


// class TreeNode
// {
//     function __construct(
//         public int|string $val,
//         public ?TreeNode $left = null,
//         public ?TreeNode $right = null
//     ) {}
// }

// function evaluate(TreeNode $root): int
// {
//     // todo Граничный случай (узел содержит число)
//     if (is_numeric($root->val)) {
//         return $root->val;
//     }

//     // todo Шаг рекурсии (узел содержит операцию '+' или '*', которую нужно применить к двум поддеревьям)
//     if ($root->val === '+') {
//         return evaluate($root->left) + evaluate($root->right);
//     }

//     if ($root->val === '*') {
//         return evaluate($root->left) * evaluate($root->right);
//     }

//     return throw new \InvalidArgumentException('Не корректное значение узла');
// }
