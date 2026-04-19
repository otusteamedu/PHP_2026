<?php

declare(strict_types=1);

namespace App;

use App\Container\Container;
use App\Observer\ClientNotifier;
use App\Observer\CookingNotifier;
use App\Observer\CookingSubjectInterface;
use App\Observer\KitchenLogger;
use App\Proxy\CookInterface;
use App\Proxy\QualityControlCookProxy;

final class Kernel
{
    public static function boot(): Container
    {
        $container = new Container();

        $container->bind(CookingSubjectInterface::class, CookingNotifier::class);
        $container->bind(CookInterface::class, QualityControlCookProxy::class);

        /** @var CookingSubjectInterface $notifier */
        $notifier = $container->get(CookingSubjectInterface::class);
        $notifier->attach($container->get(ClientNotifier::class));
        $notifier->attach($container->get(KitchenLogger::class));

        return $container;
    }
}