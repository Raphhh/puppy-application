<?php
namespace Puppy\Controller;

use Pimple\Container;
use Puppy\Route\Route;
use Puppy\Route\RouteFinder;
use Puppy\Route\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

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
            return $this;
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
        $this->assertEquals($controller, $routes[0]->getController());
        $controller = $routes[0]->getController();
        $this->assertInstanceOf('Puppy\Controller\AppController', $controller());
        $this->assertSame('GET', $routes[0]->getPattern()->getMethod());
        $this->assertSame('application/json', $routes[0]->getPattern()->getContentType());
    }

    /**
     *
     */
    public function testCallWithStringResult()
    {
        $services = new Container();

        $request = new Request();

        $router = $this->getRouter($request, $services, 'route_call_result');
        $services['router'] = function () use ($router) {
            return $router;
        };

        $services['appController'] = function () {
            return new AppController();
        };

        $services['requestStack'] = function () {
            return new RequestStack();
        };

        $frontController = new FrontController($services);
        $this->assertEquals('route_call_result', $frontController->call($request)->getContent());
    }

    public function testCallWithResponseResult()
    {
        $services = new Container();

        $request = new Request();

        $router = $this->getRouter($request, $services, new Response('route_call_result'));
        $services['router'] = function () use ($router) {
            return $router;
        };

        $services['appController'] = function () {
            return new AppController();
        };

        $services['requestStack'] = function () {
            return new RequestStack();
        };

        $frontController = new FrontController($services);
        $this->assertEquals(new Response('route_call_result'), $frontController->call($request));
    }

    /**
     * @param Request $request
     * @param Container $services
     * @param $routeResult
     * @return Router
     */
    private function getRouter(Request $request, Container $services, $routeResult)
    {
        $router = $this->getMockBuilder('Puppy\Route\Router')
            ->disableOriginalConstructor()
            ->setMethods(array('find'))
            ->getMock();

        $router->expects($this->any())
            ->method('find')
            ->will($this->returnValue($this->getRoute($services, $routeResult)));

        $router->expects($this->once())
            ->method('find')
            ->with($request);

        return $router;
    }

    /**
     * @param \Pimple\Container $services
     * @param mixed $routeResult
     * @return Route
     */
    private function getRoute(Container $services, $routeResult)
    {
        $route = $this->getMockBuilder('Puppy\Route\Route')
            ->disableOriginalConstructor()
            ->getMock();

        $route->expects($this->any())
            ->method('call')
            ->will($this->returnValue($routeResult));

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
        $services['router'] = function () {
            return new Router(New RouteFinder());
        };
        $services['appController'] = function () {
            return new AppController();
        };
        $services['requestStack'] = function () {
            return new RequestStack();
        };
        return $services;
    }

    public function testAddControllerReturn()
    {
        $services = $this->getServices();
        $frontController = new FrontController($services);
        $result = $frontController->addController(
            'uri',
            function () {
            }
        );
        $this->assertSame($services['router']->getRoutes()[0], $result);
    }

    public function testSetRequestFormat()
    {
        $request = new Request();
        $request->headers->set('Accept', 'text/html,application/xhtml+xml,application/xml');

        $this->assertSame(
            [
                'text/html',
                'application/xhtml+xml',
                'application/xml',
            ],
            $request->getAcceptableContentTypes()
        );



        $services = $this->getServices();
        $frontController = new FrontController($services);
        $frontController->addController('', function(){});

        $this->assertSame('', $request->getRequestFormat(''));
        $frontController->call($request);
        $this->assertSame('html', $request->getRequestFormat(''));
    }
}
 