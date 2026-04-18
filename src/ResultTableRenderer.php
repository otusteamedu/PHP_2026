<?php

declare(strict_types=1);

namespace App;

use League\CLImate\CLImate;

final readonly class ResultTableRenderer
{
    public function __construct(
        private CLImate $climate = new CLImate(),
    ) {}

    /**
     * @param Book[] $books
     */
    public function render(array $books): void
    {
        if ($books === []) {
            $this->climate->yellow('Ничего не найдено.');
            return;
        }

        $rows = [];
        foreach ($books as $index => $book) {
            $rows[] = [
                '#' => (string) ($index + 1),
                'SKU' => $book->sku,
                'Название' => $book->title,
                'Категория' => $book->category,
                'Цена (₽)' => $book->price,
                'Остатки' => $this->formatStock($book->stock),
            ];
        }

        $this->climate->table($rows);
    }

    /**
     * @param Stock[] $stocks
     */
    private function formatStock(array $stocks): string
    {
        return implode(', ', array_map(
            static fn (Stock $stock): string => sprintf('%s:%d', $stock->shop, $stock->stock),
            $stocks,
        ));
    }
}
