<?php
namespace Puppy\Controller;

use Pimple\Container;
use Puppy\Http\ResponseAdapter;
use Puppy\Route\Route;
use Puppy\Route\RouteFinder;
use Puppy\Route\Router;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class FrontControllerTest
 * @package Puppy
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class FrontControllerTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     */
    public function testAddController()
    {
        $controller = function () {
        };

        $services = $this->getServices();

        $frontController = new FrontController($services);
        $frontController->addController('pattern', $controller, 'get', 'application/json');
        $this->assertRouter($services['router'], $controller);
    }

    /**
     * @param Router $router
     * @param callable $controller
     */
    private function assertRouter(Router $router, callable $controller)
    {
        $routes = $router->getRoutes();
        $this->assertArrayHasKey(0, $routes);
        $this->assertSame($controller, $routes[0]->getController());
        $this->assertSame('GET', $routes[0]->getPattern()->getMethod());
        $this->assertSame('application/json', $routes[0]->getPattern()->getContentType());
    }

    /**
     *
     */
    public function testAddCall()
    {
        $services = new Container();

        $request = new Request();
        $services['request'] = function () use($request){
            return $request;
        };

        $router = $this->getRouter($request, $services);
        $services['router'] = function () use ($router) {
            return $router;
        };

        $frontController = new FrontController($services);
        $this->assertEquals(new ResponseAdapter('route_call_result'), $frontController->call());
    }

    /**
     * @param Request $request
     * @param Container $services
     * @return Router
     */
    private function getRouter(Request $request, Container $services)
    {
        $router = $this->getMockBuilder('Puppy\Route\Router')
            ->disableOriginalConstructor()
            ->setMethods(array('find'))
            ->getMock();

        $router->expects($this->any())
            ->method('find')
            ->will($this->returnValue($this->getRoute($services)));

        $router->expects($this->once())
            ->method('find')
            ->with($request);

        return $router;
    }

    /**
     * @param \Pimple\Container $services
     * @return Route
     */
    private function getRoute(Container $services)
    {
        $route = $this->getMockBuilder('Puppy\Route\Route')
            ->disableOriginalConstructor()
            ->getMock();

        $route->expects($this->any())
            ->method('call')
            ->will($this->returnValue('route_call_result'));

        $route->expects($this->once())
            ->method('call')
            ->with($services);

        return $route;
    }

    /**
     * @return Container
     */
    private function getServices(){
        $services = new Container();
        $services['request'] = function () {
            return new Request();
        };
        $services['router'] = function () {
            return new Router(New RouteFinder());
        };
        return $services;
    }


    public function testAddAppControllerToServices()
    {
        $services = $this->getServices();
        new FrontController($services);
        $this->assertArrayHasKey('appController', $services);
        $this->assertInstanceOf('Puppy\Controller\AppController', $services['appController']);
    }
}
 