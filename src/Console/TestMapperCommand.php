<?php

declare(strict_types=1);

namespace App\Console;

use App\Example\Pet;
use App\Example\PetRepository;
use App\Example\Profile;
use App\Example\ProfileRepository;
use App\Example\User;
use App\Example\UserRepository;
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
        private readonly UserRepository $users,
        private readonly ProfileRepository $profiles,
        private readonly PetRepository $pets,
    ) {
        parent::__construct();
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $user = new User(null, 'Petya', 'test@mail.ru');
        $this->users->save($user);

        $io->success("Сохранен: {$user->id} {$user->name} {$user->email}");

        $profile = new Profile(null, 1, 'test');
        $this->profiles->save($profile);
        $io->success("Сохранен профиль: {$profile->id} {$profile->userId} {$profile->login}");

        $pet = new Pet(null, 1, 'cat');
        $this->pets->save($pet);
        $io->success("Сохранен питомец: {$pet->id} {$pet->userId} {$pet->type}");

        $pet = new Pet(null, 1, 'dog');
        $this->pets->save($pet);
        $io->success("Сохранен питомец: {$pet->id} {$pet->userId} {$pet->type}");


        $user = $this->users->find(1);
        $profile = $user->profile;
        $io->success("Профиль ленивый: {$profile->id} {$profile->userId} {$profile->login}");

        $pets = $user->pets;
        foreach ($pets as $pet) {
            $io->success("Питомец ленивый: {$pet->id} {$pet->userId} {$pet->type}");
        }

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


