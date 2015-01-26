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
        $this->assertSame('this/one', $routeFinder->find($request, $routes)->getPattern()->getUri());
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
        $this->assertSame('POST', $routeFinder->find($request, $routes)->getPattern()->getMethod());
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
            $routeFinder->find($request, $routes)->getPattern()->getContentType()
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
        $routeFinder->find($request, array());
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
            $routeFinder->find($request, $routes);
        } catch (RouteException $e) {
            $this->assertSame($request, $e->getRequest());
            $this->assertSame($routes, $e->getRoutes());
        }
    }
}
 