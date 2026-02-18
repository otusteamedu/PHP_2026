<?php

namespace App\Services;

class StringValidator
{
    public function validate(?string $input): array
    {
        // 1. Проверка наличия параметра
        if ($input === null) {
            return [
                'status' => 422,
                'data' => ['error' => 'Параметр string не передан']
            ];
        }

        // 2. Проверка на пустоту
        if (empty($input)) {
            return [
                'status' => 400,
                'data' => ['error' => 'Строка пуста']
            ];
        }

        // 3. Проверка сторонних символов
        if (preg_match('/[^()]/', $input)) {
            return [
                'status' => 400,
                'data' => ['message' => 'Строка содержит символы кроме ( и )']
            ];
        }

        // 4. Подсчёт скобок
        $left = 0;
        $right = 0;

        for ($i = 0; $i < strlen($input); $i++) {
            $char = $input[$i];
            if ($char === '(') {
                $left++;
            } else {
                $right++;
            }

            // 5. Ранний выход: закрывающих больше
            if ($right > $left) {
                return [
                    'status' => 400,
                    'data' => ['message' => 'Скобка закрылась, но не открылась!']
                ];
            }
        }

        // 6. Проверка баланса скобок
        if ($left !== $right) {
            return [
                'status' => 400,
                'data' => ['message' => 'Скобка открылась, но не закрылась!']
            ];
        }

        // 7. Успех
        return [
            'status' => 200,
            'data' => ['message' => 'Строка корректна']
        ];
    }
}