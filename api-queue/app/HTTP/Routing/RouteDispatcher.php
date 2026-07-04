<?php

declare(strict_types=1);

namespace App\HTTP\Routing;

use Psr\Container\ContainerInterface;

class RouteDispatcher
{
    private string $requestUri = '/';
    private array $paramMap = [];
    private array $paramRequestMap = [];
    private RouteConfiguration $routeConfiguration;   
  
    private ContainerInterface $container;

    // ДОБАВЛЕНО: Передаем контейнер в конструктор
    public function __construct(RouteConfiguration $routeConfiguration, ContainerInterface $container)
    {
       $this->routeConfiguration = $routeConfiguration;
       $this->container = $container;
    }

    public function process(): void
    {
        $this->saveRequestUri();
        $this->setParamMap();
        $this->makeRegexRequest();
        $this->run();
    }

    private function saveRequestUri(): void
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

        $this->requestUri = $this->clean($path);

        $this->routeConfiguration->route = $this->clean($this->routeConfiguration->route);
    }

    private function clean(string $value): string
    {
        return trim($value, '/');
    }

    private function setParamMap(): void
    {
        foreach (explode('/', $this->routeConfiguration->route) as $index => $part) {

            if (preg_match('/^{(.+)}$/', $part, $matches)) {
                $this->paramMap[$index] = $matches[1];
            }
        }
    }

    private function makeRegexRequest(): void
    {
        $segments = explode('/', $this->requestUri);

        foreach ($this->paramMap as $index => $name) {
            if (!isset($segments[$index])) {
                return;
            }

            $this->paramRequestMap[$name] = $segments[$index];
            $segments[$index] = '{param}';
        }
      
        $this->requestUri = preg_quote(implode('/', $segments), '#');
        $this->requestUri = str_replace('\{param\}', '[^/]+', $this->requestUri);
    }

    private function run(): bool
    {
        if (!preg_match("#^{$this->requestUri}$#", $this->routeConfiguration->route)) {
            return false;
        }

        $this->render();

        return true;
    }

    private function render(): void
    {
        $controller = $this->container->get(
            $this->routeConfiguration->controller
        );

        $method = $this->routeConfiguration->action;

        $controller->$method(
            ...array_values($this->paramRequestMap)
        );

        die();
    }
}