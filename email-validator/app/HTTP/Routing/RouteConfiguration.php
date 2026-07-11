<?php

declare(strict_types=1);

namespace App\HTTP\Routing;

class RouteConfiguration
{
    public string $route;
    public string $controller;
    public string $action;
    protected string $name = '';
    protected string $middleware = '';

    /**
     * @param string $route
     * @param string $controller
     * @param string $action
     */
    public function __construct(string $route, string $controller, string $action)
    {
        $this->route = $route;
        $this->controller = $controller;
        $this->action = $action;
    }

    /**
     * @param string $name
     * @return RouteConfiguration
     */
    public function name(string $name): RouteConfiguration
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $middleware
     * @return RouteConfiguration
     */
    public function middleware(string $middleware): RouteConfiguration
    {
        $this->middleware = $middleware;
        return $this;
    }
}