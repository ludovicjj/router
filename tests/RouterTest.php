<?php

namespace App\Test;

use App\Route;
use App\RouteAlreadyExistException;
use App\RouteNotFoundException;
use App\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    /**
     * @throws RouteAlreadyExistException
     */
    public function testRouteCollection(): void
    {
        $router = new Router();
        $route = new Route("home", "/", function() {
            echo 'hello world';
        });

        $router->add($route);

        $this->assertCount(1, $router->getRouteCollection());
        $this->assertContainsOnlyInstancesOf(Route::class, $router->getRouteCollection());
    }

    /**
     * @throws RouteNotFoundException
     * @throws RouteAlreadyExistException
     */
    public function testGetRouteByName(): void
    {
        $router = new Router();
        $route = new Route("home", "/", function() {
            echo 'hello world';
        });
        $router->add($route);
        $this->assertEquals($route, $router->get('home'));
    }

    /**
     * @throws RouteAlreadyExistException
     * @throws RouteNotFoundException
     */
    public function testGetRouteByPath(): void
    {
        $router = new Router();
        $route = new Route("home", "/", function() {});
        $router->add($route);
        $this->assertEquals($route, $router->match("/"));
    }

    public function testRouteNotFoundByGet(): void
    {
        $router = new Router();
        $this->expectException(RouteNotFoundException::class);
        $router->get('contact');
    }

    public function testRouteNotFoundByMatch(): void
    {
        $router = new Router();
        $this->expectException(RouteNotFoundException::class);
        $router->match("/");
    }

    /**
     * @throws RouteAlreadyExistException
     */
    public function testRouteAlreadyExist(): void
    {
        $router = new Router();
        $route = new Route("home", "/", function() {
            echo 'hello world';
        });
        $router->add($route);
        $this->expectException(RouteAlreadyExistException::class);
        $router->add($route);
    }
}