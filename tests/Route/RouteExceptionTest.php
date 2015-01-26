<?php
namespace Puppy\Route;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class RouteExceptionTest
 * @package Puppy\Route
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class RouteExceptionTest extends \PHPUnit_Framework_TestCase
{

    public function testGetMessageWithEmptyRoutes()
    {
        $exception = new RouteException('message', new Request(), []);
        $this->assertSame('message', $exception->getMessage());
    }

    public function testGetMessageWithRoutes()
    {
        $exception = new RouteException('message', new Request(), [
            $this->getRoute('route1', 'method1', 'content1'),
            $this->getRoute('route2', 'method2', 'content2'),
        ]);

        $this->assertSame(
            "message\nPossible routes:\nroute1 [METHOD1] [content1]\nroute2 [METHOD2] [content2]",
            $exception->getMessage()
        );
    }

    private function getRoute($uri, $method, $contentType)
    {
        $routePattern = new RoutePattern($uri);
        $routePattern->setMethod($method);
        $routePattern->setContentType($contentType);

        return new Route($routePattern, function(){});
    }
}
 