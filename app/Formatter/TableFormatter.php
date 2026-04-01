<?php

declare(strict_types=1);

namespace App\Formatter;

class TableFormatter
{
    public function render(array $results): string
    {
        if (empty($results)) {
            return "Ничего не найдено.\n";
        }

        $out = '';
        foreach ($results as $i => $book) {
            $out .= sprintf(
                "%d. %s [%s] — %s руб., склад: %d\n",
                $i + 1,
                $book['title'] ?? '',
                $book['category'] ?? '',
                $book['price'] ?? '',
                $book['stock'] ?? 0,
            );
        }

        return $out;
    }
}
