<?php

declare(strict_types=1);

namespace App\Console;

use App\Domain\User;
use App\Domain\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test:mapper'
)]
class TestMapperCommand extends Command
{
    public function __construct(
        private readonly UserRepository $users
    ) {
        parent::__construct();
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $user = new User(null, 'Petya', 'test@mail.ru');
        $this->users->save($user);

        $io->success("Сохранен: {$user->id} {$user->name} {$user->email}");

        $user = $this->users->find(1);
        $user->name = 'new Name';
        $this->users->save($user);

        $io->success("Обновлен: {$user->id} {$user->name} {$user->email}");

        $users = $this->users->findAll();
        foreach ($users as $user) {
            $io->success("{$user->id} {$user->name} {$user->email}");
        }
        $io->success('Всего найдено: ' . count($users));

        $users = $this->users->findByEmail('test@mail.ru');
        foreach ($users as $user) {
            $io->success("{$user->id} {$user->name} {$user->email}");
        }
        $io->success('Всего найдено по почте: ' . count($users));

        return Command::SUCCESS;
    }
}


