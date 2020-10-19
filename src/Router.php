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
     * @throws RouteNotFoundException
     * @return Route
     */
    public function get(string $name): Route
    {
        if (!$this->has($name)) {
            throw new RouteNotFoundException();
        }
        return $this->routes[$name];
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name): bool
    {
        return isset($this->routes[$name]);
    }
}