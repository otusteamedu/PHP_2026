<?php

declare (strict_types = 1);

namespace App;

use App\Domain\Cinema;
use App\Domain\Hall;

class Kernel
{
    public function run()
    {
        $uri = $_SERVER['REQUEST_URI'];

        if ($uri === '/cinemas') {
            $cinemas = Cinema::all();

            echo "<h1>Список кинотеатров</h1>";

            foreach ($cinemas as $cinema) {
                echo "<p>ID: {$cinema->id}</p>";
                echo "<p>Name: {$cinema->name}</p>";
                echo "<hr>";
            }

            return;
        }

        if ($uri === '/cinema') {
            $cinema = Cinema::find(1);

            echo "<h1>{$cinema->name}</h1>";

            echo "<h2>Залы (Lazy Load)</h2>";

            foreach ($cinema->halls as $hall) {
                echo "<p>{$hall->name}</p>";
            }

            return;
        }

        if ($uri === '/hall') {
            $hall = Hall::find(1);

            echo "<h1>Информация о зале</h1>";
            echo "<p>Название зала: {$hall->name}</p>";

            echo "<h2>Кинотеатр (belongsTo)</h2>";
            echo "<p>{$hall->cinema->name}</p>";

            return;
        }

        echo "404";
    }
}
