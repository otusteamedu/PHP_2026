<?php

declare(strict_types=1);

namespace App\Command;

use App\Domain\SearchDTO;
use App\Infrastructure\BookSearch;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

#[AsCommand(
    name: 'books:search',
    description: 'Поиск книг в Elasticsearch',
)]
final class SearchBooksCommand extends Command
{
    public function __construct(
        private readonly BookSearch $search,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('q', null, InputOption::VALUE_OPTIONAL, 'Search text')
            ->addOption('category', null, InputOption::VALUE_OPTIONAL, 'Category filter')
            ->addOption('max-price', null, InputOption::VALUE_OPTIONAL, 'Maximum price')
            ->addOption('in-stock', null, InputOption::VALUE_NONE, 'Only items in stock')
            ->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'Result limit', 10);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dto = new SearchDTO(
            query: $input->getOption('q') !== null ? (string)$input->getOption('q') : null,
            category: $input->getOption('category') !== null ? (string)$input->getOption('category') : null,
            maxPrice: $input->getOption('max-price') !== null ? (int)$input->getOption('max-price') : null,
            inStock: (bool)$input->getOption('in-stock'),
            limit: (int)$input->getOption('limit'),
        );

        try {
            $response = $this->search->search($dto);
            $hits = $response['hits']['hits'] ?? [];

            $total = $response['hits']['total']['value'] ?? 0;

            if ($total === 0) {
                $output->writeln('Ничего не найдено');
                return Command::SUCCESS;
            }

            $output->writeln("Найдено документов: {$total}. Показано: " . count($hits));

            $table = new Table($output);
            $table->setHeaders(['SKU', 'Название', 'Категория', 'Цена', 'Остаток', 'Score']);

            foreach ($hits as $hit) {
                $source = $hit['_source'] ?? [];

                $table->addRow([
                    $source['sku'] ?? '',
                    $source['title'] ?? '',
                    $source['category'] ?? '',
                    (string)($source['price'] ?? ''),
                    (string)($source['stock_total'] ?? 0),
                    isset($hit['_score']) ? number_format((float)$hit['_score'], 4) : '',
                ]);
            }

            $table->render();

            return Command::SUCCESS;
        } catch (Throwable $e) {

            $output->writeln("Ошибка при поиске: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}
