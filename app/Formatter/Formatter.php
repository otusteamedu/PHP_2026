<?php

namespace App\Formatter;

class Formatter
{
    public static function render(array $result): string
    {
        if (empty($result)) {
            return "Ничего не найдено" . PHP_EOL;
        }

        $output = str_pad('Название', 40, " ", STR_PAD_BOTH) . ' | ';
        $output .= str_pad('Категория', 20, " ", STR_PAD_BOTH) . ' | ';
        $output .= str_pad('Цена', 15, " ", STR_PAD_BOTH) . ' | ';
        $output .= str_pad('На складе', 15, " ", STR_PAD_BOTH) . ' | ' . PHP_EOL;

        $output .= str_repeat('-', 90) . PHP_EOL;

        foreach ($result as $item) {
            $source = $item['_source'];
            $output .= str_pad($source['title'], 40, " ", STR_PAD_BOTH) . ' | ';
            $output .= str_pad($source['category'], 20, " ", STR_PAD_BOTH) . ' | ';
            $output .= str_pad($source['price'] . ' руб.', 10, " ", STR_PAD_BOTH) . ' | ';
            $output .= $source['stock'] . PHP_EOL;
        }
        return $output;
    }
}
