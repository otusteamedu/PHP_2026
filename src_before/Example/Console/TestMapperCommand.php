<?php

declare(strict_types=1);

namespace App\Example\Console;

use App\Example\Infrastucture\Entity\House;
use App\Example\Infrastucture\Entity\Pet;
use App\Example\Infrastucture\Entity\Profile;
use App\Example\Infrastucture\Entity\User;
use App\Example\Infrastucture\Entity\UserPet;
use App\Example\Infrastucture\Repository\HouseRepositoryInterface;
use App\Example\Infrastucture\Repository\PetRepository;
use App\Example\Infrastucture\Repository\ProfileRepository;
use App\Example\Infrastucture\Repository\UserPetRepository;
use App\Example\Infrastucture\Repository\UserRepository;
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
        private readonly HouseRepositoryInterface $houses,
        private readonly UserPetRepository $userPets,
    ) {
        parent::__construct();
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $user = new User(null, 'Petya', 'test@mail.ru');
        $this->users->save($user);

        $io->success("Сохранен юзер: {$user->id} {$user->name} {$user->email}");

        $profile = new Profile(null, 1, 'login-petya');
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
        $io->success("Сохранена связь питомца с юзером: {$userPet->id} {$userPet->userId} {$userPet->petId}");

        $userPet = new UserPet(null, 1, $pet2->id);
        $this->userPets->save($userPet);
        $io->success("Сохранена связь питомца с юзером: {$userPet->id} {$userPet->userId} {$userPet->petId}");

        $house = new House(null, 1, 'Санкт-Петербург, Литейный проспект 12');
        $this->houses->save($house);
        $io->success("Сохранен дом: {$house->id} {$house->userId} {$house->address}");

        $user = $this->users->find(1);
        $profile = $user->profile;
        $io->success("Профиль юзера с id=1 связь O2O: {$profile->id} {$profile->userId} {$profile->login}");

        $pets = $user->pets;
        foreach ($pets as $pet) {
            $io->success("Питомец юзера с id=1 связь M2M: {$pet->id} {$pet->type}");
        }

        $houses = $user->houses;
        foreach ($houses as $house) {
            $io->success("Дом юзера с id=1 связь O2M: {$house->id} {$house->userId} {$house->address}");
        }

        $houseUser = $house->user;
        $io->success("Юзер дома связь M2O: {$houseUser->id} {$houseUser->name} {$houseUser->email}");

        $user->name = 'Updated Petya';
        $this->users->save($user);

        $io->success("Обновлен юзер с id=1: {$user->id} {$user->name} {$user->email}");

        $users = $this->users->findAll();
        foreach ($users as $user) {
            $io->success("Найденный юзер в цикле: {$user->id} {$user->name} {$user->email}");
        }
        $io->success('Всего найдено юзеров: ' . count($users));

        $users = $this->users->findByEmail('test@mail.ru');
        foreach ($users as $user) {
            $io->success("Найденный по почте юзер в цикле: {$user->id} {$user->name} {$user->email}");
        }
        $io->success('Всего найдено юзеров по почте test@mail.ru: ' . count($users));

        return Command::SUCCESS;
    }
}

