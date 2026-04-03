<?php

$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->selectDatabase("test");

$dm = new DocumentManager($db);
$users = new UserRepository($dm);

// INSERT
$user = new UserDocument(username: "Stanislav");
$users->save($user);

echo "Создан документ с ID: $user->id\n";

// SELECT
$u = $users->find($user->id);
print_r($u);

// ALL
$list = $users->all();
print_r($list);
