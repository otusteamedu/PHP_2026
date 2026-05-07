<?php

namespace Database\Seeders;

use App\Models\Construct;
use App\Models\ConstructLink;
use App\Models\ConstructSnippet;
use App\Models\Language;
use Illuminate\Database\Seeder;

class ConstructsSeeder extends Seeder
{
    public function run(): void
    {
        $php = Language::query()->firstOrCreate(
            ['code' => 'php'],
            ['name' => 'PHP']
        );
        $go = Language::query()->firstOrCreate(
            ['code' => 'go'],
            ['name' => 'Go']
        );

        $this->seedPhp($php->id);
        $this->seedGo($go->id);
    }

    private function seedPhp(int $languageId): void
    {
        $foreach = Construct::query()->updateOrCreate(
            ['language_id' => $languageId, 'slug' => 'foreach'],
            [
                'title' => 'foreach',
                'summary' => 'Цикл по массиву и объектам Traversable.',
                'details' => 'Удобен для итерации по коллекциям. Можно получать ключ и значение.',
            ]
        );
        ConstructSnippet::query()->updateOrCreate(
            ['construct_id' => $foreach->id, 'sort' => 0],
            [
                'title' => 'Пример',
                'code' => <<<'PHP'
<?php
$arr = [10, 20, 30];
foreach ($arr as $i => $v) {
    echo "$i => $v\n";
}
PHP,
            ]
        );
        ConstructLink::query()->updateOrCreate(
            ['construct_id' => $foreach->id, 'sort' => 0],
            [
                'title' => 'PHP manual',
                'url' => 'https://www.php.net/manual/en/control-structures.foreach.php',
            ]
        );

        $switch = Construct::query()->updateOrCreate(
            ['language_id' => $languageId, 'slug' => 'switch'],
            [
                'title' => 'switch',
                'summary' => 'Множественный выбор по значению выражения.',
                'details' => 'Сравнение нестрогое (==). Для строгого сравнения используют match.',
            ]
        );
        ConstructSnippet::query()->updateOrCreate(
            ['construct_id' => $switch->id, 'sort' => 0],
            [
                'title' => 'Пример',
                'code' => <<<'PHP'
<?php
$x = 2;
switch ($x) {
    case 1:
        echo "one\n";
        break;
    case 2:
        echo "two\n";
        break;
    default:
        echo "other\n";
}
PHP,
            ]
        );
        ConstructLink::query()->updateOrCreate(
            ['construct_id' => $switch->id, 'sort' => 0],
            [
                'title' => 'PHP manual',
                'url' => 'https://www.php.net/manual/en/control-structures.switch.php',
            ]
        );

        $tryCatch = Construct::query()->updateOrCreate(
            ['language_id' => $languageId, 'slug' => 'try-catch'],
            [
                'title' => 'try / catch',
                'summary' => 'Обработка исключений.',
                'details' => 'Используется с Exception/Throwable. Можно ловить разные типы исключений.',
            ]
        );
        ConstructSnippet::query()->updateOrCreate(
            ['construct_id' => $tryCatch->id, 'sort' => 0],
            [
                'title' => 'Пример',
                'code' => <<<'PHP'
<?php
try {
    throw new RuntimeException('boom');
} catch (RuntimeException $e) {
    echo $e->getMessage();
}
PHP,
            ]
        );
        ConstructLink::query()->updateOrCreate(
            ['construct_id' => $tryCatch->id, 'sort' => 0],
            [
                'title' => 'PHP manual',
                'url' => 'https://www.php.net/manual/en/language.exceptions.php',
            ]
        );
    }

    private function seedGo(int $languageId): void
    {
        $goroutine = Construct::query()->updateOrCreate(
            ['language_id' => $languageId, 'slug' => 'goroutine'],
            [
                'title' => 'goroutine',
                'summary' => 'Лёгкий поток выполнения: запуск функции через go.',
                'details' => 'Обычно используется вместе с каналами и sync примитивами.',
            ]
        );
        ConstructSnippet::query()->updateOrCreate(
            ['construct_id' => $goroutine->id, 'sort' => 0],
            [
                'title' => 'Пример',
                'code' => <<<'GO'
package main

import "fmt"

func main() {
    done := make(chan struct{})

    go func() {
        fmt.Println("work")
        close(done)
    }()

    <-done
}
GO,
            ]
        );
        ConstructLink::query()->updateOrCreate(
            ['construct_id' => $goroutine->id, 'sort' => 0],
            [
                'title' => 'Go Tour',
                'url' => 'https://go.dev/tour/concurrency/1',
            ]
        );

        $defer = Construct::query()->updateOrCreate(
            ['language_id' => $languageId, 'slug' => 'defer'],
            [
                'title' => 'defer',
                'summary' => 'Отложенный вызов функции до выхода из текущей функции.',
                'details' => 'Полезно для закрытия ресурсов. Вызовы defer выполняются в LIFO порядке.',
            ]
        );
        ConstructSnippet::query()->updateOrCreate(
            ['construct_id' => $defer->id, 'sort' => 0],
            [
                'title' => 'Пример',
                'code' => <<<'GO'
package main

import "fmt"

func main() {
    defer fmt.Println("world")
    fmt.Println("hello")
}
GO,
            ]
        );
        ConstructLink::query()->updateOrCreate(
            ['construct_id' => $defer->id, 'sort' => 0],
            [
                'title' => 'Go Tour',
                'url' => 'https://go.dev/tour/flowcontrol/12',
            ]
        );
    }
}
