<?php

declare(strict_types=1);

namespace App\HTTP\Routing;

class Route
{
    private static array $routesGet = [];
    private static array $routesPost = [];
 
    /**
     * @return array
     */
    public static function getRoutesPost(): array
    {
        return self::$routesPost;
    }

    /**
     * @return array
     */
    public static function getRoutesGet(): array
    {
        return self::$routesGet;
    }

    /**
     * @param string $route
     * @param array $controller
     * @return RouteConfiguration
     */
    public static function get(string $route, array $controller): RouteConfiguration
    {
        return self::register($route, $controller, self::$routesGet);
    }

    /**
     * @param string $route
     * @param array $controller
     * @return RouteConfiguration
     */
    public static function post(string $route, array $controller): RouteConfiguration
    {
        return self::register($route, $controller, self::$routesPost);
    }

    /**
     * @param string $url
     * @return void
     */
    public static function redirect(string $url): void
    {
        header("Location: " . $url);
        exit;
    }

    /**
     * @param string $route
     * @param array $controller
     * @param $arrayRoutes
     * @return RouteConfiguration
     */
    private static function register(string $route, array $controller, array &$arrayRoutes): RouteConfiguration
    {
          if (count($controller) !== 2) {
            throw new \InvalidArgumentException('Должна быть следующая структура: [ControllerClass::class, method]');
        }

        $routeConfiguration = new RouteConfiguration($route, $controller[0], $controller[1]);
        $arrayRoutes[] = $routeConfiguration;
        return $routeConfiguration;
    }
}