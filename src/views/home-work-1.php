<?php

use App\Lessons\Hw1\CheckDatabaseConnection;
use App\Lessons\Hw1\CheckMemcached;
use App\Lessons\Hw1\CheckRedis;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.tailwindcss.com"></script>
    <title><?= htmlspecialchars($pageTitle ?? 'Home work 1') ?></title>
</head>
<body>
    <div class="max-w-5xl mx-auto space-y-6 my-16">
        <section class="bg-gray-50 rounded-md h-16">
            <h1 class="text-lg font-bold">Database: <?= htmlspecialchars(CheckDatabaseConnection::execute()) ?></h1>
        </section>

        <section class="bg-gray-50 rounded-md h-16">
            <h1 class="text-lg font-bold">Redis: <?= htmlspecialchars(CheckRedis::execute()) ?></h1>
        </section>

        <section class="bg-gray-50 rounded-md h-16">
            <h1 class="text-lg font-bold">Memcached: <?= htmlspecialchars(CheckMemcached::execute()) ?></h1>
        </section>

        <?= phpinfo() ?>
    </div>
</body>
</html>
