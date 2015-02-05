<?php
namespace Puppy\Route;

use Puppy\resources\RequestMock;

/**
 * Class RouteFinderTest
 * @package Puppy\Route
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class RouteFinderTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     */
    public function testFind()
    {
        $routes = array(
            new Route(new RoutePattern('this/other'), function () {
            }),
            new Route(new RoutePattern('this/one'), function () {
            }),
        );

        $request = new RequestMock();
        $request->setRequestUri('this/one');

        $routeFinder = new RouteFinder();
        $this->assertSame('this/one', $routeFinder->find($request, $routes, new \ArrayObject())->getPattern()->getUri());
    }

    public function testFindWithMethod()
    {
        $routePattern1 = new RoutePattern('this/one');
        $routePattern1->setMethod('GET');

        $routePattern2 = new RoutePattern('this/one');
        $routePattern2->setMethod('POST');

        $routes = array(
            new Route($routePattern1, function () {
            }),
            new Route($routePattern2, function () {
            }),
        );

        $request = new RequestMock();
        $request->setRequestUri('this/one');
        $request->setMethod('POST');

        $routeFinder = new RouteFinder();
        $this->assertSame('POST', $routeFinder->find($request, $routes, new \ArrayObject())->getPattern()->getMethod());
    }

    /**
     *
     */
    public function testFindWithAcceptableContentType()
    {
        $routePattern1 = new RoutePattern('this/one');
        $routePattern1->setContentType('text/html');

        $routePattern2 = new RoutePattern('this/one');
        $routePattern2->setContentType('application/json');

        $routes = array(
            new Route($routePattern1, function () {
            }),
            new Route($routePattern2, function () {
            }),
        );

        $request = new RequestMock();
        $request->setRequestUri('this/one');
        $request->setAcceptableContentTypes(array('application/json'));

        $routeFinder = new RouteFinder();
        $this->assertSame(
            'application/json',
            $routeFinder->find($request, $routes, new \ArrayObject())->getPattern()->getContentType()
        );
    }

    /**
     *
     */
    public function testFindWithException()
    {
        $request = new RequestMock();
        $request->setRequestUri('this/one');

        $routeFinder = new RouteFinder();

        $this->setExpectedException('Puppy\Route\RouteException', 'No route found for uri "this/one"');
        $routeFinder->find($request, array(), new \ArrayObject());
    }

    /**
     *
     */
    public function testFindWithExceptionValues()
    {
        $request = new RequestMock();
        $request->setRequestUri('this/one');

        $routes = array();

        try {
            $routeFinder = new RouteFinder();
            $routeFinder->find($request, $routes, new \ArrayObject());
        } catch (RouteException $e) {
            $this->assertSame($request, $e->getRequest());
            $this->assertSame($routes, $e->getRoutes());
        }
    }

    public function testFindWithFilter()
    {
        $routePattern1 = new RoutePattern('this/one');
        $routePattern1->addFilter(
            function () {
                return false;
            }
        );

        $routePattern2 = new RoutePattern('this/one');
        $routePattern2->addFilter(
            function () {
                return true;
            }
        );
        $routePattern2->addFilter(
            function () {
                return false;
            }
        );

        $routePattern3 = new RoutePattern('this/one');
        $routePattern3->addFilter(
            function () {
                return true;
            }
        );

        $routePattern4 = new RoutePattern('this/one');
        $routePattern4->addFilter(
            function () {
                return false;
            }
        );

        $routes = array(
            new Route(
                $routePattern1, function () {
                return false;
            }
            ),
            new Route(
                $routePattern2, function () {
                return false;
            }
            ),
            new Route(
                $routePattern3, function () {
                return true;
            }
            ),
            new Route(
                $routePattern4, function () {
                return false;
            }
            ),
        );

        $request = new RequestMock();
        $request->setRequestUri('this/one');

        $routeFinder = new RouteFinder();
        $controller = $routeFinder->find($request, $routes, new \ArrayObject())->getController();
        $this->assertTrue($controller());
    }

    public function testFindWithFilterWithDynamicParams()
    {
        $routePattern1 = new RoutePattern('this/one');
        $routePattern1->addFilter(
            function ($result) {
                return $result;
            }
        );


        $routes = array(
            new Route(
                $routePattern1, function () {
                return true;
            }
            ),
        );

        $request = new RequestMock();
        $request->setRequestUri('this/one');

        $routeFinder = new RouteFinder();
        $controller = $routeFinder->find($request, $routes, new \ArrayObject(['result' => true]))->getController();
        $this->assertTrue($controller());
    }
}
 