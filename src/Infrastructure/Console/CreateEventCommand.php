<?php

declare(strict_types=1);

namespace App\Infrastructure\Console;

use App\Application\UseCase\CreateEvent\Handler;
use App\Application\UseCase\CreateEvent\Request;
use App\Infrastructure\Console\Helper\ConditionParser;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:event:create',
    description: 'Создает новое аналитическое событие',
)]
class CreateEventCommand extends Command
{
    public function __construct(
        private readonly Handler $handler
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('Эта команда позволяет создать событие в интерактивном режиме.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Создание нового аналитического события');

        $event = $io->ask('Введите название события', null, function ($answer) {
            if (empty($answer)) {
                throw new \RuntimeException('Название события не может быть пустым.');
            }
            return $answer;
        });

        $priority = $io->ask('Введите приоритет события (целое число)', '1', function ($answer) {
            if (!is_numeric($answer)) {
                throw new \RuntimeException('Приоритет должен быть числом.');
            }
            return (int) $answer;
        });

        $conditionsString = $io->ask('Введите условия в формате param1=1,param2=2', null, function ($answer) {
            if (empty($answer)) {
                throw new \RuntimeException('Условия не могут быть пустыми.');
            }
            return $answer;
        });

        try {
            $conditions = new ConditionParser()->parseConditions($conditionsString);

            $request = new Request($priority, $conditions, $event);
            ($this->handler)($request);

            $io->success(sprintf('Событие "%s" успешно создано.', $event));

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $io->error('Ошибка при создании события: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
