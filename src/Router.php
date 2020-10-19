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
     * @throws RouteAlreadyExistException
     * @return $this
     */
    public function add(Route $route): self
    {
        if ($this->has($route->getName())) {
            throw new RouteAlreadyExistException();
        }
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
     * @param string $path
     * @return Route
     * @throws RouteNotFoundException
     */
    public function match(string $path): Route
    {
        foreach ($this->routes as $route) {
            if ($route->test($path)) {
                return $route;
            }
        }
        throw new RouteNotFoundException();
    }

    /**
     * @param $name
     * @return bool
     */
    private function has($name): bool
    {
        return isset($this->routes[$name]);
    }
}