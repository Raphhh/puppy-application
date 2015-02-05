<?php
namespace Puppy\Route;

/**
 * Class IRoutePatternSetterAdapterTest
 * @package Route
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class IRoutePatternSetterAdapterTest extends \PHPUnit_Framework_TestCase
{

    public function testMethod()
    {
        $routePattern = new RoutePattern('uri');
        $adapter = new IRoutePatternSetterAdapter($routePattern);
        $adapter->method('method');
        $this->assertSame('METHOD', $routePattern->getMethod());
    }

    public function testContent()
    {
        $routePattern = new RoutePattern('uri');
        $adapter = new IRoutePatternSetterAdapter($routePattern);
        $adapter->content('contentType');
        $this->assertSame('contentType', $routePattern->getContentType());
    }

    public function testFilter()
    {
        $middleware = function () {
        };
        $routePattern = new RoutePattern('uri');
        $adapter = new IRoutePatternSetterAdapter($routePattern);
        $adapter->filter($middleware);
        $this->assertSame([$middleware], $routePattern->getFilters());
    }
}
