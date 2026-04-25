<?php

declare(strict_types=1);

namespace App\Infrastructure\Console;

use App\Application\UseCase\ClearEvents\Handler;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:event:clear',
    description: 'Очищает все события в Redis',
)]
class ClearEventCommand extends Command
{
    public function __construct(
        private readonly Handler $handler
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        ($this->handler)();

        $io->success('Все события успешно очищены.');

        return Command::SUCCESS;
    }
}
