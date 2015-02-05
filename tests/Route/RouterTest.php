<?php
namespace Puppy\Route;

use Puppy\resources\RequestMock;

/**
 * Class RouterTest
 * @package Puppy\Route
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class RouterTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     */
    public function testFind()
    {
        $finderResult = 'finder_result';

        $request = new RequestMock();
        $request->setRequestUri('uri');

        $route1 = new Route(new RoutePattern('pattern'), function () {
        });
        $route1->getPattern()->setMethod('post');

        $route2 = new Route(new RoutePattern('pattern'), function () {
        });

        $route3 = new Route(new RoutePattern('pattern'), function () {
        });
        $route3->getPattern()->setContentType('application/json');

        $routes = [$route1, $route2, $route3];

        $router = new Router($this->getRouteFinder($finderResult, $request, $routes));
        $router->addRoute($route1);
        $router->addRoute($route2);
        $router->addRoute($route3);
        $this->assertSame($finderResult, $router->find($request, new \ArrayObject()));
    }

    /**
     * @param mixed $finderResult
     * @param string $uri
     * @param array $routes
     * @return RouteFinder
     */
    private function getRouteFinder($finderResult, $uri, $routes)
    {
        $routeFinder = $this->getMock('Puppy\Route\RouteFinder');

        $routeFinder->expects($this->once())
            ->method('find')
            ->with($uri, $routes)
            ->will($this->returnValue($finderResult));

        return $routeFinder;
    }

    /**
     *
     */
    public function testGetCurrentRoute()
    {
        $request = new RequestMock();
        $request->setRequestUri('uri');

        $route = new Route(new RoutePattern('uri'), function () {});

        $router = new Router(new RouteFinder());
        $router->addRoute($route);

        $this->assertNull($router->getCurrentRoute());
        $router->find($request, new \ArrayObject());
        $this->assertSame($route, $router->getCurrentRoute());
    }
}
 