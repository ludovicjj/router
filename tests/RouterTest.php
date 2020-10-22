<?php

namespace App\Test;

use App\Route;
use App\RouteAlreadyExistException;
use App\RouteNotFoundException;
use App\Router;
use App\Test\Classes\BarController;
use App\Test\Classes\BlogController;
use App\Test\Classes\FooController;
use App\Test\Classes\HomeController;
use PHPUnit\Framework\TestCase;
use ReflectionException;

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

    /**
     * @throws RouteAlreadyExistException
     * @throws RouteNotFoundException
     */
    public function testGetRouteByPathWithParameters(): void
    {
        $router = new Router();
        $route = new Route("article", "/blog/{id}/{slug}", function() {});
        $router->add($route);
        $this->assertEquals($route, $router->match("/blog/5/my-post"));
    }

    /**
     * @throws RouteAlreadyExistException
     * @throws RouteNotFoundException
     * @throws ReflectionException
     */
    public function testClosureContentWithParametersOrdered()
    {
        $router = new Router();
        $routePost = new Route("article", "/blog/{id}/{slug}", function(string $id, string $slug) {
            return sprintf('%s : %s', $id, $slug);
        });

        $router->add($routePost);

        $this->assertEquals("5 : my-post", $router->call("/blog/5/my-post"));
    }

    /**
     * @throws ReflectionException
     * @throws RouteAlreadyExistException
     * @throws RouteNotFoundException
     */
    public function testClosureContentWithParametersUnordered()
    {
        $router = new Router();
        $routePost = new Route("article", "/blog/{id}/{slug}", function(string $slug, string $id) {
            return sprintf('%s : %s', $id, $slug);
        });

        $router->add($routePost);

        $this->assertEquals("5 : my-post", $router->call("/blog/5/my-post"));
    }

    /**
     * @throws ReflectionException
     * @throws RouteAlreadyExistException
     * @throws RouteNotFoundException
     */
    public function testClosureContentWithNoParameters()
    {
        $router = new Router();
        $route = new Route("home", "/", function() {
            return "home page";
        });

        $router->add($route);

        $this->assertEquals("home page", $router->call("/"));
    }

    /**
     * @throws ReflectionException
     * @throws RouteAlreadyExistException
     * @throws RouteNotFoundException
     */
    public function testMethodContentNoParameters()
    {
        $router = new Router();
        $route = new Route("home", "/", [HomeController::class, 'index']);

        $router->add($route);

        $this->assertEquals("Hello world !", $router->call("/"));
    }

    /**
     * @throws ReflectionException
     * @throws RouteAlreadyExistException
     * @throws RouteNotFoundException
     */
    public function testMethodContentWithParametersOrdered()
    {
        $router = new Router();
        $route = new Route("bar", "/bar/{message}/{id}", [BarController::class, 'index']);
        $router->add($route);
        $this->assertEquals("test : 5", $router->call("/bar/test/5"));
    }

    /**
     * @throws ReflectionException
     * @throws RouteAlreadyExistException
     * @throws RouteNotFoundException
     */
    public function testMethodContentWithParametersUnordered()
    {
        $router = new Router();
        $route = new Route("foo", "/foo/{message}/{id}", [FooController::class, 'index']);

        $router->add($route);

        $this->assertEquals("test : 5", $router->call("/foo/test/5"));
    }

    public function testMethodContentWithDefaultParameter()
    {
        $router = new Router();

        $routeBlog = (new Route("blog", "/blog/{local}/page/{page}", [BlogController::class, 'index']))
            ->addDefaults(['page' => 1]);

        $router->add($routeBlog);
        $this->assertEquals("Current page is : 1", $router->call("/blog/fr/page"));
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