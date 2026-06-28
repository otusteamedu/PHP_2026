<?php

declare(strict_types=1);

namespace App;

use DI\ContainerBuilder;
use App\HTTP\Controllers\EmailController;

class Kernel
{
    public function run(): void
    {       
        $builder = new ContainerBuilder();        
     
        $builder->addDefinitions(BASE_PATH . '/config/container.php');        
       
        $container = $builder->build();
       
        $controller = $container->get(EmailController::class);

        match ($_SERVER['REQUEST_METHOD']) {
            'GET'  => $controller->index(),
            'POST' => $controller->check(),
            default => http_response_code(405),
        };
    }
}
