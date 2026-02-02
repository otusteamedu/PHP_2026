<?php

declare(strict_types=1);

namespace App\Lessons\Hw1;

use Memcached;

class CheckMemcached
{
    public static function execute(): string
    {
        $memcached = new Memcached();
        $memcached->addServer('memcached', 11211);

        $memcached->set('test_key', 'test!', 10);

        $value = $memcached->get('test_key');

        if ($value) {
            return 'Memcached работает! Значение: ' . $value;
        } else {
            return 'Ошибка: ' . $memcached->getResultMessage();
        }
    }
}
