<?php

declare(strict_types=1);

namespace App\Console;

use App\DataMapper\User;
use App\DataMapper\UserRepository;
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
        $user = $this->users->find(1);

        $io->success("{$user->id} {$user->name} {$user->email}");

        return Command::SUCCESS;
    }
}


