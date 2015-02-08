<?php
namespace Puppy\Route;

use Pimple\Container;

/**
 * Class RouteTest
 * @package Puppy\Puppy\Route
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class RouteTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     */
    public function testGetPattern()
    {
        $pattern = 'pattern';
        $controller = function () {
        };

        $route = new Route(new RoutePattern($pattern), $controller);
        $this->assertSame($pattern, $route->getPattern()->getUri());
    }

    /**
     *
     */
    public function testGetController()
    {
        $pattern = 'pattern';
        $controller = function () {
        };

        $route = new Route(new RoutePattern($pattern), $controller);
        $this->assertSame($controller, $route->getController());
    }

    /**
     *
     */
    public function testGetMatchesEmpty()
    {
        $pattern = 'pattern';
        $controller = function () {
        };

        $route = new Route(new RoutePattern($pattern), $controller);
        $this->assertSame(array(), $route->getMatches());
    }

    /**
     *
     */
    public function testGetMatchesSetted()
    {
        $pattern = 'pattern';
        $controller = function () {
        };
        $matches = array(1, 2, 3);

        $route = new Route(new RoutePattern($pattern), $controller);
        $route->setMatches($matches);
        $this->assertSame($matches, $route->getMatches());
    }

    /**
     *  Tests that the controller can retrieve the args if asked
     */
    public function testCall()
    {
        $pattern = 'pattern';
        $controller = function (array $args, Container $services) {
            return func_get_args();
        };
        $matches = array(1, 2, 3);
        $services = new Container();

        $route = new Route(new RoutePattern($pattern), $controller);
        $route->setMatches($matches);
        $this->assertSame(array($matches, $services), $route->call($services));
    }

    /**
     * Tests that no args is given to the controller, if the controller asked for no args
     */
    public function testCallArgsEmpty()
    {
        $pattern = 'pattern';
        $controller = function () {
            return func_get_args();
        };
        $matches = array(1, 2, 3);
        $services = new Container();

        $route = new Route(new RoutePattern($pattern), $controller);
        $route->setMatches($matches);
        $this->assertSame(array(), $route->call($services));
    }

    /**
     * Tests we can retrieve a specific service in the params of the controller
     */
    public function testCallWithServices()
    {
        $pattern = 'pattern';
        $controller = function (\stdClass $service1) {
            return func_get_args();
        };
        $matches = array(1, 2, 3);

        $services = new Container();
        $services['service1'] = function () {
            return new \stdClass();
        };

        $route = new Route(new RoutePattern($pattern), $controller);
        $route->setMatches($matches);
        $this->assertSame(array($services['service1']), $route->call($services));
    }


    /**
     * Tests that the services not asked are not instantiated
     */
    public function testCallWithServicesNotCalled()
    {
        $pattern = 'pattern';
        $controller = function () {
            return func_get_args();
        };

        $call = 0;

        $services = new Container();
        $services['service1'] = function () use (&$call) {
            return ++$call;
        };

        $route = new Route(new RoutePattern($pattern), $controller);
        $route->call($services);
        $this->assertSame(1, $services['service1']); ///service1 is called for the first time
    }

    /**
     *
     */
    public function test__toString()
    {
        $this->assertSame('route [METHOD] [content]', (string)$this->getRoute('route', 'method', 'content'));
    }

    /**
     *
     */
    public function test__toStringWithEmptyFilters()
    {
        $this->assertSame('route [*] [*]', (string)new Route(new RoutePattern('route'), function(){}));
    }

    /**
     * @param string $uri
     * @param string $method
     * @param string $contentType
     * @return Route
     */
    private function getRoute($uri, $method, $contentType)
    {
        $routePattern = new RoutePattern($uri);
        $routePattern->setMethod($method);
        $routePattern->setContentType($contentType);

        return new Route($routePattern, function(){});
    }
}
 