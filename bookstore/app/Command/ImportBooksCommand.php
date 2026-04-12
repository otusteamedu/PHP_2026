<?php
declare(strict_types=1);

namespace App\Command;

use App\Infrastructure\BookIndexService;
use App\Infrastructure\BookImport;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

#[AsCommand(
    name: 'books:import',
    description: 'Создаем index в Elasticsearch и импортируем данные из файла',
)]
final class ImportBooksCommand extends Command
{
    public function __construct(
        private readonly BookIndexService $service,
        private readonly BookImport $importer,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь до файла с данными'); 
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string)$input->getArgument('file');

        try {            
            $this->service->createIndex();            
                     
            $count = $this->importer->import($file);
            
            $output->writeln("Успешно записано документов: {$count}");
            
            return Command::SUCCESS;
        } catch (Throwable $e) {            
            $output->writeln("Ошибка импорта: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}