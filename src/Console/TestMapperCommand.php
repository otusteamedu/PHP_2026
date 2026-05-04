<?php

declare(strict_types=1);

namespace App\Console;

use App\Example\Pet;
use App\Example\PetRepository;
use App\Example\Profile;
use App\Example\ProfileRepository;
use App\Example\House;
use App\Example\HouseRepository;
use App\Example\User;
use App\Example\UserRepository;
use App\Example\UserPet;
use App\Example\UserPetRepository;
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
        private readonly HouseRepository $houses,
        private readonly UserPetRepository $userPets,
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

        $pet = new Pet(null, 'cat');
        $this->pets->save($pet);
        $io->success("Сохранен питомец: {$pet->id} {$pet->type}");

        $pet2 = new Pet(null, 'dog');
        $this->pets->save($pet2);
        $io->success("Сохранен питомец: {$pet2->id} {$pet2->type}");

        $userPet = new UserPet(null, 1, $pet->id);
        $this->userPets->save($userPet);
        $io->success("Связь user_pet: {$userPet->id} {$userPet->userId} {$userPet->petId}");

        $userPet = new UserPet(null, 1, $pet2->id);
        $this->userPets->save($userPet);
        $io->success("Связь user_pet: {$userPet->id} {$userPet->userId} {$userPet->petId}");

        $house = new House(null, 1, 'Moscow, Tverskaya 1');
        $this->houses->save($house);
        $io->success("Сохранен дом: {$house->id} {$house->userId} {$house->address}");


        $user = $this->users->find(1);
        $profile = $user->profile;
        $io->success("Профиль ленивый: {$profile->id} {$profile->userId} {$profile->login}");

        $pets = $user->pets;
        foreach ($pets as $pet) {
            $io->success("Питомец ленивый M2M: {$pet->id} {$pet->type}");
        }

        $houses = $user->houses;
        foreach ($houses as $house) {
            $io->success("Дом ленивый O2M: {$house->id} {$house->userId} {$house->address}");
        }

        $houseUser = $house->user;
        $io->success("Юзер дома ленивый M2O: {$houseUser->id} {$houseUser->name} {$houseUser->email}");

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

