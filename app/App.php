<?php

namespace App;

use App\Controllers\StringValidationController;

class App
{
    public function run()
    {
        $controller = new StringValidationController();
        return $controller->handleRequest();
    }
}
