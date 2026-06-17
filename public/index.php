<?php

use App\DB\Database;
use App\Mappers\PostMapper;
use App\Mappers\UserMapper;
use App\Models\Post;
use App\Models\User;

require __DIR__ . '/../vendor/autoload.php';

$database = new Database("mysql:host=mysql-8.0;dbname=hw12;port=3306", 'root', '');

$userMapper = new UserMapper($database->getConnection());
$postMapper = new PostMapper($database->getConnection());

// Создадим пользователей
$userMapper->save(new User(null, 'first_name_1', 'last_name_1'));
$userMapper->save(new User(null, 'first_name_2', 'last_name_2'));
$userMapper->save(new User(null, 'first_name_3', 'last_name_3'));

// Выгрузка всех users
$users = $userMapper->findAll();
var_dump($users);

// Выгрузка пользователя с id 1. Пользователь будет получен из $identityMap, т.к до этого уже были выгружены все пользователи.
$user = $userMapper->findById(1);
var_dump($user);

// Создание поста
$postMapper->save(new Post(null, $user->getId(), 'Контент 1', date('Y-m-d H:i:s'), date('Y-m-d H:i:s')));
$postMapper->save(new Post(null, $user->getId(), 'Контент 2', date('Y-m-d H:i:s'), date('Y-m-d H:i:s')));

//Создадим еще пользователей
$userMapper->save(new User(null, 'first_name_1', 'last_name_1'));
$userMapper->save(new User(null, 'first_name_2', 'last_name_2'));
$userMapper->save(new User(null, 'first_name_3', 'last_name_3'));

// Загрузка постов пользователя.
$posts = $user->getPosts($postMapper);
var_dump($posts);

// Удаляем пользователя
$userMapper->delete($user);

// Выгружаем все посты (их нет, т.к пользователь удален и каскадно удалены посты)
$posts = $postMapper->findAll();
var_dump($posts);

$users = $userMapper->paginate();
var_dump($users);

$users = $userMapper->paginate(2, 2);
var_dump($users);
