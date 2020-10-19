<?php


namespace App;


class Router
{
    /**
     * @var Route[]
     */
    private $routes = [];

    /**
     * @return Route[]
     */
    public function getRouteCollection(): array
    {
        return $this->routes;
    }

    /**
     * @param Route $route
     * @return $this
     */
    public function add(Route $route): self
    {
        $this->routes[$route->getName()] = $route;

        return $this;
    }

    /**
     * @param string $name
     * @return Route|null
     */
    public function get(string $name): ?Route
    {
        foreach ($this->routes as $route) {
            if ($route->getName() === $name) {
                return $route;
            }
        }
        return null;
    }
}