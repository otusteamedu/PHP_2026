<?php

declare(strict_types=1);

namespace App\Infrastructure\Console;

use App\Application\UseCase\GetEvent\Handler;
use App\Application\UseCase\GetEvent\Request;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:event:find',
    description: 'Получает наиболее приоритетное событие по условиям',
)]
class FindEventCommand extends Command
{
    public function __construct(
        private readonly Handler $handler
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('Эта команда позволяет получить наиболее приоритетное событие по условиям в интерактивном режиме.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Получение аналитического события');

        $conditionsString = $io->ask('Введите условия в формате param1=1,param2=2', null, function ($answer) {
            if (empty($answer)) {
                throw new \RuntimeException('Условия не могут быть пустыми.');
            }
            return $answer;
        });

        try {
            $conditions = new ConditionParser()->parseConditions($conditionsString);


            $request = new Request($conditions);
            $response = ($this->handler)($request);
            if (!empty($response)) {
                $io->success("Найдено событие: {$response->event}");
            } else {
                $io->success('Подходящих событий не найдено.');
            }

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $io->error('Ошибка при получении события: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
