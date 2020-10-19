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
    public function testGetRoute(): void
    {
        $router = new Router();
        $route = new Route("home", "/", function() {
            echo 'hello world';
        });
        $router->add($route);
        $this->assertEquals($route, $router->get('home'));
    }

    public function testRouteNotFoundException(): void
    {
        $router = new Router();
        $this->expectException(RouteNotFoundException::class);
        $this->assertNull($router->get('contact'));
    }

    public function testRouteAlreadyExistException()
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