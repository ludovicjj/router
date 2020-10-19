<?php

namespace App\Test;

use App\Route;
use App\RouteNotFoundException;
use App\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
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
}